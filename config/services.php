<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Hargreaves\HolidayProvider\HolidayProvider;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(HolidayProvider::class)
        ->arg('$holidayData', '%hargreaves_holiday_provider.data%');
};
