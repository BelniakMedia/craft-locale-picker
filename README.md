# Craft - Locale selector

This plugin adds a new "Country or Site" field type to the Craft CMS. 
The Country or site field allows content editors to choose from a list of link types and offers individual input fields for each of them.

## Requirements

This plugin requires Craft CMS 4.0.0 or later.


## Installation

The plugin can be installed from the integrated plugin store by searching for 
"Locale selector" or using Composer:
1. Open your terminal and navigate to your Craft project:
    ```shell
    cd /path/to/project
    ```
2. Then tell Composer to load the plugin:
    ```shell
    composer require la-haute-societe/craft-locale-selector
    ```
3. Finally, install and enable the plugin:
    ```shell
    ./craft plugin/install locale-selector
    ./craft plugin/enable locale-selector
    ```


## Usage

After the plugin has been installed, "Country or site" fields can be created 
using the field settings within the control panel. 
All field settings can be found within the field manager.


## Templating

### Countries

Country fields can be rendered directly in Twig, they return the localized name 
of the selected country. 

```twig
{{ entry.myCountryField }}
```

The field value is actually an instance of 
`\lhs\craft\localeSelectorField\models\CountryModel` which exposes additional 
properties and methods that can be used in templates.

### Sites

Site fields can be rendered directly in Twig, they return the name of the site.

```twig
{{ entry.mySiteField }}
```

The field value is actually an instance of `\craft\models\Site` which exposes
additional properties and methods that can be used in templates.


## GraphQL

GraphQL is supported 🎉

### Sites

Sites expose the following properties:

- `id` - `[string]`: The ID of the site
- `name` - `[string]`: The name of the site
- `language` - `[string]`: The language code of the site as selected in the CP (example: `fr-FR` or `en`)
- `baseUrl` - `[string]`: The site base URL
- `handle` - `[string]`: The site handle

### Countries

Countries expose the following properties: 

- `name` - `[string]`: The localized name of the country
- `nativeName` - `[string]`: The name of the country, in the country main language
- `iso2` - `[string]`: The 2-letter country code, according to [ISO 3166][]
- `iso3` - `[string]`: The 3-letter country code, according to [ISO 3166][]

[ISO 3166]: https://www.iso.org/iso-3166-country-codes.html


## Roadmap

- Add the ability to select languages
- Add the ability to select locales (language + country)

Brought to you by
<a href="https://www.lahautesociete.com" target="_blank"><br><img src=".readme/logo-lahautesociete.png" height="100" alt="Logo La Haute Société" /></a>
