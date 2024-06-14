<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\Runtime;

use App\Crm\Application\Widget\WidgetException;
use App\Crm\Application\Widget\WidgetInterface;
use App\Crm\Application\Widget\WidgetService;
use App\User\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @package App\Crm\Application\Twig\Runtime
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class WidgetExtension implements RuntimeExtensionInterface
{
    public function __construct(
        private WidgetService $service,
        private Security $security
    ) {
    }

    /**
     * @param WidgetInterface|string $widget
     * @throws WidgetException
     */
    public function renderWidget(Environment $environment, $widget, array $options = []): string
    {
        if (!($widget instanceof WidgetInterface) && !\is_string($widget)) {
            throw new \InvalidArgumentException('Widget must be either a WidgetInterface or a string');
        }

        if (\is_string($widget)) {
            if (!$this->service->hasWidget($widget)) {
                throw new \InvalidArgumentException(sprintf('Unknown widget "%s" requested', $widget));
            }

            $widget = $this->service->getWidget($widget);
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $widget->setUser($user);
        }

        $options = $widget->getOptions($options);

        return $environment->render($widget->getTemplateName(), [
            'data' => $widget->getData($options),
            'options' => $options,
            'title' => $widget->getTitle(),
            'widget' => $widget,
        ]);
    }
}
