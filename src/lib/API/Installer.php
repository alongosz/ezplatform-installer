<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);
namespace EzSystems\EzPlatformInstaller\API;

/**
 * Installer API interface.
 */
interface Installer
{
    /**
     * Install eZ Platform using the given Installer Adapter.
     *
     * @param string $adapterName Installer Adapter name
     */
    public function install(string $adapterName): void;

    /**
     * Get list of available Installer Adapters.
     *
     * @return string[]
     */
    public function getAvailableAdapters(): array;
}
