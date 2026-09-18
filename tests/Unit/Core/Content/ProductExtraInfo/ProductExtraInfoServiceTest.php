<?php declare(strict_types=1);

namespace Tb\Tests\Unit\Core\Content\ProductExtraInfo;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoCollection;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;
use Tb\Services\ProductExtraInfoService;
use Tb\Tests\Unit\TestLog;

#[CoversClass(ProductExtraInfoService::class)]
final class ProductExtraInfoServiceTest extends TestCase
{
    use TestLog;

    public function testGetByProductIdReturnsTheFoundExtraInfo(): void
    {
        $extraText = 'Zusatzinformation für das Testprodukt.';
        $productId = 'c7bca22753c84d08b6178a50052b4146';
        $context = Context::createCLIContext();

        $extraInfo = new ProductExtraInfoEntity();
        $extraInfo->setId('01aeaedc3c3799dd6a55258c301b834');
        $extraInfo->setProductId($productId);
        $extraInfo->setExtraText($extraText);
        $extraInfo->setPriority(123);

        $collection = new ProductExtraInfoCollection([
            $extraInfo,
        ]);

        $searchResult = new EntitySearchResult(
            'product_extra_info',
            1,
            $collection,
            null,
            new Criteria(),
            $context,
        );

        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('search')
            ->with(
                self::callback(
                    static function (Criteria $criteria) use ($productId): bool {
                        $filters = $criteria->getFilters();

                        if (count($filters) !== 1) {
                            return false;
                        }

                        $filter = $filters[0];

                        return $filter instanceof EqualsFilter
                            && $filter->getField() === 'productId'
                            && $filter->getValue() === $productId;
                    },
                ),
                self::identicalTo($context),
            )
            ->willReturn($searchResult);

        $this->testLog->reset();
        $service = new ProductExtraInfoService($repository, $this->log);

        $result = $service->getByProductId($productId, $context);

        self::assertSame($extraInfo, $result);
        self::assertSame(
            $extraText,
            $result?->getExtraText(),
        );
        self::assertSame(123, $result?->getPriority());

        $this->assertCount(0, $this->testLog->getRecords(), 'Only expected 0 log message');
        $this->assertFalse($this->testLog->hasErrorThatMatches('/TbProductExtraInfo/'));
    }

    public function testGetByProductIdReturnsNullWhenNoExtraInfoExists(): void
    {
        $productId = 'c7bca22753c84d08b6178a50052b4146';
        $context = Context::createCLIContext();

        $searchResult = new EntitySearchResult(
            'product_extra_info',
            0,
            new ProductExtraInfoCollection(),
            null,
            new Criteria(),
            $context,
        );

        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('search')
            ->willReturn($searchResult);

        $service = new ProductExtraInfoService($repository, $this->log);

        $result = $service->getByProductId($productId, $context);

        self::assertNull($result);
    }
}