<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Certificate Eloquent model
 *
 * @property int $id
 * @property int $serverId
 * @property string $type
 * @property string $domain
 * @property CertificateState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read string $name
 * @property-read string $directory
 * @property-read Set $domains
 * @property-read Server $server
 * @method static CertificateFactory factory(...$parameters)
 * @mixin IdeHelperCertificate
 * @property int $server_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate orWhereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate orWhereState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * Daemon Eloquent model
 * 
 * Represents a supervisord-managed program running on a server.
 *
 * @property int $id
 * @property int $serverId
 * @property string $command
 * @property string $username
 * @property string|null $directory
 * @property int $processes
 * @property int $startSeconds
 * @property DaemonState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read string $name
 * @property-read string $shortCommand
 * @property-read string $shortDirectory
 * @property-read Server $server
 * @method static DaemonFactory factory(...$parameters)
 * @mixin IdeHelperDaemon
 * @property int $server_id
 * @property int $start_seconds
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon orWhereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon orWhereState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereProcesses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereStartSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Daemon whereUsername($value)
 */
	class Daemon extends \Eloquent {}
}

namespace App\Models{
/**
 * Database Eloquent model
 *
 * @property int $id
 * @property int $serverId
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Server $server
 * @property-read Collection $databaseUsers
 * @property-read Project|null $project
 * @method static DatabaseFactory factory(...$parameters)
 * @mixin IdeHelperDatabase
 * @property int $server_id
 * @property int $tasks
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read int|null $database_users_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Database newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Database newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Database query()
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Database whereUpdatedAt($value)
 */
	class Database extends \Eloquent implements \App\Models\Interfaces\HasTasksCounterInterface {}
}

namespace App\Models{
/**
 * DatabaseUser Eloquent model
 *
 * @property int $id
 * @property int $serverId
 * @property string $name
 * @property string|null $password
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Server $server
 * @property-read Collection $databases
 * @property-read Project|null $project
 * @method static DatabaseUserFactory factory(...$parameters)
 * @mixin IdeHelperDatabaseUser
 * @property int $server_id
 * @property int $tasks
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read int|null $databases_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseUser whereUpdatedAt($value)
 */
	class DatabaseUser extends \Eloquent implements \App\Models\Interfaces\HasTasksCounterInterface {}
}

namespace App\Models{
/**
 * DeploySshKey Eloquent model
 * 
 * Represents an SSH key that was automatically created for a server
 * and added to a specific VCS repository to be used for deployment.
 *
 * @property int $id
 * @property int $projectId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read string $name
 * @property-read Project $project
 * @method static DeploySshKeyFactory factory(...$parameters)
 * @mixin IdeHelperDeploySshKey
 * @property int $project_id
 * @property \phpseclib3\Crypt\Common\PublicKey $public_key
 * @property \phpseclib3\Crypt\Common\PrivateKey|null $private_key
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string|null $private_key_string
 * @property-read string $public_key_fingerprint
 * @property-read string $public_key_string
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploySshKey whereUpdatedAt($value)
 */
	class DeploySshKey extends \Eloquent implements \App\Models\Interfaces\SshKeyInterface {}
}

namespace App\Models{
/**
 * Deployment Eloquent model
 * 
 * Represents a single complete deployment that can happen at multiple servers,
 * DeploymentServerPivot contains information about that process on a single server.
 *
 * @property int $id
 * @property int $projectId
 * @property string $type
 * @property string $branch
 * @property string|null $commit
 * @property string|null $environment
 * @property string|null $deployScript
 * @property string|null $gunicornConfig
 * @property string|null $nginxConfig
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read bool $started
 * @property-read bool $finished
 * @property-read bool|null $successful
 * @property-read bool|null $failed
 * @property-read string $status
 * @property-read CarbonInterface|null $startedAt
 * @property-read CarbonInterface|null $finishedAt
 * @property-read CarbonInterval|null $duration
 * @property-read string $createdAtFormatted
 * @property-read string $commitUrl
 * @property-read User $user
 * @property-read Project $project
 * @property-read Collection $servers
 * @property-read DeploymentServerPivot|null $serverDeployment
 * @method static DeploymentQueryBuilder query()
 * @method static DeploymentFactory factory(...$parameters)
 * @mixin IdeHelperDeployment
 * @property int $project_id
 * @property string|null $deploy_script
 * @property string|null $gunicorn_config
 * @property string|null $nginx_config
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read int|null $servers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment finished()
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment newModelQuery()
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment newQuery()
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment successful()
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereBranch($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereCommit($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereCreatedAt($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereDeployScript($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereEnvironment($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereGunicornConfig($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereId($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereNginxConfig($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereProjectId($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereType($value)
 * @method static \App\QueryBuilders\DeploymentQueryBuilder|Deployment whereUpdatedAt($value)
 */
	class Deployment extends \Eloquent {}
}

namespace App\Models{
/**
 * Pivot model for Deployment to Server relation
 * 
 * Represents the deployment process performed on a specific server.
 *
 * @property int $id
 * @property int $deploymentId
 * @property int $serverId
 * @property CarbonInterface|null $startedAt
 * @property CarbonInterface|null $finishedAt
 * @property bool|null $successful
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read bool $started
 * @property-read bool $finished
 * @property-read CarbonInterval|null $duration
 * @mixin IdeHelperDeploymentServerPivot
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentServerPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentServerPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentServerPivot query()
 */
	class DeploymentServerPivot extends \Eloquent {}
}

namespace App\Models{
/**
 * FirewallRule Eloquent model
 *
 * @property int $id
 * @property int $serverId
 * @property string $name
 * @property string $port
 * @property string $fromIp
 * @property bool $canDelete
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Server $server
 * @method static FirewallRuleFactory factory(...$parameters)
 * @mixin IdeHelperFirewallRule
 * @property int $server_id
 * @property string|null $from_ip
 * @property bool|null $can_delete
 * @property string $status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereCanDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereFromIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FirewallRule whereUpdatedAt($value)
 */
	class FirewallRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Membership
 *
 * @mixin IdeHelperMembership
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string|null $role
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUserId($value)
 */
	class Membership extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property string $id
 * @property string $type
 * @property array $data
 * @property CarbonInterface $readAt
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read Model $notifiable
 * @mixin IdeHelperNotification
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property \Carbon\CarbonImmutable|null $read_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] all($columns = ['*'])
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification read()
 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * OAuthUser Eloquent model
 * 
 * Represents an OAuth account linked to some User model.
 *
 * @property int $id
 * @property int $userId
 * @property string $provider
 * @property string $oauthId
 * @property string $nickname
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Provider|null $serverProvider
 * @property-read VcsProvider|null $vcsProvider
 * @method static OAuthUserFactory factory(...$parameters)
 * @mixin IdeHelperOAuthUser
 * @property string $oauth_id
 * @property int $user_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereOauthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OAuthUser whereUserId($value)
 */
	class OAuthUser extends \Eloquent {}
}

namespace App\Models{
/**
 * Project Eloquent model
 *
 * @property int $id
 * @property int $userId
 * @property string $domain
 * @property Set<string> $aliases
 * @property bool $allowSubDomains
 * @property string $type
 * @property string $root
 * @property string|null $pythonVersion
 * @property string|null $repo
 * @property string|null $branch
 * @property string|null $package
 * @property bool|null $useDeployKey
 * @property string|null $requirementsFile
 * @property string|null $environment
 * @property string|null $deployScript
 * @property string|null $gunicornConfig
 * @property string|null $nginxConfig
 * @property bool $removingRepo
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read string $fullDomainName
 * @property-read string $serverUsername
 * @property-read string|null $projectName
 * @property-read string $deployScriptFilePath
 * @property-read string $envFilePath
 * @property-read string $nginxConfigFilePath
 * @property-read string $userNginxConfigFilePath
 * @property-read string $gunicornConfigFilePath
 * @property-read string $projectDir
 * @property-read string $michmanDir
 * @property-read bool $deployed
 * @property-read bool $webhookEnabled
 * @property-read string|null $repoUrl
 * @property-read string|null $vcsProviderName
 * @property-read bool $repoInstalled
 * @property-read User $user
 * @property-read Collection $servers
 * @property-read DeploySshKey|null $deploySshKey
 * @property-read VcsProvider|null $vcsProvider
 * @property-read Database|null $database
 * @property-read DatabaseUser|null $databaseUser
 * @property-read Collection $deployments
 * @property-read Collection $workers
 * @property-read Webhook|null $webhook
 * @method static ProjectFactory factory(...$parameters)
 * @mixin IdeHelperProject
 * @property int $user_id
 * @property int|null $vcs_provider_id
 * @property int $allow_sub_domains
 * @property string|null $python_version
 * @property bool|null $use_deploy_key
 * @property string|null $requirements_file
 * @property string|null $deploy_script
 * @property string|null $gunicorn_config
 * @property string|null $nginx_config
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $database_id
 * @property int|null $database_user_id
 * @property bool|null $removing_repo
 * @property-read int|null $deployments_count
 * @property-read int|null $servers_count
 * @property-read int|null $workers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereAllowSubDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDatabaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDatabaseUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeployScript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereGunicornConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereNginxConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePythonVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereRemovingRepo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereRepo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereRequirementsFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUseDeployKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereVcsProviderId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * Pivot model for Project to Server relation
 *
 * @property int $id
 * @property int $projectId
 * @property int $serverId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @mixin IdeHelperProjectServerPivot
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectServerPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectServerPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectServerPivot query()
 */
	class ProjectServerPivot extends \Eloquent {}
}

namespace App\Models{
/**
 * Server Provider Eloquent model
 * 
 * Represents an account on a third-party server provider connected to the app over their API,
 * like DigitalOcean or Linode.
 *
 * @property int $id
 * @property int $userId
 * @property string $provider
 * @property string|null $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read string $status
 * @property-read string $localName
 * @property-read User $user
 * @property-read Collection $servers
 * @property-read OAuthUser|null $oauthUser
 * @method static ProviderFactory factory(...$parameters)
 * @mixin IdeHelperProvider
 * @property int $user_id
 * @property \App\DataTransferObjects\AbstractDto $token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $oauth_user_id
 * @property-read bool $expired
 * @property-read int|null $servers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Provider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Provider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Provider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereOauthUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Provider whereUserId($value)
 */
	class Provider extends \Eloquent {}
}

namespace App\Models{
/**
 * Python Eloquent model
 *
 * @property int $id
 * @property int $serverId
 * @property string $version
 * @property string|null $status
 * @property string|null $patchVersion
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Server $server
 * @method static PythonFactory factory(...$parameters)
 * @mixin IdeHelperPython
 * @property int $server_id
 * @property string|null $patch_version
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Python newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Python newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Python query()
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python wherePatchVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Python whereVersion($value)
 */
	class Python extends \Eloquent {}
}

namespace App\Models{
/**
 * Server Eloquent model
 * 
 * Database IDs:
 *
 * @property int $id
 * @property int $providerId
 * 
 * Database-stored properties:
 * @property string $externalId
 * @property string $region
 * @property string $size
 * @property string $name
 * @property string $type
 * @property string|null $publicIp
 * @property string $sshPort
 * @property string|null $sshHostKey
 * @property string|null $sudoPassword
 * @property bool|null $suitable
 * @property bool|null $available
 * @property string|null $installedDatabase
 * @property string|null $databaseRootPassword
 * @property string|null $installedCache
 * @property ServerState $state
 * @property CarbonInterface $updatedAt
 * @property CarbonInterface $createdAt
 * 
 * Derived properties:
 * @property-read User $user
 * @property-read string $publicWorkerDir
 * 
 * Relations:
 * @property-read Provider $provider
 * @property-read WorkerSshKey $workerSshKey
 * @property-read Collection $logs
 * @property-read Collection $userSshKeys
 * @property-read Collection $databases
 * @property-read Collection $databaseUsers
 * @property-read Collection $pythons
 * @property-read ServerSshKey $serverSshKey
 * @property-read Collection $firewallRules
 * @property-read Collection $projects
 * @property-read Collection $deployments
 * @property-read DeploymentServerPivot|null $serverDeployment
 * @property-read Collection $certificates
 * @property-read Collection $workers
 * @property-read Collection $daemons
 * @method static ServerFactory factory(...$parameters)
 * @mixin IdeHelperServer
 * @property int $provider_id
 * @property string|null $external_id
 * @property string|null $public_ip
 * @property string|null $ssh_port
 * @property string|null $ssh_host_key
 * @property mixed|null $sudo_password
 * @property string|null $installed_database
 * @property mixed|null $database_root_password
 * @property string|null $installed_cache
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read int|null $certificates_count
 * @property-read int|null $daemons_count
 * @property-read int|null $database_users_count
 * @property-read int|null $databases_count
 * @property-read int|null $deployments_count
 * @property-read int|null $firewall_rules_count
 * @property-read int|null $logs_count
 * @property-read int|null $projects_count
 * @property-read int|null $pythons_count
 * @property-read int|null $user_ssh_keys_count
 * @property-read int|null $workers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server orWhereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Server orWhereState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDatabaseRootPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereInstalledCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereInstalledDatabase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePublicIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSshHostKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSshPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSudoPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSuitable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 */
	class Server extends \Eloquent {}
}

namespace App\Models{
/**
 * Server Log Eloquent model to store SSH logs in the database
 *
 * @property int $id
 * @property int $serverId
 * @property string $type
 * @property string|null $command
 * @property int|null $exitCode
 * @property string|null $content
 * @property string|null $localFile
 * @property string|null $remoteFile
 * @property bool|null $success
 * @property CarbonInterface $createdAt
 * @property-read bool $renderable
 * @property-read Server $server
 * @method static ServerLogFactory factory(...$parameters)
 * @mixin IdeHelperServerLog
 * @property int $server_id
 * @property int|null $exit_code
 * @property string|null $local_file
 * @property string|null $remote_file
 * @property \Carbon\CarbonImmutable|null $created_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereExitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereLocalFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereRemoteFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerLog whereType($value)
 */
	class ServerLog extends \Eloquent {}
}

namespace App\Models{
/**
 * ServerSshKey Eloquent model
 * 
 * Represents an SSH key that is used by a server
 * to access project repositories during deployment.
 *
 * @property int $id
 * @property int $serverId
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read Server $server
 * @property-read ServerSshKeyVcsProviderPivot|null $vcsProviderKey
 * @method static ServerSshKeyFactory factory(...$parameters)
 * @mixin IdeHelperServerSshKey
 * @property int $server_id
 * @property \phpseclib3\Crypt\Common\PublicKey $public_key
 * @property \phpseclib3\Crypt\Common\PrivateKey|null $private_key
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string|null $private_key_string
 * @property-read string $public_key_fingerprint
 * @property-read string $public_key_string
 * @property-read \App\Collections\EloquentCollection|\App\Models\VcsProvider[] $vcsProviders
 * @property-read int|null $vcs_providers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKey whereUpdatedAt($value)
 */
	class ServerSshKey extends \Eloquent implements \App\Models\Interfaces\SshKeyInterface {}
}

namespace App\Models{
/**
 * Pivot model for ServerSshKey to VcsProvider deployment.
 * 
 * Represents a key added to a VCS provider account.
 *
 * @property int $id
 * @property int $serverSshKeyId
 * @property int $vcsProviderId
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @mixin IdeHelperServerSshKeyVcsProviderPivot
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKeyVcsProviderPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKeyVcsProviderPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerSshKeyVcsProviderPivot query()
 */
	class ServerSshKeyVcsProviderPivot extends \Eloquent {}
}

namespace App\Models{
/**
 * Pivot model for Server to UserSshKey relation
 *
 * @property int $id
 * @property int $userId
 * @property int $serverId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @mixin IdeHelperServerUserSshKeyPivot
 * @method static \Illuminate\Database\Eloquent\Builder|ServerUserSshKeyPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerUserSshKeyPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServerUserSshKeyPivot query()
 */
	class ServerUserSshKeyPivot extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Team
 *
 * @mixin IdeHelperTeam
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_team
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TeamInvitation[] $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\TeamFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePersonalTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUserId($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TeamInvitation
 *
 * @mixin IdeHelperTeamInvitation
 * @property int $id
 * @property int $team_id
 * @property string $email
 * @property string|null $role
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamInvitation whereUpdatedAt($value)
 */
	class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * User Eloquent model
 *
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property CarbonInterface $emailVerifiedAt
 * @property bool $isDeleting
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read string $name
 * @property-read string $avatarUrl
 * @property-read Collection $providers
 * @property-read Collection $servers
 * @property-read Collection $userSshKeys
 * @property-read Collection $vcsProviders
 * @property-read Collection $projects
 * @property-read Collection $oauthUsers
 * @property-read Collection $webhooks
 * @method static UserFactory factory(...$parameters)
 * @mixin IdeHelperUser
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property bool|null $is_deleting
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read \Laravel\Paddle\Customer|null $customer
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $oauth_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read int|null $projects_count
 * @property-read int|null $providers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Paddle\Receipt[] $receipts
 * @property-read int|null $receipts_count
 * @property-read int|null $servers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Paddle\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read int|null $user_ssh_keys_count
 * @property-read int|null $vcs_providers_count
 * @property-read int|null $webhooks_count
 * @method static \Illuminate\Database\Eloquent\Builder|User isDeleting()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User notDeleting()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsDeleting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Illuminate\Contracts\Translation\HasLocalePreference {}
}

namespace App\Models{
/**
 * UserSshKey Eloquent model
 * 
 * Represents an SSH key that the user added to their account to be able to access their servers manually.
 *
 * @property int $id
 * @property int $userId
 * @property string $name
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read null $privateKey
 * @property-read null $privateKeyString
 * @property-read User $user
 * @property-read Collection $servers
 * @method static UserSshKeyFactory factory(...$parameters)
 * @mixin IdeHelperUserSshKey
 * @property int $user_id
 * @property \phpseclib3\Crypt\Common\PublicKey $public_key
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \phpseclib3\Crypt\Common\PrivateKey|null $private_key
 * @property-read string|null $private_key_string
 * @property-read string $public_key_fingerprint
 * @property-read string $public_key_string
 * @property-read int|null $servers_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSshKey whereUserId($value)
 */
	class UserSshKey extends \Eloquent implements \App\Models\Interfaces\SshKeyInterface {}
}

namespace App\Models{
/**
 * VcsProvider Eloquent model
 * 
 * Represents an account on a third-party VCS service provider connected to the app over their API,
 * like GitHub or GitLab.
 *
 * @property int $id
 * @property int $userId
 * @property string $provider
 * @property string $externalId
 * @property string $nickname
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read string $webhookProvider
 * @property-read User $user
 * @property-read Collection $projects
 * @property-read ServerSshKeyVcsProviderPivot|null $vcsProviderKey
 * @property-read OAuthUser|null $oauthUser
 * @method static VcsProviderFactory factory(...$parameters)
 * @mixin IdeHelperVcsProvider
 * @property int $user_id
 * @property string $external_id
 * @property \App\DataTransferObjects\AbstractDto $token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $oauth_user_id
 * @property-read bool $expired
 * @property-read int|null $projects_count
 * @property-read \App\Collections\EloquentCollection|\App\Models\ServerSshKey[] $serverSshKeys
 * @property-read int|null $server_ssh_keys_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereOauthUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VcsProvider whereUserId($value)
 */
	class VcsProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * Webhook Eloquent model
 *
 * @property int $id
 * @property int $projectId
 * @property string $uuid
 * @property string $provider
 * @property string $type
 * @property string $url
 * @property string $secret
 * @property string|null $externalId
 * @property WebhookState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read string $repo
 * @property-read Project $project
 * @property-read Collection $calls
 * @method static WebhookFactory factory(...$parameters)
 * @mixin IdeHelperWebhook
 * @property int $project_id
 * @property string|null $external_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read int|null $calls_count
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook orWhereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook orWhereState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUuid($value)
 */
	class Webhook extends \Eloquent {}
}

namespace App\Models{
/**
 * WebhookCall Eloquent model
 *
 * @property int $id
 * @property int $webhookId
 * @property string $type
 * @property string $url
 * @property string $externalId
 * @property array $headers
 * @property array $payload
 * @property WebhookCallExceptionDto|null $exception
 * @property bool $processed
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read Webhook $webhook
 * @method static WebhookCallFactory factory(...$parameters)
 * @mixin IdeHelperWebhookCall
 * @property int $webhook_id
 * @property string $external_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall query()
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebhookCall whereWebhookId($value)
 */
	class WebhookCall extends \Eloquent {}
}

namespace App\Models{
/**
 * Worker Eloquent model
 * 
 * Represents a server queue worker.
 *
 * @property int $id
 * @property int $projectId
 * @property string $type
 * @property string|null $app
 * @property int|null $processes
 * @property array|null $queues
 * @property int $stopSeconds
 * @property int|null $maxTasksPerChild
 * @property int|null $maxMemoryPerChild
 * @property WorkerState $state
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read string $debugLevel
 * @property-read string $name
 * @property-read Project $project
 * @property-read Server $server
 * @method static WorkerFactory factory(...$parameters)
 * @mixin IdeHelperWorker
 * @property int $server_id
 * @property int $project_id
 * @property int|null $stop_seconds
 * @property int|null $max_tasks_per_child
 * @property int|null $max_memory_per_child
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Worker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Worker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Worker orWhereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker orWhereState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker query()
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereMaxMemoryPerChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereMaxTasksPerChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereNotState(string $column, $states)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereProcesses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereQueues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereStopSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worker whereUpdatedAt($value)
 */
	class Worker extends \Eloquent {}
}

namespace App\Models{
/**
 * WorkerSshKey Eloquent model
 * 
 * Represents an SSH key that our worker process uses to access the server.
 *
 * @property int $id
 * @property int $serverId
 * @property string $name
 * @property string|null $externalId
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 * @property-read User $user
 * @property-read Server $server
 * @method static WorkerSshKeyFactory factory(...$parameters)
 * @mixin IdeHelperWorkerSshKey
 * @property int $server_id
 * @property \phpseclib3\Crypt\Common\PublicKey $public_key
 * @property \phpseclib3\Crypt\Common\PrivateKey|null $private_key
 * @property string|null $external_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string|null $private_key_string
 * @property-read string $public_key_fingerprint
 * @property-read string $public_key_string
 * @method static \App\Collections\EloquentCollection|static[] all($columns = ['*'])
 * @method static \App\Collections\EloquentCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkerSshKey whereUpdatedAt($value)
 */
	class WorkerSshKey extends \Eloquent implements \App\Models\Interfaces\SshKeyInterface {}
}

namespace App\Support\Websockets{
/**
 * App\Support\Websockets\StatisticsEntry
 *
 * @mixin IdeHelperStatisticsEntry
 * @property int $id
 * @property string $app_id
 * @property int $peak_connections_count
 * @property int $websocket_messages_count
 * @property int $api_messages_count
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereApiMessagesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry wherePeakConnectionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatisticsEntry whereWebsocketMessagesCount($value)
 */
	class StatisticsEntry extends \Eloquent {}
}

