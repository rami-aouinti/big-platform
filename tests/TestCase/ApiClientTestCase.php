<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Tests\TestCase;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Library\DbHelper;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * TestCase ApiClientTestCase
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.2 (2022-11-12)
 * @since 0.1.2 (2022-11-12) Upgrade to symfony 6.1
 * @since 0.1.1 (2022-01-29) Possibility to disable the JWT locally for debugging processes (#45)
 * @since 0.1.0 First version.
 */
abstract class ApiClientTestCase extends ApiTestCase
{
    protected static bool $keepDataBetweenTests = false;

    protected static bool $setUpDone = false;

    protected static bool $clearDB = true;

    protected static Client $client;

    protected static ContainerInterface $container;

    /**
     * This method is called before class.
     *
     * @param string[] $kernelOptions  Options to pass to the createKernel method
     * @param string[] $defaultOptions Default options for the requests
     * @throws Exception
     */
    public static function initClientEnvironment(array $kernelOptions = [], array $defaultOptions = []): void
    {
        /* If setup is already done. Stop here. */
        if (self::$setUpDone) {
            return;
        }

        /* Create client. */
        self::$client = self::createClient($kernelOptions, $defaultOptions);

        /* Setup is already done. */
        self::$setUpDone = true;

        /* Save container class. */
        self::$container = self::$kernel->getContainer();

        /* Do not clear the DB. */
        if (self::$keepDataBetweenTests || !self::$clearDB) {
            return;
        }

        /* Build the db helper */
        $dbHelper = new DbHelper(self::$kernel);

        /* Empty test table */
        $dbHelper->printAndExecuteCommands([
            '/* Drop schema */' => 'doctrine:schema:drop --force --env=%(environment)s',
            '/* Create schema */' => 'doctrine:schema:create --env=%(environment)s',
            '/* Load fixtures */' => 'doctrine:fixtures:load -n --env=%(environment)s', # --group=test',
        ]);
    }

    /**
     * Do API request.
     *
     * @param string $endpoint
     * @param string $method
     * @param string[]|string[][] $options
     * @param string|null $bearer
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function doRequest(string $endpoint, string $method = Request::METHOD_GET, array $options = [], string $bearer = null): ResponseInterface
    {
        return self::$client->request($method, $endpoint, array_merge([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ], $bearer !== null ? ['auth_bearer' => $bearer] : [], $options));
    }

    /**
     * Empty tearDown.
     * Will be done with self::tearDownAfterClass.
     */
    protected function tearDown(): void
    {
    }

    /**
     * Tear down after test.
     */
    public static function tearDownAfterClass(): void
    {
        static::ensureKernelShutdown();
        static::$booted = false;
    }

    /**
     * Returns the full api endpoint.
     *
     * @param string $apiEndpointItem
     * @return string
     */
    protected function getApiEndpoint(string $apiEndpointItem): string
    {
        return $apiEndpointItem;
    }

    /**
     * Returns the full api endpoint for an item.
     *
     * @param string $apiEndpointItem
     * @param int $id
     * @return string
     */
    protected function getApiEndpointItem(string $apiEndpointItem, int $id): string
    {
        return sprintf($apiEndpointItem, $id);
    }
}
