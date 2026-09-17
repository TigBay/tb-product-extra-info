<?php declare(strict_types=1);

namespace Tb\Core\Content\ProductExtraInfo;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductExtraInfoEntity extends Entity
{
    use EntityIdTrait;

    protected string $productId;

    protected ?string $extraText = null;

    protected bool $active;

    protected int $priority;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): ProductExtraInfoEntity
    {
        $this->productId = $productId;
        return $this;
    }

    public function getExtraText(): ?string
    {
        return $this->extraText;
    }

    public function setExtraText(?string $extraText): ProductExtraInfoEntity
    {
        $this->extraText = $extraText;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): ProductExtraInfoEntity
    {
        $this->priority = $priority;
        return $this;
    }
}
