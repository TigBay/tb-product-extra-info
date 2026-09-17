<?php declare(strict_types=1);

namespace Tb\Core\Content\ProductExtraInfo;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ProductExtraInfoEntity $entity)
 * @method void set(string $key, ProductExtraInfoEntity $entity)
 * @method ProductExtraInfoEntity[] getIterator()
 * @method ProductExtraInfoEntity[] getElements()
 * @method ProductExtraInfoEntity|null get(string $key)
 * @method ProductExtraInfoEntity|null first()
 * @method ProductExtraInfoEntity|null last()
 */
class ProductExtraInfoCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductExtraInfoEntity::class;
    }
}
