<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tb\Core\Content\Product\ProductExtension;
use Tb\Core\Content\ProductExtraInfo\ProductExtraInfoDefinition;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('Tb\\', '../../')
        ->exclude('../../{Resources,Migration,*.php}');

    $services
        ->set(ProductExtraInfoDefinition::class)
        ->tag('shopware.entity.definition', [
            'entity' => 'product_extra_info',
        ]);

    $services
        ->set(ProductExtension::class)
        ->tag('shopware.entity.extension');
};
