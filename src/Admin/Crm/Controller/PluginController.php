<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Application\Utils\PageSetup;
use App\Plugin\PluginManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @package App\Admin\Crm\Controller
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/admin/plugins')]
#[IsGranted('plugins')]
final class PluginController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/', name: 'plugins', methods: ['GET'])]
    public function indexAction(PluginManager $manager, HttpClientInterface $client, CacheInterface $cache): Response
    {
        $installed = [];
        $plugins = $manager->getPlugins();
        foreach ($plugins as $plugin) {
            $installed[] = $plugin->getId();
        }

        $page = new PageSetup('menu.plugin');
        $page->setHelp('plugins.html');

        return $this->render('plugin/index.html.twig', [
            'page_setup' => $page,
            'plugins' => $plugins,
            'installed' => $installed,
            'extensions' => $this->getPluginInformation($client, $cache),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getPluginInformation(HttpClientInterface $client, CacheInterface $cache): array
    {
        return $cache->get('kimai.marketplace_extensions', function (ItemInterface $item) use ($client) {
            try {
                $response = $client->request('GET', 'https://www.kimai.org/plugins.json');

                if ($response->getStatusCode() !== 200) {
                    return [];
                }

                $json = json_decode($response->getContent(), true);

                if ($json === null) {
                    return [];
                }

                $item->expiresAfter(86400); // one day

                return $response->toArray();
            } catch (\Throwable $exception) {
                $this->logException($exception);
                $this->flashError('Could not download plugin information');
            }

            return [];
        });
    }
}
