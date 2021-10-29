<?php declare(strict_types=1);

namespace App\Support;

use phpseclib3\Crypt\Common\PrivateKey;
use phpseclib3\Crypt\Common\PublicKey;

class SshKeyFormatter
{
    /**
     * Convert any SSH key from phpseclib3 to a properly formatted string.
     *
     * Handles all the weird quirks of phpseclib3 built-in toString methods.
     */
    public static function format(
        PrivateKey|PublicKey $key,
        string $comment = null,
        string $format = 'OpenSSH'
    ): string {
        $string = $key->toString($format, [
            // phpseclib3 adds its own comment if none provided - so we have to give it
            // at least an empty string, even null doesn't work.
            'comment' => empty($comment) ? '' : $comment,
        ]);

        // For some reason toString() method on a public key in phpseclib3 adds a space at the end
        // if the comment provided is an empty string so better trim the result just in case.
        $string = trim($string, ' ');

        // For some reason phpseclib3 uses DOS "\r\n" characters for newlines while outputting keys to string,
        // so we have to convert that to Unix "\n" character or else OpenSSH on Linux won't understand it.
        // (Though third-party APIs seem to have no problems with it.)
        $string = Str::replace("\r\n", "\n", $string);

        return $string;
    }
}
