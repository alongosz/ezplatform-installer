<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformInstallerBundle\Command;

use Doctrine\DBAL\Connection;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformInstaller\API\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * The eZ Platform Installer Symfony command <code>ezplatform:install</code>.
 */
class EzPlatformInstallCommand extends Command
{
    const EXIT_DATABASE_NOT_FOUND_ERROR = 3;
    const EXIT_GENERAL_DATABASE_ERROR = 4;
    const EXIT_PARAMETERS_NOT_FOUND = 5;
    const EXIT_MISSING_PERMISSIONS = 6;
    const EXIT_CANCELLED = 7;

    /**
     * @var \Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface
     */
    private $cacheClearer;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var \EzSystems\EzPlatformInstaller\API\Installer
     */
    private $installer;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    public function __construct(
        Installer $installer,
        Connection $connection,
        CacheClearerInterface $cacheClearer,
        $cacheDir
    ) {
        $this->installer = $installer;
        $this->connection = $connection;
        $this->cacheClearer = $cacheClearer;
        $this->cacheDir = $cacheDir;

        parent::__construct();
    }

    protected function configure()
    {
        $availableInstallers = $this->installer->getAvailableAdapters();

        $this
            ->setName('ezplatform:install')
            ->setDescription('eZ Platform Installer')
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                sprintf(
                    'The type of install. Available Installers: %s',
                    implode(', ', $availableInstallers)
                ),
                'clean'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->checkPermissions();
        $this->checkParametersFile();

        $database = $this->connection->getDatabase();
        $databasePlatform = $this->connection->getDatabasePlatform();
        $this->output->writeln('');
        if (!$this->confirm(
            "You are about to install eZ Platform using '<info>{$database}</info>' <comment>{$databasePlatform->getName()}</comment> database. Would you like to continue[y/N]? "
        )) {
            return self::EXIT_CANCELLED;
        }

        $type = $input->getArgument('type');
        $this->installer->install($type);
        $this->cacheClear();
    }

    /**
     * Database does not exist Event handler.
     *
     * @param string $databaseName
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function databaseExists(string $databaseName)
    {
        if (!$this->confirm(
            "Database {$databaseName} does not exist. Would you like to create it?"
        )) {
            throw new InvalidArgumentException(
                "Database {$databaseName} does not exist",
                self::EXIT_DATABASE_NOT_FOUND_ERROR
            );
        }
        //$installer->createDatabase();
        $this->output->writeln('Database created.');
    }

    private function checkPermissions()
    {
        if (!is_writable('web') && !is_writable('web/var')) {
            throw new IOException(
                '[web/ | web/var] is not writable',
                self::EXIT_MISSING_PERMISSIONS
            );
        }
    }

    private function checkParametersFile()
    {
        $parametersFile = 'app/config/parameters.yml';
        if (!is_file($parametersFile)) {
            throw new FileNotFoundException(
                "Required configuration file '{$parametersFile}' not found",
                self::EXIT_PARAMETERS_NOT_FOUND
            );
        }
    }

    /**
     * Clear Symfony Cache.
     */
    private function cacheClear()
    {
        if (!is_writable($this->cacheDir)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to write in the "%s" directory, check install doc on disk permissions before you continue.',
                    $this->cacheDir
                )
            );
        }

        $this->output->writeln("Clearing cache for directory <info>{$this->cacheDir}</info>");
        $this->cacheClearer->clear($this->cacheDir);
    }

    /**
     * Ask user for confirmation using Console Question Helper.
     *
     * @param string $text
     *
     * @return bool
     */
    private function confirm($text): bool
    {
        if (!$this->input->isInteractive()) {
            return true;
        }

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion($text);

        return (bool)$questionHelper->ask($this->input, $this->output, $question);
    }
}
