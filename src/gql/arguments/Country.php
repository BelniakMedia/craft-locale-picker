<?php

namespace lhs\craft\localeSelectorField\gql\arguments;

use GraphQL\Type\Definition\Type;

class Country
{
    /**
     * Arguments available
     *
     * @return array[]
     */
    public static function getArguments(): array
    {
        return [
            'site' => [
                'name' => 'site',
                'type' => Type::string(),
                'description' => 'Localize query results based on the site handle provided.',
            ],
            'iso2' => [
                'name' => 'iso2',
                'type' => Type::string(),
                'description' => 'Narrows query results based on the ISO2 of the country.',
            ],
        ];
    }
}
