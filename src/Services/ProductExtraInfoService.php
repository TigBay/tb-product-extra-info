<?php

namespace Tb\Services;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;
use Throwable;

readonly class ProductExtraInfoService
{
    public function __construct(
        #[Autowire(service: 'product_extra_info.repository')]
        private EntityRepository $productExtraInfoRepository,
        private LoggerInterface  $logger
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

        try {
            /** @var ProductExtraInfoEntity|null $extraInfo */
            $extraInfo = $this->productExtraInfoRepository
                ->search($criteria, $context)
                ->getEntities()
                ->first();
        } catch (Throwable $exception) {
            $this->logger->error('TbProductExtraInfo - ProductExtraInfoService ' . $exception->getMessage());
            return null;
        }

        return $extraInfo;
    }
}