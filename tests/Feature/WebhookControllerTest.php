<?php

namespace Tests\Feature;

use App\Jobs\Webhooks\HandlePingWebhookJob;
use App\Jobs\Webhooks\HandlePushWebhookJob;
use App\Models\Project;
use App\Models\VcsProvider;
use App\Models\Webhook;
use App\Models\WebhookCall;
use App\Support\Arr;
use App\Support\Str;
use Illuminate\Support\Facades\Bus;
use Tests\AbstractFeatureTest;

class WebhookControllerTest extends AbstractFeatureTest
{
    public function test_valid_ping_webhook_call_gets_accepted()
    {
        $hook = $this->webhook();

        $data = $this->pingData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret),
        ]);

        Bus::fake();

        $response = $this->postJson(
            'hook/github/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('webhook_calls', [
            'webhook_id' => $hook->id,
            'type' => 'ping',
            'url' => 'http://localhost/hook/github/' . $hook->uuid,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(1, $hook->calls);

        /** @var WebhookCall $call */
        $call = $hook->calls->first();

        $this->assertEquals('ping', $call->type);
        $this->assertFalse($call->processed);
        $this->assertEquals('804ed210-24e6-11ec-8f0d-307dea89cdae', $call->externalId);
        $this->assertEquals($data, $call->payload);

        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $call->headers[Str::lower($key)][0]);
        }

        Bus::assertDispatched(HandlePingWebhookJob::class);
    }

    public function test_invalid_provider_is_caught()
    {
        $hook = $this->webhook();

        $data = $this->pingData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret),
        ]);

        Bus::fake();

        $response = $this->postJson(
            'hook/gitlab/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(404);

        $this->assertDatabaseMissing('webhook_calls', [
            'webhook_id' => $hook->id,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(0, $hook->calls);

        Bus::assertNotDispatched(HandlePingWebhookJob::class);
    }

    public function test_invalid_event_is_caught()
    {
        $hook = $this->webhook();

        $data = $this->pingData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret),
            'X-GitHub-Event' => 'foobar',
        ]);

        Bus::fake();

        $response = $this->postJson(
            'hook/github/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(400);

        $this->assertDatabaseMissing('webhook_calls', [
            'webhook_id' => $hook->id,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(0, $hook->calls);

        Bus::assertNotDispatched(HandlePingWebhookJob::class);
    }

    public function test_invalid_id_is_caught()
    {
        $hook = $this->webhook();

        $data = $this->pingData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret),
        ]);

        Arr::forget($headers, 'X-GitHub-Delivery');

        Bus::fake();

        $response = $this->postJson(
            'hook/github/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(400);

        $this->assertDatabaseMissing('webhook_calls', [
            'webhook_id' => $hook->id,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(0, $hook->calls);

        Bus::assertNotDispatched(HandlePingWebhookJob::class);
    }

    public function test_invalid_signature_is_caught()
    {
        $hook = $this->webhook();

        $data = $this->pingData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret, true),
        ]);

        Bus::fake();

        $response = $this->postJson(
            'hook/github/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(403);

        $this->assertDatabaseMissing('webhook_calls', [
            'webhook_id' => $hook->id,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(0, $hook->calls);

        Bus::assertNotDispatched(HandlePingWebhookJob::class);
    }

    public function test_push_webhook_call_gets_accepted()
    {
        $hook = $this->webhook();

        $data = $this->pushData();

        $headers = $this->headers([
            'X-Hub-Signature-256' => $this->signature($data, $hook->secret),
            'X-GitHub-Event' => 'push',
        ]);

        Bus::fake();

        $response = $this->postJson(
            'hook/github/' . $hook->uuid,
            $data,
            $headers,
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('webhook_calls', [
            'webhook_id' => $hook->id,
            'type' => 'push',
            'url' => 'http://localhost/hook/github/' . $hook->uuid,
            'external_id' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
        ]);

        $hook->refresh();

        $this->assertCount(1, $hook->calls);

        /** @var WebhookCall $call */
        $call = $hook->calls->first();

        $this->assertEquals('push', $call->type);
        $this->assertFalse($call->processed);
        $this->assertEquals('804ed210-24e6-11ec-8f0d-307dea89cdae', $call->externalId);
        $this->assertEquals($data, $call->payload);

        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $call->headers[Str::lower($key)][0]);
        }

        Bus::assertDispatched(HandlePushWebhookJob::class);
    }

    protected function webhook(): Webhook
    {
        /** @var VcsProvider $vcs */
        $vcs = VcsProvider::factory(['provider' => 'github_v3'])->withUser()->create();

        /** @var Project $project */
        $project = Project::factory()
            ->for($vcs->user)
            ->for($vcs)
            ->repoInstalled()
            ->create();

        /** @var Webhook $hook */
        $hook = Webhook::factory([
            'provider' => 'github',
            'external_id' => '321536027',
        ])
            ->for($project)
            ->create();

        $hook->url = route('hook.push', [$project->vcsProvider->webhookProvider, $hook]);
        $hook->secret = 'SDTRfWhKATNWftLauezpp5VF87GpWPEKgiVqZdfj';
        $hook->save();

        return $hook;
    }

    protected function pingData(): array
    {
        return json_decode(<<<DATA
{
  "zen": "Avoid administrative distraction.",
  "hook_id": 321536027,
  "hook": {
    "type": "Repository",
    "id": 321536027,
    "name": "web",
    "active": true,
    "events": [
      "push"
    ],
    "config": {
      "content_type": "json",
      "events": "[\"push\"]",
      "insecure_ssl": "0",
      "secret": "********",
      "url": "https://michman.lhr.rocks/hook/github/6a584acb-2f74-4c80-8e18-cd974eeca10b"
    },
    "updated_at": "2021-10-04T07:41:35Z",
    "created_at": "2021-10-04T07:41:35Z",
    "url": "https://api.github.com/repos/KondorB/django_demo/hooks/321536027",
    "test_url": "https://api.github.com/repos/KondorB/django_demo/hooks/321536027/test",
    "ping_url": "https://api.github.com/repos/KondorB/django_demo/hooks/321536027/pings",
    "deliveries_url": "https://api.github.com/repos/KondorB/django_demo/hooks/321536027/deliveries",
    "last_response": {
      "code": null,
      "status": "unused",
      "message": null
    }
  },
  "repository": {
    "id": 344106988,
    "node_id": "MDEwOlJlcG9zaXRvcnkzNDQxMDY5ODg=",
    "name": "django_demo",
    "full_name": "KondorB/django_demo",
    "private": true,
    "owner": {
      "login": "KondorB",
      "id": 5469212,
      "node_id": "MDQ6VXNlcjU0NjkyMTI=",
      "avatar_url": "https://avatars.githubusercontent.com/u/5469212?v=4",
      "gravatar_id": "",
      "url": "https://api.github.com/users/KondorB",
      "html_url": "https://github.com/KondorB",
      "followers_url": "https://api.github.com/users/KondorB/followers",
      "following_url": "https://api.github.com/users/KondorB/following{/other_user}",
      "gists_url": "https://api.github.com/users/KondorB/gists{/gist_id}",
      "starred_url": "https://api.github.com/users/KondorB/starred{/owner}{/repo}",
      "subscriptions_url": "https://api.github.com/users/KondorB/subscriptions",
      "organizations_url": "https://api.github.com/users/KondorB/orgs",
      "repos_url": "https://api.github.com/users/KondorB/repos",
      "events_url": "https://api.github.com/users/KondorB/events{/privacy}",
      "received_events_url": "https://api.github.com/users/KondorB/received_events",
      "type": "User",
      "site_admin": false
    },
    "html_url": "https://github.com/KondorB/django_demo",
    "description": null,
    "fork": false,
    "url": "https://api.github.com/repos/KondorB/django_demo",
    "forks_url": "https://api.github.com/repos/KondorB/django_demo/forks",
    "keys_url": "https://api.github.com/repos/KondorB/django_demo/keys{/key_id}",
    "collaborators_url": "https://api.github.com/repos/KondorB/django_demo/collaborators{/collaborator}",
    "teams_url": "https://api.github.com/repos/KondorB/django_demo/teams",
    "hooks_url": "https://api.github.com/repos/KondorB/django_demo/hooks",
    "issue_events_url": "https://api.github.com/repos/KondorB/django_demo/issues/events{/number}",
    "events_url": "https://api.github.com/repos/KondorB/django_demo/events",
    "assignees_url": "https://api.github.com/repos/KondorB/django_demo/assignees{/user}",
    "branches_url": "https://api.github.com/repos/KondorB/django_demo/branches{/branch}",
    "tags_url": "https://api.github.com/repos/KondorB/django_demo/tags",
    "blobs_url": "https://api.github.com/repos/KondorB/django_demo/git/blobs{/sha}",
    "git_tags_url": "https://api.github.com/repos/KondorB/django_demo/git/tags{/sha}",
    "git_refs_url": "https://api.github.com/repos/KondorB/django_demo/git/refs{/sha}",
    "trees_url": "https://api.github.com/repos/KondorB/django_demo/git/trees{/sha}",
    "statuses_url": "https://api.github.com/repos/KondorB/django_demo/statuses/{sha}",
    "languages_url": "https://api.github.com/repos/KondorB/django_demo/languages",
    "stargazers_url": "https://api.github.com/repos/KondorB/django_demo/stargazers",
    "contributors_url": "https://api.github.com/repos/KondorB/django_demo/contributors",
    "subscribers_url": "https://api.github.com/repos/KondorB/django_demo/subscribers",
    "subscription_url": "https://api.github.com/repos/KondorB/django_demo/subscription",
    "commits_url": "https://api.github.com/repos/KondorB/django_demo/commits{/sha}",
    "git_commits_url": "https://api.github.com/repos/KondorB/django_demo/git/commits{/sha}",
    "comments_url": "https://api.github.com/repos/KondorB/django_demo/comments{/number}",
    "issue_comment_url": "https://api.github.com/repos/KondorB/django_demo/issues/comments{/number}",
    "contents_url": "https://api.github.com/repos/KondorB/django_demo/contents/{+path}",
    "compare_url": "https://api.github.com/repos/KondorB/django_demo/compare/{base}...{head}",
    "merges_url": "https://api.github.com/repos/KondorB/django_demo/merges",
    "archive_url": "https://api.github.com/repos/KondorB/django_demo/{archive_format}{/ref}",
    "downloads_url": "https://api.github.com/repos/KondorB/django_demo/downloads",
    "issues_url": "https://api.github.com/repos/KondorB/django_demo/issues{/number}",
    "pulls_url": "https://api.github.com/repos/KondorB/django_demo/pulls{/number}",
    "milestones_url": "https://api.github.com/repos/KondorB/django_demo/milestones{/number}",
    "notifications_url": "https://api.github.com/repos/KondorB/django_demo/notifications{?since,all,participating}",
    "labels_url": "https://api.github.com/repos/KondorB/django_demo/labels{/name}",
    "releases_url": "https://api.github.com/repos/KondorB/django_demo/releases{/id}",
    "deployments_url": "https://api.github.com/repos/KondorB/django_demo/deployments",
    "created_at": "2021-03-03T11:41:55Z",
    "updated_at": "2021-09-23T10:14:28Z",
    "pushed_at": "2021-09-23T10:14:25Z",
    "git_url": "git://github.com/KondorB/django_demo.git",
    "ssh_url": "git@github.com:KondorB/django_demo.git",
    "clone_url": "https://github.com/KondorB/django_demo.git",
    "svn_url": "https://github.com/KondorB/django_demo",
    "homepage": null,
    "size": 23,
    "stargazers_count": 0,
    "watchers_count": 0,
    "language": "Python",
    "has_issues": false,
    "has_projects": false,
    "has_downloads": true,
    "has_wiki": false,
    "has_pages": false,
    "forks_count": 0,
    "mirror_url": null,
    "archived": false,
    "disabled": false,
    "open_issues_count": 0,
    "license": null,
    "allow_forking": true,
    "visibility": "private",
    "forks": 0,
    "open_issues": 0,
    "watchers": 0,
    "default_branch": "main"
  },
  "sender": {
    "login": "KondorB",
    "id": 5469212,
    "node_id": "MDQ6VXNlcjU0NjkyMTI=",
    "avatar_url": "https://avatars.githubusercontent.com/u/5469212?v=4",
    "gravatar_id": "",
    "url": "https://api.github.com/users/KondorB",
    "html_url": "https://github.com/KondorB",
    "followers_url": "https://api.github.com/users/KondorB/followers",
    "following_url": "https://api.github.com/users/KondorB/following{/other_user}",
    "gists_url": "https://api.github.com/users/KondorB/gists{/gist_id}",
    "starred_url": "https://api.github.com/users/KondorB/starred{/owner}{/repo}",
    "subscriptions_url": "https://api.github.com/users/KondorB/subscriptions",
    "organizations_url": "https://api.github.com/users/KondorB/orgs",
    "repos_url": "https://api.github.com/users/KondorB/repos",
    "events_url": "https://api.github.com/users/KondorB/events{/privacy}",
    "received_events_url": "https://api.github.com/users/KondorB/received_events",
    "type": "User",
    "site_admin": false
  }
}
DATA, true);
    }

    protected function pushData(): array
    {
        return json_decode(<<<DATA
{
  "ref": "refs/heads/main",
  "before": "fadc7041a9fbd56883d73942f4cf9d619027b8ea",
  "after": "5858ac6f0f611d9581157e8f32b468945357432d",
  "repository": {
    "id": 344106988,
    "node_id": "MDEwOlJlcG9zaXRvcnkzNDQxMDY5ODg=",
    "name": "django_demo",
    "full_name": "KondorB/django_demo",
    "private": true,
    "owner": {
      "name": "KondorB",
      "email": "5469212+KondorB@users.noreply.github.com",
      "login": "KondorB",
      "id": 5469212,
      "node_id": "MDQ6VXNlcjU0NjkyMTI=",
      "avatar_url": "https://avatars.githubusercontent.com/u/5469212?v=4",
      "gravatar_id": "",
      "url": "https://api.github.com/users/KondorB",
      "html_url": "https://github.com/KondorB",
      "followers_url": "https://api.github.com/users/KondorB/followers",
      "following_url": "https://api.github.com/users/KondorB/following{/other_user}",
      "gists_url": "https://api.github.com/users/KondorB/gists{/gist_id}",
      "starred_url": "https://api.github.com/users/KondorB/starred{/owner}{/repo}",
      "subscriptions_url": "https://api.github.com/users/KondorB/subscriptions",
      "organizations_url": "https://api.github.com/users/KondorB/orgs",
      "repos_url": "https://api.github.com/users/KondorB/repos",
      "events_url": "https://api.github.com/users/KondorB/events{/privacy}",
      "received_events_url": "https://api.github.com/users/KondorB/received_events",
      "type": "User",
      "site_admin": false
    },
    "html_url": "https://github.com/KondorB/django_demo",
    "description": null,
    "fork": false,
    "url": "https://github.com/KondorB/django_demo",
    "forks_url": "https://api.github.com/repos/KondorB/django_demo/forks",
    "keys_url": "https://api.github.com/repos/KondorB/django_demo/keys{/key_id}",
    "collaborators_url": "https://api.github.com/repos/KondorB/django_demo/collaborators{/collaborator}",
    "teams_url": "https://api.github.com/repos/KondorB/django_demo/teams",
    "hooks_url": "https://api.github.com/repos/KondorB/django_demo/hooks",
    "issue_events_url": "https://api.github.com/repos/KondorB/django_demo/issues/events{/number}",
    "events_url": "https://api.github.com/repos/KondorB/django_demo/events",
    "assignees_url": "https://api.github.com/repos/KondorB/django_demo/assignees{/user}",
    "branches_url": "https://api.github.com/repos/KondorB/django_demo/branches{/branch}",
    "tags_url": "https://api.github.com/repos/KondorB/django_demo/tags",
    "blobs_url": "https://api.github.com/repos/KondorB/django_demo/git/blobs{/sha}",
    "git_tags_url": "https://api.github.com/repos/KondorB/django_demo/git/tags{/sha}",
    "git_refs_url": "https://api.github.com/repos/KondorB/django_demo/git/refs{/sha}",
    "trees_url": "https://api.github.com/repos/KondorB/django_demo/git/trees{/sha}",
    "statuses_url": "https://api.github.com/repos/KondorB/django_demo/statuses/{sha}",
    "languages_url": "https://api.github.com/repos/KondorB/django_demo/languages",
    "stargazers_url": "https://api.github.com/repos/KondorB/django_demo/stargazers",
    "contributors_url": "https://api.github.com/repos/KondorB/django_demo/contributors",
    "subscribers_url": "https://api.github.com/repos/KondorB/django_demo/subscribers",
    "subscription_url": "https://api.github.com/repos/KondorB/django_demo/subscription",
    "commits_url": "https://api.github.com/repos/KondorB/django_demo/commits{/sha}",
    "git_commits_url": "https://api.github.com/repos/KondorB/django_demo/git/commits{/sha}",
    "comments_url": "https://api.github.com/repos/KondorB/django_demo/comments{/number}",
    "issue_comment_url": "https://api.github.com/repos/KondorB/django_demo/issues/comments{/number}",
    "contents_url": "https://api.github.com/repos/KondorB/django_demo/contents/{+path}",
    "compare_url": "https://api.github.com/repos/KondorB/django_demo/compare/{base}...{head}",
    "merges_url": "https://api.github.com/repos/KondorB/django_demo/merges",
    "archive_url": "https://api.github.com/repos/KondorB/django_demo/{archive_format}{/ref}",
    "downloads_url": "https://api.github.com/repos/KondorB/django_demo/downloads",
    "issues_url": "https://api.github.com/repos/KondorB/django_demo/issues{/number}",
    "pulls_url": "https://api.github.com/repos/KondorB/django_demo/pulls{/number}",
    "milestones_url": "https://api.github.com/repos/KondorB/django_demo/milestones{/number}",
    "notifications_url": "https://api.github.com/repos/KondorB/django_demo/notifications{?since,all,participating}",
    "labels_url": "https://api.github.com/repos/KondorB/django_demo/labels{/name}",
    "releases_url": "https://api.github.com/repos/KondorB/django_demo/releases{/id}",
    "deployments_url": "https://api.github.com/repos/KondorB/django_demo/deployments",
    "created_at": 1614771715,
    "updated_at": "2021-09-23T10:14:28Z",
    "pushed_at": 1633338840,
    "git_url": "git://github.com/KondorB/django_demo.git",
    "ssh_url": "git@github.com:KondorB/django_demo.git",
    "clone_url": "https://github.com/KondorB/django_demo.git",
    "svn_url": "https://github.com/KondorB/django_demo",
    "homepage": null,
    "size": 23,
    "stargazers_count": 0,
    "watchers_count": 0,
    "language": "Python",
    "has_issues": false,
    "has_projects": false,
    "has_downloads": true,
    "has_wiki": false,
    "has_pages": false,
    "forks_count": 0,
    "mirror_url": null,
    "archived": false,
    "disabled": false,
    "open_issues_count": 0,
    "license": null,
    "allow_forking": true,
    "visibility": "private",
    "forks": 0,
    "open_issues": 0,
    "watchers": 0,
    "default_branch": "main",
    "stargazers": 0,
    "master_branch": "main"
  },
  "pusher": {
    "name": "KondorB",
    "email": "5469212+KondorB@users.noreply.github.com"
  },
  "sender": {
    "login": "KondorB",
    "id": 5469212,
    "node_id": "MDQ6VXNlcjU0NjkyMTI=",
    "avatar_url": "https://avatars.githubusercontent.com/u/5469212?v=4",
    "gravatar_id": "",
    "url": "https://api.github.com/users/KondorB",
    "html_url": "https://github.com/KondorB",
    "followers_url": "https://api.github.com/users/KondorB/followers",
    "following_url": "https://api.github.com/users/KondorB/following{/other_user}",
    "gists_url": "https://api.github.com/users/KondorB/gists{/gist_id}",
    "starred_url": "https://api.github.com/users/KondorB/starred{/owner}{/repo}",
    "subscriptions_url": "https://api.github.com/users/KondorB/subscriptions",
    "organizations_url": "https://api.github.com/users/KondorB/orgs",
    "repos_url": "https://api.github.com/users/KondorB/repos",
    "events_url": "https://api.github.com/users/KondorB/events{/privacy}",
    "received_events_url": "https://api.github.com/users/KondorB/received_events",
    "type": "User",
    "site_admin": false
  },
  "created": false,
  "deleted": false,
  "forced": false,
  "base_ref": null,
  "compare": "https://github.com/KondorB/django_demo/compare/fadc7041a9fb...5858ac6f0f61",
  "commits": [
    {
      "id": "5858ac6f0f611d9581157e8f32b468945357432d",
      "tree_id": "1412ef03051bf8b36c6507b533b7d7f052df065d",
      "distinct": true,
      "message": "Update .env.example",
      "timestamp": "2021-10-04T12:13:56+03:00",
      "url": "https://github.com/KondorB/django_demo/commit/5858ac6f0f611d9581157e8f32b468945357432d",
      "author": {
        "name": "Bogdan Kondratov",
        "email": "5469212+KondorB@users.noreply.github.com",
        "username": "KondorB"
      },
      "committer": {
        "name": "Bogdan Kondratov",
        "email": "5469212+KondorB@users.noreply.github.com",
        "username": "KondorB"
      },
      "added": [

      ],
      "removed": [

      ],
      "modified": [
        ".env.example"
      ]
    }
  ],
  "head_commit": {
    "id": "5858ac6f0f611d9581157e8f32b468945357432d",
    "tree_id": "1412ef03051bf8b36c6507b533b7d7f052df065d",
    "distinct": true,
    "message": "Update .env.example",
    "timestamp": "2021-10-04T12:13:56+03:00",
    "url": "https://github.com/KondorB/django_demo/commit/5858ac6f0f611d9581157e8f32b468945357432d",
    "author": {
      "name": "Bogdan Kondratov",
      "email": "5469212+KondorB@users.noreply.github.com",
      "username": "KondorB"
    },
    "committer": {
      "name": "Bogdan Kondratov",
      "email": "5469212+KondorB@users.noreply.github.com",
      "username": "KondorB"
    },
    "added": [

    ],
    "removed": [

    ],
    "modified": [
      ".env.example"
    ]
  }
}
DATA, true);
    }

    protected function headers(array $merge = []): array
    {
        return Arr::merge([
            'Request method' => 'POST',
            'Accept' => '*/*',
            'content-type' => 'application/json',
            'User-Agent' => 'GitHub-Hookshot/03df353',
            'X-GitHub-Delivery' => '804ed210-24e6-11ec-8f0d-307dea89cdae',
            'X-GitHub-Event' => 'ping',
            'X-GitHub-Hook-ID' => 321536027,
            'X-GitHub-Hook-Installation-Target-ID' => 344106988,
            'X-GitHub-Hook-Installation-Target-Type' => 'repository',
        ], $merge);
    }

    protected function signature(array $data, string $secret, bool $invalid = false): string
    {
        $sig = 'sha256=' . hash_hmac('sha256', json_encode($data), $secret);

        if ($invalid) {
            $sig = Str::substr($sig, 0, Str::length($sig) - 1) . 'a';
        }

        return $sig;
    }
}
