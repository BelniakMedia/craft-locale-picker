<?php

namespace lhs\craft\localeSelectorField\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use lhs\craft\localeSelectorField\gql\arguments\Countries as CountriesArguments;
use lhs\craft\localeSelectorField\gql\arguments\Country as CountryArguments;
use lhs\craft\localeSelectorField\gql\interfaces\Country as CountryInterface;
use lhs\craft\localeSelectorField\gql\resolvers\Countries as CountriesResolver;
use lhs\craft\localeSelectorField\gql\resolvers\Country as CountryResolver;

class Country extends Query
{
    /**
     * Available queries
     *
     * @param bool $checkToken
     * @return array[]
     */
    public static function getQueries(bool $checkToken = true): array
    {
        return [
            'countries' => [
                'type' => Type::listOf(CountryInterface::getType()),
                'args' => CountriesArguments::getArguments(),
                'resolve' => CountriesResolver::class . '::resolve',
                'description' => 'This query is used to query for multiple countries.',
            ],
            'country' => [
                'type' => CountryInterface::getType(),
                'args' => CountryArguments::getArguments(),
                'resolve' => CountryResolver::class . '::resolve',
                'description' => 'This query is used to query for a single country.',
            ],
        ];
    }
}
