<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class DeploymentDto extends AbstractDto
{
    public function __construct(
        public string $branch,
        public string $commit,
        public string $environment,
        public string $deploy_script,
        public string $gunicorn_config,
        public string $nginx_config,
    ) {}
}
