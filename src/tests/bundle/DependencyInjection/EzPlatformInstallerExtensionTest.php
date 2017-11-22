<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\Tests\EzPlatformInstallerBundle\DependencyInjection;

use EzSystems\EzPlatformInstallerBundle\Command\EzPlatformInstallCommand;
use EzSystems\EzPlatformInstallerBundle\DependencyInjection\EzPlatformInstallerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\ExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \EzPlatformInstallerExtension
 */
class EzPlatformInstallerExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var EzPlatformInstallerExtension
     */
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new EzPlatformInstallerExtension();
        $this->container->setParameter('kernel.cache_dir', '/tmp');
        $this->container->registerExtension($this->extension);
        $this->container->addCompilerPass(new ExtensionCompilerPass());
    }

    /**
     * Test getting registered extension.
     */
    public function testGetExtension()
    {
        self::assertSame($this->extension, $this->container->getExtension($this->extension->getAlias()));
    }

    /**
     * Test loading registered extension.
     */
    public function testLoadExtension()
    {
        $this->extension->load([], $this->container);
        $this->container->compile();

        self::assertTrue($this->container->isCompiled(), 'Container is not compiled');
        self::assertTrue($this->container->hasDefinition(EzPlatformInstallCommand::class));
    }
}
