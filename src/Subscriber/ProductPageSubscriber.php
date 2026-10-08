<?php declare(strict_types=1);

namespace Tb\Subscriber;

use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tb\Services\ProductExtraInfoService;

readonly class ProductPageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductExtraInfoService $productExtraInfoService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $context = $event->getContext();

        $extraInfo = $this->productExtraInfoService->getByProductId($product->getId(), $context);

        if ($extraInfo === null && $product->getParentId() !== null) {
            $extraInfo = $this->productExtraInfoService->getByProductId($product->getParentId(), $context);
        }

        if ($extraInfo === null || trim((string) $extraInfo->getExtraText()) === '') {
            return;
        }

        $event->getPage()->addExtension('productExtraInfo', $extraInfo);
    }
}
