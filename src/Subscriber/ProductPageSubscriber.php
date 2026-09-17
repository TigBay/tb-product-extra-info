<?php declare(strict_types=1);

namespace Tb\Subscriber;

use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tb\Services\ProductExtraInfoService;

readonly class ProductPageSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private ProductExtraInfoService $productExtraInfoService
    )
    {

    }

    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $productId = $product->getId();

        $extraInfo = $this->productExtraInfoService->getByProductId($productId, $event->getContext());

        if ($extraInfo === null && $product->getParentId() !== null) {
            $extraInfo = $this->productExtraInfoService->getByProductId(
                $product->getParentId(),
                $event->getContext(),
            );
        }

        if ($extraInfo === null || trim((string)$extraInfo->getExtraText()) === '') {
            return;
        }

        $event->getPage()->addExtension(
            'productExtraInfo',
            $extraInfo,
        );
    }
}
