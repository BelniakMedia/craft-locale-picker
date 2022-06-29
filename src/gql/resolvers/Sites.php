<?php

namespace lhs\craft\localeSelectorField\gql\resolvers;

use Craft;
use craft\gql\base\Resolver;
use GraphQL\Type\Definition\ResolveInfo;

class Sites extends Resolver
{
    /**
     * Fetch the data as requested
     *
     * @param mixed $source
     * @param array $arguments
     * @param mixed $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    public static function resolve(mixed $source, array $arguments, mixed $context, ResolveInfo $resolveInfo): array
    {
        return Craft::$app->getSites()->getAllSites(false);
    }
}
