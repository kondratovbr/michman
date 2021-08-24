<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class NewProjectData extends DataTransferObject
{
    public string $domain;
    public array $aliases = [];
    public string $type;
    public string|null $python_version = null;
    public bool $allow_sub_domains;
    public bool $create_database = false;
    public string|null $db_name = null;
    public bool $create_db_user = false;
    public string|null $db_user_name = null;
    public string|null $db_user_password = null;
}
