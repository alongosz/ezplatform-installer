<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\EzPlatformInstallerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * eZ Platform Installer Bundle Extension.
 */
class EzPlatformInstallerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->loadCoreServices($container);
        $this->loadBundleServices($container);
    }

    private function loadCoreServices($container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../lib/Core/Resources/config'));
        $loader->load('services.yml');
    }

    private function loadBundleServices($container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
