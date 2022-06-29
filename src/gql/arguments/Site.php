<?php

namespace lhs\craft\localeSelectorField\gql\arguments;

use GraphQL\Type\Definition\Type;

class Site
{
    /**
     * Arguments available
     *
     * @return array[]
     */
    public static function getArguments(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::id(),
                'description' => 'Narrows query results based on the ID of the site.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'Narrows query results based on the handle of the site.',
            ],
        ];
    }
}
