# TbProductExtraInfo

Shopware 6 plugin for maintaining additional information per product (e.g. internal notes, marketing texts or stock hints) in the Administration and showing it on the Storefront product detail page.

## Features

- Own DAL entity `product_extra_info` (`extra_text`, `priority`) with a 1:1 association to `product`
- Card **Product additional information** on the product detail page in the Administration
- Box **Zusatzinfo** below the buy widget on the Storefront product detail page, rendered only if the text is not empty
- Variants without their own entry fall back to the entry of their parent product
- Errors while loading are logged via `Psr\Log\LoggerInterface` and never break the product page

## Requirements

- Shopware 6.7

## Installation

Clone the repository into `custom/plugins/TbProductExtraInfo` and run in the shop root:

```bash
bin/console plugin:refresh
bin/console plugin:install --activate TbProductExtraInfo
bin/console cache:clear
```

The migration creates the table `product_extra_info` automatically during installation. The built Administration assets are part of the repository. After changing files in `src/Resources/app/administration`, rebuild them with:

```bash
bin/build-administration.sh
```

Uninstalling without *keep user data* drops the table `product_extra_info`.

## Usage

1. Open a product in the Administration (*Catalogues → Products*).
2. Fill in the card **Product additional information** in the *General* tab.
3. Click **Save additional information**. The card is saved independently of the product's main save button.
4. Open the product detail page in the Storefront – the text is shown in the **Zusatzinfo** box.

The card is only shown for products that have already been saved once.

## Technical structure

| Layer | Class / file | Responsibility |
|---|---|---|
| Entity | `Core/Content/ProductExtraInfo/ProductExtraInfoDefinition`, `…Entity`, `…Collection` | DAL definition of `product_extra_info` |
| Entity extension | `Core/Content/Product/ProductExtension` | Adds the 1:1 association `productExtraInfo` to `product` (cascade delete) |
| Migration | `Migration/Migration1789625606CreateProductExtraInfoTable` | Creates the table with a unique index on `product_id` |
| Service | `Services/ProductExtraInfoService` | Loads the entry for a product ID via the repository, logs and returns `null` on errors |
| Subscriber | `Subscriber/ProductPageSubscriber` | Listens to `ProductPageLoadedEvent` and adds the entry as page extension `productExtraInfo` |
| Storefront | `Resources/views/storefront/component/buy-widget/buy-widget.html.twig` | Twig inheritance of the buy widget |
| Administration | `Resources/app/administration/src` | Component `tb-product-extra-info` and override of `sw-product-detail-base` |

## Tests

The unit tests cover the service and the subscriber and don't need a database. Run them from the shop root:

```bash
./vendor/bin/phpunit -c custom/plugins/TbProductExtraInfo/phpunit.xml
```

## Notes

- Only one entry per product is possible, enforced by the unique index on `product_id`.
- `priority` is stored and editable, but not evaluated by the Storefront yet.

## License

MIT
