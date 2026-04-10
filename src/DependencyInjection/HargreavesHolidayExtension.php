<?php

namespace Hargreaves\HolidayProvider\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;

class HargreavesHolidayExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );

        $loader->load('services.php');

        $holidayPath = __DIR__ . '/../../resources/holidays/';
        $holidayData = [];

        foreach (glob($holidayPath . '*/*.php') as $file) {
            $data = require $file;
            $holidayData = array_replace_recursive($holidayData, $data);
        }

        foreach ($holidayData as &$years) {
            foreach ($years as &$holidays) {
                usort($holidays, fn ($a, $b) => strcmp($a['date'], $b['date']));
            }
        }

        $container->setParameter(
            'hargreaves_holiday_provider.data',
            $holidayData
        );
    }
}
