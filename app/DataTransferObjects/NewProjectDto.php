<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class NewProjectDto extends AbstractDto
{
    public function __construct(
        public string $domain,
        public string $type,
        public bool $allow_sub_domains,
        public array $aliases = [],
        public string|null $python_version = null,
        public bool $create_database = false,
        public string|null $db_name = null,
        public bool $create_db_user = false,
        public string|null $db_user_name = null,
        public string|null $db_user_password = null,
    ) {}
}
