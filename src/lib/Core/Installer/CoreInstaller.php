<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformInstaller\Core\Installer;

use EzSystems\EzPlatformInstaller\API\Installer;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformInstaller\SPI\Installer\Adapter;
use Traversable;

/**
 * Core Installer implementation.
 */
class CoreInstaller implements Installer
{
    /**
     * @var \EzSystems\EzPlatformInstaller\SPI\Installer\Adapter[]
     */
    private $adapters = [];

    /**
     * @param \Traversable|\EzSystems\EzPlatformInstaller\SPI\Installer\Adapter[] $adapters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(Traversable $adapters)
    {
        foreach ($adapters as $adapter) {
            $adapterName = $this->getAdapterName($adapter);
            $this->adapters[$adapterName] = $adapter;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $adapterName): void
    {
        if (!\array_key_exists($adapterName, $this->adapters)) {
            throw new InvalidArgumentException(
                '$adapterName',
                sprintf(
                    'The Adapter "%s" does not exist. Did you forget to enable Bundle providing it in AppKernel? Existing adapters: %s',
                    $adapterName,
                    implode(', ', $this->getAvailableAdapters())
                )
            );
        }

        // delegate installing to chosen Adapter
        $this->adapters[$adapterName]->install();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableAdapters(): array
    {
        return array_keys($this->adapters);
    }

    /**
     * Get name of an Adapter injected via extensibility point and validate it.
     *
     * @param \EzSystems\EzPlatformInstaller\SPI\Installer\Adapter $adapter
     *
     * @return string Adapter name
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    private function getAdapterName(Adapter $adapter): string
    {
        $adapterName = $adapter->getName();
        if (\array_key_exists($adapterName, $this->adapters)) {
            throw new InvalidArgumentException(
                '$adapters',
                sprintf(
                    'The Adapter "%s" is already implemented by %s',
                    $adapterName,
                    \get_class($this->adapters[$adapterName])
                )
            );
        }

        if (!preg_match(Adapter::NAME_REGEX, $adapterName)) {
            throw new InvalidArgumentException(
                '$adapters',
                sprintf(
                    'The Adapter name "%s" can contain alphanumeric characters, hyphens and underscores only',
                    $adapterName
                )
            );
        }

        return $adapterName;
    }
}
