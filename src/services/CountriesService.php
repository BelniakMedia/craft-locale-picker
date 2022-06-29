<?php

namespace lhs\craft\localeSelectorField\services;

use Craft;
use craft\base\Component;
use lhs\craft\localeSelectorField\models\CountryModel;
use Locale;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Rinvex\Country\CountryLoaderException;

class CountriesService extends Component
{
    /**
     * Retrieves all countries as an array of key/val arrays, or as an array of NormalizedCountry objects
     * with countries name localized according to the language provided
     *
     * @param bool $asKeyValArray
     * @param null|string $language
     * @return array
     * @throws CountryLoaderException
     * FIXME: Remove firstParameter and always return an array of NormalizedCountry instances
     */
    public function getCountries(bool $asKeyValArray = false, string $language = null): array
    {
        //Fetch all countries
        $dataCountries = CountryLoader::countries(true, true);

        /**
         * Fill an array, with key/val arrays, or NormalizedCountry objects, according to the request
         *
         * @var Country $dataCountry
         */
        $countriesLocalized = [];
        foreach ($dataCountries as $dataCountry) {
            $localizedName = $this->getLocalizedName($dataCountry->getIsoAlpha2(), $language);
            if ($asKeyValArray) {
                $countriesLocalized[$dataCountry->getIsoAlpha2()] = $localizedName;
                asort($countriesLocalized);
            } else {
                $country = new CountryModel();

                $country
                    ->setName($localizedName)
                    ->setNativeName($dataCountry->getNativeName())
                    ->setIso2($dataCountry->getIsoAlpha2())
                    ->setIso3($dataCountry->getIsoAlpha3());

                $countriesLocalized[] = $country;
            }
        }

        return $countriesLocalized;
    }

    /**
     * Fetch a country by its ISO code
     *
     * @param string $iso2
     * @param string|null $language
     * @return null|CountryModel
     * @throws CountryLoaderException
     */
    public function getCountryByISO(string $iso2, string $language = null): ?CountryModel
    {
        if ($language === null) {
            $language = Craft::$app->getLocale()->getLanguageID();
        }

        //Fetch the country by its ISO code
        $dataCountry = CountryLoader::country($iso2);

        //If country was found, we create an object to return
        if (!empty($dataCountry)) {
            $country = new CountryModel();
            $country
                ->setName($this->getLocalizedName($iso2, $language))
                ->setNativeName($dataCountry->getNativeName())
                ->setIso2($dataCountry->getIsoAlpha2())
                ->setIso3($dataCountry->getIsoAlpha3());

            return $country;
        }

        return null;
    }

    public function getLocalizedName(string $iso2, ?string $language = null): string
    {
        if ($language === null) {
            $language = Craft::$app->getLocale()->getLanguageID();
        }

        return Locale::getDisplayRegion("-$iso2", $language);
    }
}
