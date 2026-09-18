# TbProductExtraInfo

Shopware 6 plugin for managing and displaying additional, translatable product information.

## Features

- Custom DAL entity `product_extra_info` with a 1:1 association to `product`
- Translatable additional text (`extra_text`) through `product_extra_info_translation`
- Management of additional text and priority in the Shopware Administration product detail page
- Display of additional information on the Storefront product detail page when a non-empty text is available
- Translated UI labels for both Administration and Storefront using snippets

## Installation

```bash
bin/console plugin:refresh
bin/console plugin:install --activate TbProductExtraInfo
bin/console cache:clear
bin/console theme:compile
```

## Usage

1. Open a product in the Shopware Administration.
2. Open the **Product additional information** card in the product settings section.
3. Enter the additional text and, optionally, a priority, then save it.
4. The additional information is displayed on the Storefront product detail page when the additional text is not empty.

## Technical Structure

- `ProductExtraInfoDefinition`: DAL definition for the additional product information
- `ProductExtraInfoTranslationDefinition`: Translations for the additional text
- `ProductExtension`: 1:1 association between a product and its additional information
- `ProductExtraInfoService`: Loads additional information by product ID
- `ProductPageSubscriber`: Adds the additional information to the Storefront product detail page
- `Resources/views`: Storefront Twig override
- `Resources/app/administration`: Administration extension

## Tests

```bash
./vendor/bin/phpunit -c custom/plugins/TbProductExtraInfo/phpunit.xml
```

## Notes

- Only one additional-information record is allowed per product. This is enforced by a unique index on `product_id`.
- The additional text is stored per language and rendered in the Storefront using Shopware’s language fallback chain.