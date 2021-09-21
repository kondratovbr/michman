<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // TODO: Do I even use this?
        Blade::directive('alpine', function (string $variables) {
            return <<<PHP
<?php
    \$data = array_combine(
        array_map(
            fn(\$variable) => str_replace('$', '', trim(\$variable, ' ')),
            explode(',', '$variables')
        ),
        [$variables]
    );
    
    \$result = str_replace(["'", '"'], ["\'", "'"], json_encode(\$data));
    
    if (\$result[-1] === '}')
        \$result = substr(\$result, 0, -1);
        
    if (\$result[0] === '{')
        \$result = substr(\$result, 1, strlen(\$result) - 1);

    echo \$result;
?>
PHP;
        });
    }
}
