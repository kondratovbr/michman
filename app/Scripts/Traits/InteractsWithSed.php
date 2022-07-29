<?php declare(strict_types=1);

namespace App\Scripts\Traits;

trait InteractsWithSed
{
    /** Escape the slash "/" character used as a command delimiter in sed. */
    protected function sedEscape(string $string): string
    {
        return str_replace('/', '\/', $string);
    }

    /** Use sed command to remove a string from a file. */
    protected function sedRemoveString(string $file, string $string): bool|string
    {
        $string = $this->sedEscape($string);

        return $this->exec("sed -i '/$string/d' $file");
    }
}
