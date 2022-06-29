<?php

namespace lhs\craft\localeSelectorField\gql\interfaces;

use Craft;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use lhs\craft\localeSelectorField\gql\types\Country as CountryType;

class Country
{
    public static function getName(): string
    {
        return 'CountryInterface';
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
                'description' => 'This is the interface implemented by all countries.',
                'resolveType' => self::class . '::resolveElementTypeName',
            ])
        );

        self::generateType();

        return $type;
    }

    public static function resolveElementTypeName(): string
    {
        return GqlEntityRegistry::prefixTypeName('CountryType');
    }

    public static function getFieldDefinitions(): array
    {
        $fields = [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'Human-readable country name.',
            ],
            'nativeName' => [
                'name' => 'nativeName',
                'type' => Type::string(),
                'description' => 'Human-readable country native name.',
            ],
            'iso2' => [
                'name' => 'iso2',
                'type' => Type::string(),
                'description' => 'ISO2 of the country.',
            ],
            'iso3' => [
                'name' => 'iso3',
                'type' => Type::string(),
                'description' => 'ISO3 of the country.',
            ],
        ];

        /** @noinspection PhpDeprecationInspection */
        return version_compare(Craft::$app->getVersion(), '4.0.0', '>=') ?
            TypeManager::prepareFieldDefinitions($fields, self::getName())
            : Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }


    public static function generateType(): ObjectType
    {
        $typeName = 'CountryType';
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
        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity(
            $typeName,
            new CountryType([
                'name' => $typeName,
                'fields' => function () use ($fields) {
                    return $fields;
                },
            ])
        );
    }
}
