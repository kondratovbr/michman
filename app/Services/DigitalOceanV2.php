<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\ServerDataCollection;
use App\Collections\SizeDataCollection;
use App\Collections\SshKeyDataCollection;
use App\DataTransferObjects\NewServerDto;
use App\DataTransferObjects\AuthTokenDto;
use App\DataTransferObjects\RegionDto;
use App\DataTransferObjects\ServerDto;
use App\DataTransferObjects\SizeDto;
use App\DataTransferObjects\SshKeyDto;
use App\Services\Exceptions\ExternalApiException;
use App\Support\Arr;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigitalOceanV2 extends AbstractServerProvider
{
    protected function getConfigPrefix(): string
    {
        return 'providers.list.digital_ocean_v2';
    }

    protected function commonCacheKey(string $key): string
    {
        return "providers.digital_ocean_v2.$key";
    }

    protected function request(): PendingRequest
    {
        return Http::withToken($this->token->token)->acceptJson();
    }

    /** Override the standard nextUrl method - DigitalOcean returns links in the body instead of a header. */
    protected function nextUrl(Response $response): string|null
    {
        $links = $this->decodeJson($response->body())->links ?? null;

        if (is_null($links))
            return null;

        return $links->pages->next ?? null;
    }

    public function credentialsAreValid(): bool
    {
        try {
            $response = $this->get('/account');
        } catch (RequestException) {
            return false;
        }

        return $response->successful();
    }

    protected function getAllRegionsFromApi(): RegionDataCollection
    {
        return $this->get('/regions', [],
            function (RegionDataCollection $carry, object $data) {
                /** @var object $region */
                foreach ($data->regions as $region) {
                    $carry->push(new RegionDto(
                        name: $region->name,
                        slug: $region->slug,
                        sizes: $region->sizes,
                        available: $region->available,
                    ));
                }

                return $carry;
            },
        new RegionDataCollection,
        );
    }

    protected function getAllSizesFromApi(): SizeDataCollection
    {
        return $this->get('/sizes', [],
            function (SizeDataCollection $carry, object $data) {
                /** @var object $size */
                foreach ($data->sizes as $size) {
                    $carry->push(new SizeDto(
                        slug: $size->slug,
                        transfer: $size->transfer,
                        priceMonthly: $size->price_monthly,
                        memoryMb: $size->memory,
                        cpus: $size->vcpus,
                        diskGb: $size->disk,
                        regions: $size->regions,
                        available: $size->available,
                        description: $size->description ?? '',
                    ));
                }

                return $carry;
            },
            new SizeDataCollection,
        );
    }

    public function getServer(string $serverId): ServerDto
    {
        $response = $this->get('/droplets/' . $serverId);
        $data = $this->decodeJson($response->body());

        return $this->serverDataFromResponseData($data->droplet);
    }

    public function getAllServers(): ServerDataCollection
    {
        return $this->get('/droplets', [],
            function (ServerDataCollection $carry, object $data) {
                foreach ($data->droplets as $droplet) {
                    $carry->push($this->serverDataFromResponseData($droplet));
                }

                return $carry;
            },
            new ServerDataCollection,
        );
    }

    public function getServerPublicIp4(string $serverId): string|null
    {
        $response = $this->get('/droplets/' . $serverId);
        $data = $this->decodeJson($response->body());

        return $this->publicIpFromNetworks($data->droplet->networks->v4);
    }

    public function createServer(NewServerDto $data, string $sshKeyIdentifier): ServerDto
    {
        if (empty($sshKeyIdentifier))
            throw new ExternalApiException('No SSH key identifier provided. SSH key is required to request a new server, it should be added to the server provider account beforehand.');

        $response = $this->post('/droplets', [
            'name' => $data->name,
            'region' => $data->region,
            'size' => $data->size,
            'image' => (string) $this->config('default_image'),
            'ssh_keys' => App::environment('local')
                ? [$sshKeyIdentifier, $this->config('dev_ssh_key_identifier')]
                : [$sshKeyIdentifier],
            'monitoring' => true,
        ]);
        $data = $this->decodeJson($response->body());

        return $this->serverDataFromResponseData($data->droplet);
    }

    public function deleteServer(string $externalId): void
    {
        $this->delete("/droplets/$externalId");
    }

    public function getAvailableRegions(): RegionDataCollection
    {
        $availableSizes = $this->getAllSizes()
            ->filter(fn(SizeDto $size) => $size->available)
            ->pluck('slug');

        return $this->getAllRegions()->filter(fn(RegionDto $region) =>
            $region->available
            && ! Arr::empty($region->sizes)
            && $availableSizes->intersect($region->sizes)->isNotEmpty()
        );
    }

    public function getAvailableSizes(): SizeDataCollection
    {
        $availableRegions = $this->getAllRegions()
            ->filter(fn(RegionDto $region) => $region->available)
            ->pluck('slug');

        return $this->getAllSizes()->filter(fn(SizeDto $size) =>
            $size->available
            && ! Arr::empty($size->regions)
            && $availableRegions->intersect($size->regions)->isNotEmpty()
        );
    }

    public function getSizesAvailableInRegion(RegionDto|string $region): SizeDataCollection
    {
        return $this->getAvailableSizes()->filter(fn(SizeDto $size) =>
            Arr::hasValue($size->regions, is_string($region) ? $region : $region->slug)
        );
    }

    public function addSshKey(string $name, string $publicKey): SshKeyDto
    {
        $response = $this->post('/account/keys', [
            'name' => $name,
            'public_key' => $publicKey,
        ]);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function getAllSshKeys(): SshKeyDataCollection
    {
        return $this->get('/account/keys', [],
            function (SshKeyDataCollection $carry, object $data) {
                /** @var object $key */
                foreach ($data->ssh_keys as $key) {
                    $carry->push($this->sshKeyDataFromResponseData($key));
                }

                return $carry;
            },
            new SshKeyDataCollection,
        );
    }

    public function addSshKeySafely(string $name, string $publicKey): SshKeyDto
    {
        $addedKeys = $this->getAllSshKeys();

        /** @var SshKeyDto $duplicatedAddedKey */
        $duplicatedAddedKey = $addedKeys->firstWhere('publicKey', $publicKey);

        if ($duplicatedAddedKey !== null) {
            if ($duplicatedAddedKey->name === $name)
                return $duplicatedAddedKey;

            return $this->updateSshKey($duplicatedAddedKey->id, $name);
        }

        return $this->addSshKey($name, $publicKey);
    }

    public function getSshKey(string $identifier): SshKeyDto
    {
        $response = $this->get('/account/keys/' . $identifier);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function updateSshKey(string $identifier, string $newName): SshKeyDto
    {
        $response = $this->put('/account/keys/' . $identifier, [
            'name' => $newName,
        ]);
        $data = $this->decodeJson(($response->body()));

        return $this->sshKeyDataFromResponseData($data->ssh_key);
    }

    public function refreshToken(): AuthTokenDto
    {
        Log::warning("DigitalOceanV2::refreshToken() method was called, but DO's tokens don't expire, so it shouldn't have been called at all.");

        return $this->token;
    }

    /** Convert SSH key object from response format to internal format. */
    protected function sshKeyDataFromResponseData(object $data): SshKeyDto
    {
        return new SshKeyDto(
            name: $data->name,
            publicKey: $data->public_key,
            id: (string) $data->id,
            fingerprint: $data->fingerprint,
        );
    }

    /** Convert server object from response format to internal format. */
    protected function serverDataFromResponseData(object $data): ServerDto
    {
        return new ServerDto(
            id: (string) $data->id,
            name: $data->name,
            publicIp4: $this->publicIpFromNetworks($data->networks->v4 ?? []),
        );
    }

    /** Extract a public IP address from a list of networks attached to a server. */
    protected function publicIpFromNetworks(array $networks): string|null
    {
        foreach ($networks as $network) {
            if ($network->type === 'public')
                return $network->ip_address;
        }

        return null;
    }
}
