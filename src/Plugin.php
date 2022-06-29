<?php

namespace lhs\craft\localeSelectorField;

use craft\base\Plugin as BasePlugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\services\Fields;
use craft\services\Gql;
use lhs\craft\localeSelectorField\fields\LocalesSelectorField;
use lhs\craft\localeSelectorField\gql\queries\Country as CountriesQuery;
use lhs\craft\localeSelectorField\gql\queries\Site as SitesQuery;
use lhs\craft\localeSelectorField\services\CountriesService;
use yii\base\Event;

/**
 * The main Craft plugin class.
 *
 * @property CountriesService $countriesService
 */
class Plugin extends BasePlugin
{
    public string $sourceLanguage = 'none'; // Translation files are translation code, they're not written in any specific language and not meant to be displayed

    /**
     * Data types available
     */
    public const DATATYPE_SITES = 1;
    public const DATATYPE_COUNTRIES = 2;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Register the field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = LocalesSelectorField::class;
            }
        );

        // Register services
        $this->set('countriesService', CountriesService::class);

        // Register GraphQL queries
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_QUERIES,
            static function (RegisterGqlQueriesEvent $event) {
                $event->queries = array_merge($event->queries, SitesQuery::getQueries());
                $event->queries = array_merge($event->queries, CountriesQuery::getQueries());
            }
        );
    }
}
