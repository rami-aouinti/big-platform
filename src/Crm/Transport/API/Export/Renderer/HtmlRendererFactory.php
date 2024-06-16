<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Application\Service\Activity\ActivityStatisticService;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Transport\API\Export\Base\HtmlRenderer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

/**
 * @package App\Crm\Transport\API\Export\Renderer
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class HtmlRendererFactory
{
    public function __construct(
        private Environment $twig,
        private EventDispatcherInterface $dispatcher,
        private ProjectStatisticService $projectStatisticService,
        private ActivityStatisticService $activityStatisticService
    ) {
    }

    public function create(string $id, string $template): HtmlRenderer
    {
        $renderer = new HtmlRenderer($this->twig, $this->dispatcher, $this->projectStatisticService, $this->activityStatisticService);
        $renderer->setId($id);
        $renderer->setTemplate($template);

        return $renderer;
    }
}
