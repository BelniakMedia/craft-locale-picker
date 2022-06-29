<?php

namespace lhs\craft\localeSelectorField\gql\types;

use craft\gql\base\ObjectType;
use lhs\craft\localeSelectorField\gql\interfaces\Country as CountryInterface;

class Country extends ObjectType
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            CountryInterface::getType(),
        ];

        parent::__construct($config);
    }
}
