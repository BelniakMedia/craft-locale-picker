<?php

namespace lhs\craft\localeSelectorField\services;

use Craft;
use craft\base\Component;
use craft\i18n\PhpMessageSource;
use lhs\craft\localeSelectorField\models\CountryModel;
use Locale;
use Rinvex\Country\Country;
use Rinvex\Country\CountryLoader;
use Rinvex\Country\CountryLoaderException;

class CountriesService extends Component
{
    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        /**
         * Define translations source for the Countries
         * Using PHP bundle "umpirsky/country-list" (https://github.com/umpirsky/country-list)
         */
        Craft::$app->i18n->translations['country'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'fr-FR',
            'basePath' => Craft::getAlias("@vendor/umpirsky/country-list/data"),
            'forceTranslation' => true,
            'allowOverrides' => true,
        ];

        parent::__construct($config);
    }

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
            $localizedName = Locale::getDisplayRegion('-' . $dataCountry->getIsoAlpha2(), $language);
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
     * @param string $iso
     * @param string|null $language
     * @return null|CountryModel
     * @throws CountryLoaderException
     */
    public function getCountryByISO(string $iso, string $language = null): ?CountryModel
    {
        //Fetch the country by its ISO code
        $dataCountry = CountryLoader::country($iso);

        //If country was found, we create an object to return
        if (!empty($dataCountry)) {
            $country = new CountryModel();
            $country
                ->setName(Craft::t('country', $dataCountry->getIsoAlpha2(), [], $language))
                ->setNativeName($dataCountry->getNativeName())
                ->setIso2($dataCountry->getIsoAlpha2())
                ->setIso3($dataCountry->getIsoAlpha3());

            return $country;
        }

        return null;
    }
}
