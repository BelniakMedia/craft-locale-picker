<?php

namespace lhs\craft\localeSelectorField\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use lhs\craft\localeSelectorField\gql\arguments\Site as SiteArguments;
use lhs\craft\localeSelectorField\gql\interfaces\Site as SiteInterface;
use lhs\craft\localeSelectorField\gql\resolvers\Site as SiteResolver;
use lhs\craft\localeSelectorField\gql\resolvers\Sites as SitesResolver;

class Site extends Query
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
            'sites' => [
                'type' => Type::listOf(SiteInterface::getType()),
                'resolve' => SitesResolver::class . '::resolve',
                'description' => 'This query is used to query for multiple sites.',
            ],
            'site' => [
                'type' => SiteInterface::getType(),
                'args' => SiteArguments::getArguments(),
                'resolve' => SiteResolver::class . '::resolve',
                'description' => 'This query is used to query for a single site.',
            ],
        ];
    }
}
