<?php declare(strict_types=1);

namespace Tb\Core\Content\ProductExtraInfo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ProductExtraInfoEntity>
 */
class ProductExtraInfoCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductExtraInfoEntity::class;
    }
}
