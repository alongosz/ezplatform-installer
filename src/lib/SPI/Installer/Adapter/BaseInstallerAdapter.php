<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformInstaller\SPI\Installer\Adapter;

/**
 * Base Installer Adapter to be extended by Adapter implementations.
 */
abstract class BaseInstallerAdapter
{
    public function __construct()
    {
    }
}
