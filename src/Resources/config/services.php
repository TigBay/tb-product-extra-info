<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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
        ->set(\Tb\Core\Content\ProductExtraInfo\ProductExtraInfoDefinition::class)
        ->tag('shopware.entity.definition', [
            'entity' => 'product_extra_info',
        ]);
};
