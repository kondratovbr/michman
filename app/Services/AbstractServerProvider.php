<?php declare(strict_types=1);

namespace App\Services;

use App\Collections\RegionDataCollection;
use App\Collections\SizeDataCollection;

abstract class AbstractServerProvider extends AbstractProvider implements ServerProviderInterface
{
    /** Call the API for all server regions it supports. */
    abstract protected function getAllRegionsFromApi(): RegionDataCollection;

    /** Call the API for all server sizes it supports. */
    abstract protected function getAllSizesFromApi(): SizeDataCollection;

    /** Get a collection of all regions supported by the API. */
    public function getAllRegions(): RegionDataCollection
    {
        return $this->getAllRegionsFromApi();
    }

    /** Get a collection of all sizes supported by the API. */
    public function getAllSizes(): SizeDataCollection
    {
        return $this->getAllSizesFromApi();
    }
}
