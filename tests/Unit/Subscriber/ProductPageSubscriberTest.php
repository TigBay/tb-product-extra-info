<?php declare(strict_types=1);

namespace Tb\Tests\Unit\Subscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPage;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoCollection;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoEntity;
use Tb\Services\ProductExtraInfoService;
use Tb\Subscriber\ProductPageSubscriber;

#[CoversClass(ProductPageSubscriber::class)]
final class ProductPageSubscriberTest extends TestCase
{
    private const PRODUCT_ID = 'c7bca22753c84d08b6178a50052b4146';
    private const PARENT_ID = '4f9d56b1559d0125c2be298840cb4db7';

    public function testSubscribesToProductPageLoadedEvent(): void
    {
        self::assertArrayHasKey(ProductPageLoadedEvent::class, ProductPageSubscriber::getSubscribedEvents());
    }

    public function testAddsExtraInfoToPage(): void
    {
        $extraInfo = $this->createExtraInfo(self::PRODUCT_ID, 'Nur solange der Vorrat reicht.');
        $event = $this->createEvent(self::PRODUCT_ID);

        $this->createSubscriber([self::PRODUCT_ID => $extraInfo])->onProductPageLoaded($event);

        self::assertSame($extraInfo, $event->getPage()->getExtension('productExtraInfo'));
    }

    public function testFallsBackToParentProductForVariants(): void
    {
        $parentExtraInfo = $this->createExtraInfo(self::PARENT_ID, 'Gilt für alle Varianten.');
        $event = $this->createEvent(self::PRODUCT_ID, self::PARENT_ID);

        $this->createSubscriber([self::PARENT_ID => $parentExtraInfo])->onProductPageLoaded($event);

        self::assertSame($parentExtraInfo, $event->getPage()->getExtension('productExtraInfo'));
    }

    public function testDoesNotAddExtensionWhenNoExtraInfoExists(): void
    {
        $event = $this->createEvent(self::PRODUCT_ID);

        $this->createSubscriber([])->onProductPageLoaded($event);

        self::assertFalse($event->getPage()->hasExtension('productExtraInfo'));
    }

    public function testDoesNotAddExtensionWhenExtraTextIsBlank(): void
    {
        $event = $this->createEvent(self::PRODUCT_ID);

        $this->createSubscriber([self::PRODUCT_ID => $this->createExtraInfo(self::PRODUCT_ID, "  \n ")])
            ->onProductPageLoaded($event);

        self::assertFalse($event->getPage()->hasExtension('productExtraInfo'));
    }

    /**
     * @param array<string, ProductExtraInfoEntity> $extraInfoByProductId
     */
    private function createSubscriber(array $extraInfoByProductId): ProductPageSubscriber
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository
            ->method('search')
            ->willReturnCallback(static function (Criteria $criteria, Context $context) use ($extraInfoByProductId): EntitySearchResult {
                $filter = $criteria->getFilters()[0];
                \assert($filter instanceof EqualsFilter);

                $productId = $filter->getValue();
                \assert(\is_string($productId));

                $extraInfo = $extraInfoByProductId[$productId] ?? null;
                $collection = new ProductExtraInfoCollection($extraInfo ? [$extraInfo] : []);

                return new EntitySearchResult('product_extra_info', $collection->count(), $collection, null, $criteria, $context);
            });

        return new ProductPageSubscriber(new ProductExtraInfoService($repository, new NullLogger()));
    }

    private function createEvent(string $productId, ?string $parentId = null): ProductPageLoadedEvent
    {
        $product = new SalesChannelProductEntity();
        $product->setId($productId);
        $product->setParentId($parentId);

        $page = new ProductPage();
        $page->setProduct($product);

        $salesChannelContext = $this->createStub(SalesChannelContext::class);
        $salesChannelContext->method('getContext')->willReturn(Context::createCLIContext());

        return new ProductPageLoadedEvent($page, $salesChannelContext, new Request());
    }

    private function createExtraInfo(string $productId, string $extraText): ProductExtraInfoEntity
    {
        $extraInfo = new ProductExtraInfoEntity();
        $extraInfo->setId(md5($productId));
        $extraInfo->setProductId($productId);
        $extraInfo->setExtraText($extraText);

        return $extraInfo;
    }
}
