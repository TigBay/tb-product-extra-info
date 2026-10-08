<?php declare(strict_types=1);

namespace Tb\Tests\Unit\Core\Content\ProductExtraInfo;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoCollection;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;
use Tb\Services\ProductExtraInfoService;

#[CoversClass(ProductExtraInfoService::class)]
final class ProductExtraInfoServiceTest extends TestCase
{
    private const PRODUCT_ID = 'c7bca22753c84d08b6178a50052b4146';

    private Context $context;

    protected function setUp(): void
    {
        $this->context = Context::createCLIContext();
    }

    public function testGetByProductIdReturnsTheFoundExtraInfo(): void
    {
        $extraInfo = new ProductExtraInfoEntity();
        $extraInfo->setId('01aeaedc3c3799dd6a55258c301b8340');
        $extraInfo->setProductId(self::PRODUCT_ID);
        $extraInfo->setExtraText('Zusatzinformation für das Testprodukt.');
        $extraInfo->setPriority(123);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects(self::once())
            ->method('search')
            ->with(
                self::callback(static function (Criteria $criteria): bool {
                    $filters = $criteria->getFilters();

                    return \count($filters) === 1
                        && $filters[0] instanceof EqualsFilter
                        && $filters[0]->getField() === 'productId'
                        && $filters[0]->getValue() === self::PRODUCT_ID;
                }),
                self::identicalTo($this->context),
            )
            ->willReturn($this->createSearchResult($extraInfo));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('error');

        $result = (new ProductExtraInfoService($repository, $logger))->getByProductId(self::PRODUCT_ID, $this->context);

        self::assertSame($extraInfo, $result);
        self::assertSame('Zusatzinformation für das Testprodukt.', $result?->getExtraText());
        self::assertSame(123, $result?->getPriority());
    }

    public function testGetByProductIdReturnsNullWhenNoExtraInfoExists(): void
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('search')->willReturn($this->createSearchResult());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('error');

        $result = (new ProductExtraInfoService($repository, $logger))->getByProductId(self::PRODUCT_ID, $this->context);

        self::assertNull($result);
    }

    public function testGetByProductIdReturnsNullAndLogsWhenRepositoryFails(): void
    {
        $exception = new RuntimeException('Database not reachable');

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('search')->willThrowException($exception);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with(
                self::stringContains('TbProductExtraInfo'),
                self::identicalTo([
                    'productId' => self::PRODUCT_ID,
                    'exception' => $exception,
                ]),
            );

        $result = (new ProductExtraInfoService($repository, $logger))->getByProductId(self::PRODUCT_ID, $this->context);

        self::assertNull($result);
    }

    /**
     * @return EntitySearchResult<ProductExtraInfoCollection>
     */
    private function createSearchResult(ProductExtraInfoEntity ...$entities): EntitySearchResult
    {
        return new EntitySearchResult(
            'product_extra_info',
            \count($entities),
            new ProductExtraInfoCollection($entities),
            null,
            new Criteria(),
            $this->context,
        );
    }
}
