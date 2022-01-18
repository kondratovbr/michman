<?php

/*
 * Third-party VCS providers API configuration
 */

use App\Services\BitbucketV2;
use App\Services\GitHubV3;
use App\Services\GitLabV4;

return [

    // A real one GitHub API token for development
    'github_dev_token' => env('GITHUB_DEV_TOKEN', null),

    // TODO: Do I even use this?
    'default' => 'github_v3',
    
    'list' => [

        'github_v3' => [
            // GitHub scopes are per-request and should be properly configured here.
            'oauth_scopes' => ['user', 'repo', 'admin:public_key'],
            'provider_class' => GitHubV3::class,
            'supports_ssh_keys' => true,
            'base_path' => 'https://api.github.com',
            'auth_type' => 'token',
            'icon' => 'fab fa-github',
            // TODO: VERY IMPORTANT! I should have a scheduled command that will check that this is the current key and notify me on the emergency channel if it isn't.
            //       A key can be retrieved like this: ssh-keyscan -t rsa github.com
            //       Or for all keys: ssh-keyscan github.com
            //       The format returned is suitable for a known-hosts file.
            'ssh_host_key' => 'github.com ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==',
        ],

        'gitlab_v4' => [
            // GitLab scopes are configured on the OAuth application on gitlab.com,
            // but fewer scopes may be requested by a request.
            'oauth_scopes' => ['read_user', 'api'],
            'provider_class' => GitLabV4::class,
            'supports_ssh_keys' => true,
            'base_path' => 'https://gitlab.com/api/v4',
            'auth_type' => 'token',
            'icon' => 'fab fa-gitlab',
            'ssh_host_key' => 'gitlab.com ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCsj2bNKTBSpIYDEGk9KxsGh3mySTRgMtXL583qmBpzeQ+jqCMRgBqB98u3z++J1sKlXHWfM9dyhSevkMwSbhoR8XIq/U0tCNyokEi/ueaBMCvbcTHhO7FcwzY92WK4Yt0aGROY5qX2UKSeOvuP4D6TPqKF1onrSzH9bx9XUf2lEdWT/ia1NEKjunUqu1xOB/StKDHMoX4/OKyIzuS0q/T1zOATthvasJFoPrAjkohTyaDUz2LN5JoH839hViyEG82yB+MjcFV5MU3N1l1QL3cVUCh93xSaua1N85qivl+siMkPGbO5xR/En4iEY6K2XPASUEMaieWVNTRCtJ4S8H+9',
        ],

        'bitbucket_v2' => [
            // Bitbucket doesn't support granular scopes.
            // Scopes are configured on the OAuth application (consumer) on bitbucket.com
            'oauth_scopes' => [],
            'provider_class' => BitbucketV2::class,
            // Bitbucket doesn't support adding SSH keys over API for some reason.
            'supports_ssh_keys' => false,
            'base_path' => 'https://api.bitbucket.org/2.0',
            'auth_type' => 'token',
            'icon' => 'fab fa-bitbucket',
            'ssh_host_key' => 'bitbucket.org ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAubiN81eDcafrgMeLzaFPsw2kNvEcqTKl/VqLat/MaB33pZy0y3rJZtnqwR2qOOvbwKZYKiEO1O6VqNEBxKvJJelCq0dTXWT5pbO2gDXC6h6QDXCaHo6pOHGPUy+YBaGQRGuSusMEASYiWunYN0vCAI8QaXnWMXNMdFP3jHAJH0eDsoiGnLPBlBp4TNm6rYI74nMzgz3B9IikW4WVK+dc8KZJZWYjAuORU3jc1c/NPskD2ASinf8v3xnfXeukU0sJ5N6m5E8VLjObPEO+mN2t/FZTMZLiFqPWc/ALSqnMnnhwrNi2rbfg/rd/IpL8Le3pSBne8+seeFVBoGqzHM9yXw==',
        ],

    ],

];
