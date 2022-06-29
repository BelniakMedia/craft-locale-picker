<?php

namespace lhs\craft\localeSelectorField\gql\types;

use craft\gql\base\ObjectType;
use lhs\craft\localeSelectorField\gql\interfaces\Site as SiteInterface;

class Site extends ObjectType
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SiteInterface::getType(),
        ];

        parent::__construct($config);
    }
}
