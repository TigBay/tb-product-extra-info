<?php declare(strict_types=1);

namespace Tb\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1789625606CreateProductExtraInfoTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1789625606;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `product_extra_info` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL,
                `extra_text` LONGTEXT NULL,
                `active` INT NOT NULL DEFAULT 0,
                `priority` INT NOT NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_product_id` (`product_id`),
                CONSTRAINT `fk.product_extra_info.product_id`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `product` (`id`)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
