<?php declare(strict_types=1);

namespace Tests\Feature\Traits;

use App\Support\Str;
use Mockery\MockInterface;
use phpseclib3\Net\SFTP;
use Tests\AbstractFeatureTest;

/**
 * Trait MocksSshSessions for feature tests
 *
 * @mixin AbstractFeatureTest
 */
trait MocksSshSessions
{
    /**
     * Set up the service container to return mocked SFTP instances.
     */
    protected function mockSftp(): void
    {
        $this->mockBind(SFTP::class, function (MockInterface $mock) {
            $mock->shouldReceive('getServerPublicHostKey')
                ->withNoArgs()
                ->zeroOrMoreTimes()
                ->andReturn(Str::random());
            $mock->shouldReceive('login')
                ->withAnyArgs()
                ->once()
                ->andReturnTrue();
            $mock->shouldReceive('setKeepAlive')
                ->withAnyArgs()
                ->zeroOrMoreTimes();
        });
    }
}
