<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\EzPlatformInstallerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * eZ Platform Installer Bundle Extension.
 */
class EzPlatformInstallerExtension extends Extension
{
    /**
     * Load EzPlatformInstallerBundle configuration.
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/services')
        );

        $loader->load('core.yaml');
        $container->addResource(
            new FileResource(__DIR__ . '/../Resources/config/services/core.yaml')
        );

        $loader->load('api.yaml');
        $container->addResource(
            new FileResource(__DIR__ . '/../Resources/config/services/api.yaml')
        );
    }
}
