<?php

namespace lhs\craft\localeSelectorField\gql\interfaces;

use Craft;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use lhs\craft\localeSelectorField\gql\types\Site as SiteType;

class Site
{
    public static function getName(): string
    {
        return 'SiteInterface';
    }

    public static function getType(): Type
    {
        // Return the type if it’s already been created
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        // Otherwise create the type via the entity registry, which handles prefixing
        $type = GqlEntityRegistry::createEntity(
            self::getName(),
            new InterfaceType([
                'name' => static::getName(),
                'fields' => self::class . '::getFieldDefinitions',
                'description' => 'This is the interface implemented by all sites.',
                'resolveType' => self::class . '::resolveElementTypeName',
            ])
        );

        self::generateType();

        return $type;
    }

    public static function resolveElementTypeName(): string
    {
        return GqlEntityRegistry::prefixTypeName('SiteType');
    }

    public static function getFieldDefinitions(): array
    {
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => Type::id(),
                'description' => 'Numeric ID of the site.',
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Human-readable site name.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'Handle of the site.',
            ],
            'baseUrl' => [
                'name' => 'baseUrl',
                'type' => Type::string(),
                'description' => 'Base URL of the site.',
            ],
            'language' => [
                'name' => 'language',
                'type' => Type::string(),
                'description' => 'Language of the site of the site as defined in Craft.',
            ],
        ];

        /** @noinspection PhpDeprecationInspection */
        return version_compare(Craft::$app->getVersion(), '4.0.0', '>=') ?
            TypeManager::prepareFieldDefinitions($fields, self::getName())
            : Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }


    public static function generateType(): ObjectType
    {
        $typeName = 'SiteType';
        if (version_compare(Craft::$app->getVersion(), '4.0.0', '>=')) {
            $fields = Craft::$app->getGql()->prepareFieldDefinitions(
                self::getFieldDefinitions(),
                $typeName
            );
        } else {
            /** @noinspection PhpDeprecationInspection */
            $fields = TypeManager::prepareFieldDefinitions(
                self::getFieldDefinitions(),
                $typeName
            );
        }

        // Return the type if it exists, otherwise create and return it
        return GqlEntityRegistry::getEntity($typeName) ?:
            GqlEntityRegistry::createEntity(
                $typeName,
                new SiteType([
                    'name' => $typeName,
                    'fields' => function () use ($fields) {
                        return $fields;
                    },
                ])
            );
    }
}
