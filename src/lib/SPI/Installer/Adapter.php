<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformInstaller\SPI\Installer;

/**
 * Installer adapter.
 *
 * Implement it if you need custom installer logic.
 */
interface Adapter
{
    const NAME_REGEX = '/^[A-Za-z0-9_-]+$/';

    /**
     * Get Installer Adapter name.
     * Requirements:
     * - unique across all available implementations of Adapter
     * - alphanumeric (with dashes or underscores, without whitespaces) - see NAME_REGEX.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Perform installer operations.
     */
    public function install(): void;
}
