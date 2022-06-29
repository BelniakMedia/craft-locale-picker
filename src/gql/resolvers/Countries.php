<?php

namespace lhs\craft\localeSelectorField\gql\resolvers;

use craft\gql\base\Resolver;
use GraphQL\Type\Definition\ResolveInfo;
use lhs\craft\localeSelectorField\Plugin;
use Rinvex\Country\CountryLoaderException;

class Countries extends Resolver
{
    /**
     * Fetch the data as requested
     *
     * @param mixed $source
     * @param array $arguments
     * @param mixed $context
     * @param ResolveInfo $resolveInfo
     * @return array|null
     * @throws CountryLoaderException
     */
    public static function resolve(mixed $source, array $arguments, mixed $context, ResolveInfo $resolveInfo): ?array
    {
        $countries = Plugin::getInstance()->countriesService->getCountries(false, ($arguments['site'] ?? null));

        return $countries ?? null;
    }
}
