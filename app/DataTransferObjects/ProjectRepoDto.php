<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class ProjectRepoDto extends AbstractDto
{
    public function __construct(
        public string $root,
        public string $repo,
        public string $branch,
        public string $package,
        public bool $use_deploy_key,
        public string|null $requirements_file = null,
    ) {}
}
