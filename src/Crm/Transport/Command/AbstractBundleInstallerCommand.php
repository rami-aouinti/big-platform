<?php

declare(strict_types=1);

namespace App\Crm\Transport\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Extend this class if you have a plugin that requires installation steps.
 */
abstract class AbstractBundleInstallerCommand extends Command
{
    /**
     * Returns the base directory to the Kimai installation.
     */
    protected function getRootDirectory(): string
    {
        /** @var Application $application */
        $application = $this->getApplication();

        return $application->getKernel()->getProjectDir();
    }

    /**
     * If your bundle ships assets, that need to be available in the public/ directory,
     * then overwrite this method and return: <true>.
     */
    protected function hasAssets(): bool
    {
        return false;
    }

    /**
     * Returns an absolute filename to your doctrine migrations configuration, if you want to install database tables.
     */
    protected function getMigrationConfigFilename(): ?string
    {
        return null;
    }

    /**
     * Returns the bundle short name for the installer command.
     */
    abstract protected function getBundleCommandNamePart(): string;

    /**
     * Returns the full name fo this command.
     * Please stick to the standard and overwrite getBundleCommandNamePart() only.
     */
    protected function getInstallerCommandName(): string
    {
        return sprintf('kimai:bundle:%s:install', $this->getBundleCommandNamePart());
    }

    /**
     * Returns the bundles real name (same as your namespace).
     */
    protected function getBundleName(): string
    {
        $class = new \ReflectionClass($this);
        $parts = explode('\\', $class->getNamespaceName());

        if ($parts[0] !== 'KimaiPlugin') {
            throw new LogicException(
                sprintf('Unsupported namespace given, expected "KimaiPlugin" but received "%s". Please overwrite getBundleName() and return the correct bundle name.', $parts[0])
            );
        }

        return $parts[1];
    }

    protected function configure(): void
    {
        $this
            ->setName($this->getInstallerCommandName())
            ->setDescription('Install the bundle: ' . $this->getBundleName())
            ->setHelp('This command will perform the basic installation steps to get the bundle up and running.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // many users execute the bin/console command from arbitrary locations
        // this will make sure that relative paths (like doctrine migrations) work as expected
        $path = getcwd();
        chdir($this->getRootDirectory());

        $bundleName = $this->getBundleName();
        $io->title(
            sprintf('Starting installation of plugin: %s ...', $bundleName)
        );

        try {
            $this->importMigrations($io, $output);
        } catch (\Exception $ex) {
            $io->error(
                sprintf('Failed to install database for bundle %s. %s', $bundleName, $ex->getMessage())
            );

            return Command::FAILURE;
        }

        if ($this->hasAssets()) {
            try {
                $this->installAssets($io, $output);
            } catch (\Exception $ex) {
                $io->error(
                    sprintf('Failed to install assets for bundle %s. %s', $bundleName, $ex->getMessage())
                );

                return Command::FAILURE;
            }
        }

        chdir($path);

        $io->success(
            sprintf('Congratulations! Plugin was successful installed: %s', $bundleName)
        );

        return Command::SUCCESS;
    }

    protected function installAssets(SymfonyStyle $io, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('assets:install');
        $cmdInput = new ArrayInput([]);
        $cmdInput->setInteractive(false);
        if ($command->run($cmdInput, $output) !== 0) {
            throw new \Exception('Problem occurred while installing assets.');
        }

        $io->writeln('');
    }

    protected function importMigrations(SymfonyStyle $io, OutputInterface $output): void
    {
        $config = $this->getMigrationConfigFilename();

        if ($config === null) {
            return;
        }

        if (!file_exists($config)) {
            throw new FileNotFoundException('Missing doctrine migrations config file: ' . $config);
        }

        // prevent windows from breaking
        $config = str_replace('/', DIRECTORY_SEPARATOR, $config);

        $command = $this->getApplication()->find('doctrine:migrations:migrate');
        $cmdInput = new ArrayInput([
            '--allow-no-migration' => true,
            '--configuration' => $config,
        ]);
        $cmdInput->setInteractive(false);
        if ($command->run($cmdInput, $output) !== 0) {
            throw new \Exception('Problem occurred while executing migrations.');
        }

        $io->writeln('');
    }
}
