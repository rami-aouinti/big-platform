<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tool\Transport\Controller\Api\v1\Localization;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class LanguageControllerTest
 *
 * @package App\Tests
 */
class LanguageControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/localization/language';
    private LocalizationServiceInterface $localizationService;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $localizationService = static::getContainer()->get(LocalizationServiceInterface::class);
        self::assertInstanceOf(LocalizationServiceInterface::class, $localizationService);
        $this->localizationService = $localizationService;
    }

    /**
     * @testdox Test that `GET /v1/localization/language` returns success response.
     *
     * @throws Throwable
     */
    public function testThatGettingSupportedLanguagesRouteReturns200(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        static::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        $supportedLanguages = array_flip($this->localizationService->getLanguages());

        foreach ($responseData as $language) {
            self::assertArrayHasKey($language, $supportedLanguages);
        }
    }
}