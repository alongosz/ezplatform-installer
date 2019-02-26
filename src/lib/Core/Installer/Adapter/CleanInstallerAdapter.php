<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformInstaller\Core\Installer\Adapter;

use EzSystems\EzPlatformInstaller\SPI\Installer\Adapter as InstallerAdapter;
use EzSystems\EzPlatformInstaller\SPI\Installer\Adapter\BaseInstallerAdapter;

class CleanInstallerAdapter extends BaseInstallerAdapter implements InstallerAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'clean';
    }

    /**
     * {@inheritdoc}
     */
    public function install(): void
    {
        // TODO: Implement install() method.
    }
}
