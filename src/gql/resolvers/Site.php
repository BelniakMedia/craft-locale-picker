<?php

namespace lhs\craft\localeSelectorField\gql\resolvers;

use Craft;
use craft\gql\base\Resolver;
use craft\models\Site as SiteModel;
use GraphQL\Type\Definition\ResolveInfo;

class Site extends Resolver
{
    /**
     * Fetch the data as requested
     *
     * @param mixed $source
     * @param array $arguments
     * @param mixed $context
     * @param ResolveInfo $resolveInfo
     * @return SiteModel|null
     */
    public static function resolve(mixed $source, array $arguments, mixed $context, ResolveInfo $resolveInfo): ?SiteModel
    {
        if ($id = $arguments['id'] ?? null) {
            $site = Craft::$app->getSites()->getSiteById($id);
        }

        if ($handle = $arguments['handle'] ?? null) {
            $site = Craft::$app->getSites()->getSiteByHandle($handle);
        }

        return $site ?? null;
    }
}
