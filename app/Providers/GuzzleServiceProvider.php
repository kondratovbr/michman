<?php declare(strict_types=1);

namespace App\Providers;

use App\Support\Arr;
use App\Support\Str;
use Illuminate\Http\Client\Response;
use Illuminate\Support\ServiceProvider;

class GuzzleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** Parse response's "Link" header. */
        Response::macro('links', function (): array|null {
            /** @var Response $this */

            if (! isset($this->headers()['Link']))
                return null;

            /** @var string $header */
            $header = $this->header('Link');

            $links = explode(',', $header);

            return Arr::mapAssoc($links, function (int $index, string $link) {
                preg_match(
                    '/<(.*?(?:(?:\?|\&)page=(\d+).*)?)>.*rel="(.*)"/',
                    $link,
                    $matches,
                    PREG_UNMATCHED_AS_NULL,
                );

                return [Str::lower($matches[3]), $matches[1]];
            });
        });

        /** Get a "next" property from a response's "Link" header. */
        Response::macro('nextUrl', function (): string|null {
            /** @var Response $this */

            $links = $this->links();

            return $links['next'] ?? null;
        });
    }
}
