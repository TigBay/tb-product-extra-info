<?php

namespace Tb\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;

readonly class ProductExtraInfoService
{
    public function __construct(
        #[Autowire(service: 'product_extra_info.repository')]
        private EntityRepository $productExtraInfoRepository
    )
    {

    }

    public function getByProductId(
        string  $productId,
        Context $context,
    ): ?ProductExtraInfoEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('productId', $productId),
        );

        /** @var ProductExtraInfoEntity|null $extraInfo */
        $extraInfo = $this->productExtraInfoRepository
            ->search($criteria, $context)
            ->getEntities()
            ->first();

        return $extraInfo;
    }
}