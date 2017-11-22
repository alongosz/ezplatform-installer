<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\Tests\EzPlatformInstaller\SPI;

use ArrayIterator;
use EzSystems\EzPlatformInstaller\Core\Installer\CoreInstaller;
use EzSystems\EzPlatformInstaller\Core\Installer\Adapter\CleanInstallerAdapter;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DriverManager as DBALDriverManager;
use RuntimeException;

/**
 * Database Integration tests for Installer.
 *
 * This test depends on DATABASE environment variable containing database DSN.
 */
class CoreInstallerIntegrationTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private static $connection = null;

    /**
     * @var \EzSystems\EzPlatformInstaller\API\Installer
     */
    private $installer;

    private static function getConnection()
    {
        $databaseDsn = getenv('DATABASE');
        if (empty($databaseDsn)) {
            throw new RuntimeException(
                'Missing database DSN. You can specify it via DATABASE environment variable'
            );
        }

        return DBALDriverManager::getConnection(
            [
                'url' => $databaseDsn,
            ],
            new DBALConfiguration()
        );
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$connection = static::getConnection();
    }

    public function setUp()
    {
        parent::setUp();

        $this->installer = new CoreInstaller(
            new ArrayIterator(
                [
                    new CleanInstallerAdapter(),
                ]
            )
        );
    }

    public function testInstall()
    {
        $this->installer->install('clean');
    }
}
