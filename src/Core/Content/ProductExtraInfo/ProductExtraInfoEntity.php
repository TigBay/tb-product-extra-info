<?php declare(strict_types=1);

namespace Tb\Core\Content\ProductExtraInfo;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductExtraInfoEntity extends Entity
{
    use EntityIdTrait;

    protected string $productId;

    protected ?ProductEntity $product = null;

    protected ?string $extraText = null;

    protected int $priority = 0;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getExtraText(): ?string
    {
        return $this->extraText;
    }

    public function setExtraText(?string $extraText): self
    {
        $this->extraText = $extraText;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): self
    {
        $this->product = $product;

        return $this;
    }
}
