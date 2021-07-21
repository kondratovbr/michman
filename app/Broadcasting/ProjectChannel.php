<?php declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\Project;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;

class ProjectChannel implements BroadcastingChannelInterface
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Project $project): bool
    {
        return $user->is($project->user);
    }

    /**
     * Get the channel's definition string.
     */
    public static function definition(): string
    {
        return 'projects.{project}';
    }

    /**
     * Get the channel's name.
     */
    public static function name(Project|int $project): string
    {
        $projectKey = $project instanceof Project
            ? $project->getKey()
            : $project;

        return "projects.{$projectKey}";
    }

    /**
     * Get an instance of Laravel's Channel class corresponding with this broadcasting class.
     */
    public static function channelInstance(Project|int $project): PrivateChannel
    {
        return new PrivateChannel(static::name($project));
    }
}
