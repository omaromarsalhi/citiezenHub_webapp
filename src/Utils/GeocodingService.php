<?php
namespace App\Utils;

use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client;


class GeocodingService
{
    private StatefulGeocoder $geocoder;

    public function __construct(StatefulGeocoder $geocoder)
    {
        $httpClient = new Client(); // Utilisez la classe importée
        $apiKey = '';
        $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, $apiKey);
        $this->geocoder = new StatefulGeocoder($provider, 'fr');
    }

    public function getMunicipalityFromAddress(string $address): ?string
    {
        $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

        foreach ($result as $item) {
            $locality = $item->getLocality();
            if ($locality) {
                return $locality;
            }
        }

        return null;
    }

}

