<?php declare(strict_types=1);

namespace Tb\Services;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoCollection;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;
use Throwable;

readonly class ProductExtraInfoService
{
    /**
     * @param EntityRepository<ProductExtraInfoCollection> $productExtraInfoRepository
     */
    public function __construct(
        #[Autowire(service: 'product_extra_info.repository')]
        private EntityRepository $productExtraInfoRepository,
        private LoggerInterface $logger,
    ) {}

    public function getByProductId(string $productId, Context $context): ?ProductExtraInfoEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->setLimit(1);

        try {
            return $this->productExtraInfoRepository
                ->search($criteria, $context)
                ->getEntities()
                ->first();
        } catch (Throwable $exception) {
            $this->logger->error('TbProductExtraInfo: loading product extra info failed', [
                'productId' => $productId,
                'exception' => $exception,
            ]);

            return null;
        }
    }
}
