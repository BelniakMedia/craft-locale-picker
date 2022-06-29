<?php

namespace lhs\craft\localeSelectorField\gql\arguments;

use GraphQL\Type\Definition\Type;

class Countries
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
        ];
    }
}
