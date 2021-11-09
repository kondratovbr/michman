<?php

namespace Tests\Feature;

use App\Services\DigitalOceanV2;
use App\Services\ServerProviderInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

// TODO: CRITICAL! CONTINUE.

class DigitalOceanV2ApiNewTest extends AbstractFeatureTest
{
    public const TOKEN = '666666';

    public function test_credentialsAreValid_method()
    {
        Http::fake();

        $result = $this->api()->credentialsAreValid();

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'account'));

        $this->assertTrue($result);
    }

    public function test_getServer_method()
    {
        Http::fake([
            'https://api.digitalocean.com/v2/droplets/123' => Http::response(),
            '*' => Http::response(null, 500),
        ]);

        $result = $this->api()->getServer('123');

        Http::assertSent(fn(Request $request) => $this->checkRequest($request, 'GET', 'droplets/123'));

        //
    }

    protected function api(): DigitalOceanV2
    {
        /** @var ServerProviderInterface $api */
        $api = App::make('digital_ocean_v2_servers', ['token' => static::TOKEN]);

        return $api;
    }

    protected function checkRequest(Request $request, string $method, string $url): bool
    {
        return $request->method() == $method
            && $request->url() == "https://api.digitalocean.com/v2/{$url}"
            && $request->hasHeader('Authorization', 'Bearer ' . static::TOKEN);
    }
}
