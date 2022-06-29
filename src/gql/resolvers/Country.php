<?php

namespace lhs\craft\localeSelectorField\gql\resolvers;

use Craft;
use craft\gql\base\Resolver;
use GraphQL\Type\Definition\ResolveInfo;
use lhs\craft\localeSelectorField\models\CountryModel;
use lhs\craft\localeSelectorField\Plugin;
use Rinvex\Country\CountryLoaderException;

class Country extends Resolver
{
    /**
     * Fetch the data as requested
     *
     * @param mixed $source
     * @param array $arguments
     * @param mixed $context
     * @param ResolveInfo $resolveInfo
     * @return CountryModel|null
     * @throws CountryLoaderException
     */
    public static function resolve(mixed $source, array $arguments, mixed $context, ResolveInfo $resolveInfo): ?CountryModel
    {
        //Get the language related to te site provided
        $language = null;
        if ($siteHandle = $arguments['site'] ?? null) {
            $language = Craft::$app->getSites()->getSiteByHandle($siteHandle)->language;
        }

        //Find the country related to the ISO2 code provided
        if ($iso2 = $arguments['iso2'] ?? null) {
            $country = Plugin::getInstance()->countriesService->getCountryByISO($iso2, $language);
        }

        return $country ?? null;
    }
}
