<?php

namespace lhs\craft\localeSelectorField\models;

use craft\base\Model;
use Stringable;

/**
 * NormalizedCountry class
 */
class CountryModel extends Model implements Stringable
{
    /**
     * @var string $name
     */
    private string $name;

    /**
     * @var string $nativeName
     */
    private string $nativeName;

    /**
     * @var string $iso2
     */
    private string $iso2;

    /**
     * @var string $iso3
     */
    private string $iso3;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CountryModel
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNativeName(): string
    {
        return $this->nativeName;
    }

    /**
     * @param string $nativeName
     * @return CountryModel
     */
    public function setNativeName(string $nativeName): self
    {
        $this->nativeName = $nativeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getIso2(): string
    {
        return $this->iso2;
    }

    /**
     * @param string $iso2
     * @return CountryModel
     */
    public function setIso2(string $iso2): self
    {
        $this->iso2 = $iso2;
        return $this;
    }

    /**
     * @return string
     */
    public function getIso3(): string
    {
        return $this->iso3;
    }

    /**
     * @param string $iso3
     * @return CountryModel
     */
    public function setIso3(string $iso3): self
    {
        $this->iso3 = $iso3;
        return $this;
    }
}
