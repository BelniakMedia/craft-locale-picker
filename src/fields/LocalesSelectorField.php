<?php

namespace lhs\craft\localeSelectorField\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\errors\InvalidFieldException;
use craft\helpers\ArrayHelper;
use craft\models\Site;
use Exception;
use GraphQL\Type\Definition\Type;
use lhs\craft\localeSelectorField\gql\interfaces\Country as CountryInterface;
use lhs\craft\localeSelectorField\gql\interfaces\Site as SiteInterface;
use lhs\craft\localeSelectorField\models\CountryModel;
use lhs\craft\localeSelectorField\Plugin;
use Rinvex\Country\CountryLoaderException;
use yii\db\Schema;
use function Arrayy\array_first;

/**
 * This field allows a selection from a configured set of sites.
 */
class LocalesSelectorField extends Field implements PreviewableFieldInterface
{
    /**
     * @var int Which datatype is currently used ?
     */
    public int $datatype = Plugin::DATATYPE_SITES;

    /**
     * @var string[]|string What sites have been whitelisted as selectable for this field.
     */
    public array|string $whitelistedSites = '*';

    /**
     * @var array What countries have been whitelisted as selectable for this field.
     */
    public array $whitelistedCountries = [];

    /**
     * @inheritdoc
     * @see craft\base\ComponentInterface
     */
    public static function displayName(): string
    {
        return Craft::t('locale-selector', 'input.field.label');
    }

    /**
     * @inheritdoc
     * @see craft\base\Field
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     * @see craft\base\SavableComponentInterface
     */
    public function getSettingsHtml(): string
    {
        /**
         * Render template
         */
        return Craft::$app->getView()->renderTemplate(
            'locale-selector/cp/_settings.html.twig', [
                'availableDatatypes' => $this->getAvailableDataTypes(),
                'currentDatatype' => $this->datatype,
                'availableSites' => $this->getAvailableSites(), // Get all sites available to the current user
                'whitelistedSites' => $this->whitelistedSites,
                'availableCountries' => Plugin::getInstance()->countriesService->getCountries(
                    true,
                    Craft::$app->getUser()->getIdentity()->getPreferredLanguage()
                ), // Get all countries, localized
                'whitelistedCountries' => $this->whitelistedCountries,
            ]
        );
    }

    /**
     * @inheritdoc
     * @see craft\base\Field
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['whitelistedSites'], 'validateSitesWhitelist'];

        return $rules;
    }

    /**
     * @inheritdoc
     * @param $value
     * @param ElementInterface|null $element
     * @return string
     * @throws Exception
     * @see craft\base\Field
     */
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        /**
         * Retrieve useful data according to the type
         */
        switch ($this->datatype) {
            case Plugin::DATATYPE_SITES:
                // Get all sites available to the current user
                $data = $this->getAvailableSites();

                // Get all whitelisted sites.
                $whitelist = $this->whitelistedSites === '*'
                    ? ArrayHelper::getColumn($data, 'handle')
                    : array_flip($this->whitelistedSites);

                //Transform the value (related to the data given by "normalizeValue()")
                $value = is_null($value) ? null : $value->handle;
                break;

            case Plugin::DATATYPE_COUNTRIES:
                //Get all countries
                $data = Plugin::getInstance()->countriesService->getCountries(
                    true,
                    Craft::$app->getUser()->getIdentity()->getPreferredLanguage()
                );

                // Get all whitelisted countries.
                $whitelist = array_flip($this->whitelistedCountries);

                //Transform the value (related to the data given by "normalizeValue()")
                $value = is_null($value) ? null : $value->getIso2();
                break;
            default:
                throw new Exception('Invalid datatype'); // TODO: Use a custom exception
        }


        // Add a blank entry in, in case the field's options allow a 'None' selection.
        $whitelist[''] = true;

        // TODO: Ensure none is the first element of the dropdown
        if (!$this->required) {
            // Add a 'None' option specifically for optional, single value fields.
            $data = ['' => Craft::t('app', 'None')] + $data;
        }
        $whitelist = array_intersect_key($data, $whitelist); // Discard any sites not available within the whitelist.

        /**
         * Render template
         */
        return Craft::$app->getView()->renderTemplate(
            'locale-selector/cp/_input.html.twig', [
                'field' => $this,
                'value' => $value,
                'data' => $whitelist,
            ]
        );
    }

    /**`
     * Normalize the value read from the database
     *
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return array|mixed|CountryModel|void|null
     * @throws Exception
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if (is_null($value)) {
            return null;
        }
        if (is_object($value) || is_array($value)) {
            return $value;
        }

        switch ($this->datatype) {
            case Plugin::DATATYPE_SITES:
                return Craft::$app->sites->getSiteByHandle($value);
            case Plugin::DATATYPE_COUNTRIES:
                try {
                    return Plugin::getInstance()->countriesService->getCountryByISO($value);
                } catch (CountryLoaderException $e) {
                    return null;
                }
            default:
                throw new Exception('Invalid datatype!'); // TODO: Use a custom exception
        }
    }

    /**`
     * Serialize the value for recording in database
     *
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string|null
     * @throws Exception
     */
    public function serializeValue(mixed $value, ?ElementInterface $element = null): ?string
    {
        return match ($this->datatype) {
            Plugin::DATATYPE_SITES => ($value instanceof Site) ? $value->handle : null,
            Plugin::DATATYPE_COUNTRIES => ($value instanceof CountryModel) ? $value->getIso2() : null,
            default => throw new Exception('Invalid datatype!'), // TODO: Use a custom exception
        };
    }

    /**
     * @inheritdoc
     * @see craft\base\Field
     */
    public function getElementValidationRules(): array
    {
        //If the current datatype is "Sites", we apply a validation rule
        if ($this->datatype === Plugin::DATATYPE_SITES) {
            return [
                ['validateSites'],
            ];
        }

        return [];
    }

    /**
     * Ensures the site IDs selected for the whitelist are for valid sites.
     *
     * @param string $attribute The name of the attribute being validated.
     * @return void
     */
    public function validateSitesWhitelist(string $attribute): void
    {
        if (!is_array($this->whitelistedSites)) {
            if ($this->whitelistedSites !== '*') {
                $this->addError($attribute, Craft::t('locale-selector', 'input.errors.invalid-site'));
            }
            return;
        }

        if (count($this->whitelistedSites) && array_first($this->whitelistedSites) === '*') {
            return;
        }

        $sites = $this->getAvailableSites();
        foreach ($this->whitelistedSites as $site) {
            if (!isset($sites[$site])) {
                $this->addError($attribute, Craft::t('locale-selector', 'input.errors.invalid-site'));
            }
        }
    }



    /**
     * Ensures the site IDs selected are available to the current user.
     *
     * @param ElementInterface $element The element with the value being validated.
     * @return void
     * @throws InvalidFieldException
     */
    public function validateSites(ElementInterface $element): void
    {
        $value = $element->getFieldValue($this->handle);
        $sites = $this->getAvailableSites();
        if (isset($value->handle) && !isset($sites[$value->handle])) {
            $element->addError($this->handle, Craft::t('locale-selector', 'input.errors.invalid-site'));
        }
    }

    /**
     * Get the list of available data types, with associated labels
     *
     * @return array
     */
    private function getAvailableDataTypes(): array
    {
        return [
            Plugin::DATATYPE_SITES => Craft::t('locale-selector', 'settings.datatype.sites-label'),
            Plugin::DATATYPE_COUNTRIES => Craft::t('locale-selector', 'settings.datatype.countries-label'),
        ];
    }

    /**
     * Retrieves all sites in an id, name pair, suitable for the underlying options display.
     *
     * @return array
     */
    private function getAvailableSites(): array
    {
        $sites = [];
        foreach (Craft::$app->getSites()->getAllSites() as $site) {
            $sites[$site->handle] = Craft::t('site', $site->name);
        }
        return $sites;
    }

    /**
     * Get the GQL Type of the data
     *
     * @throws Exception
     */
    public function getContentGqlType(): array|Type
    {
        return match ($this->datatype) {
            Plugin::DATATYPE_SITES => SiteInterface::getType(),
            Plugin::DATATYPE_COUNTRIES => CountryInterface::getType(),
            default => throw new Exception('Invalid datatype!'), // TODO: Use a custom exception
        };
    }

    public function searchKeywords($value, ElementInterface $element): string
    {
        /** @var CountryModel|Site $value */

        if ($value instanceof CountryModel) {
            return $value->getIso2();
        }

        if ($value instanceof Site) {
            return $value->handle;
        }

        return parent::searchKeywords($value, $element);
    }

    public function beforeSave(bool $isNew): bool
    {
        if (is_array($this->whitelistedSites) && count($this->whitelistedSites) === 1 && array_first($this->whitelistedSites) === '*') {
            $this->whitelistedSites = '*';
        }

        return parent::beforeSave($isNew);
    }
}
