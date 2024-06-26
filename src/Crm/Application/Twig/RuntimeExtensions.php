<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig;

use App\Crm\Application\Twig\Runtime\EncoreExtension;
use App\Crm\Application\Twig\Runtime\MarkdownExtension;
use App\Crm\Application\Twig\Runtime\MenuExtension;
use App\Crm\Application\Twig\Runtime\QrCodeExtension;
use App\Crm\Application\Twig\Runtime\ThemeExtension;
use App\Crm\Application\Twig\Runtime\TimesheetExtension;
use App\Crm\Application\Twig\Runtime\WidgetExtension;
use App\Crm\Application\Utils\StringHelper;
use KevinPapst\TablerBundle\Twig\RuntimeExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class RuntimeExtensions extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('trigger', [ThemeExtension::class, 'trigger'], [
                'needs_environment' => true,
            ]),
            new TwigFunction('actions', [ThemeExtension::class, 'actions']),
            new TwigFunction('get_title', [ThemeExtension::class, 'generateTitle']),
            new TwigFunction('progressbar_color', [ThemeExtension::class, 'getProgressbarClass']),
            new TwigFunction('javascript_translations', [ThemeExtension::class, 'getJavascriptTranslations']),
            new TwigFunction('form_time_presets', [ThemeExtension::class, 'getTimePresets']),
            new TwigFunction('active_timesheets', [TimesheetExtension::class, 'activeEntries']),
            new TwigFunction('favorite_timesheets', [TimesheetExtension::class, 'favoriteEntries']),
            new TwigFunction('encore_entry_css_source', [EncoreExtension::class, 'getEncoreEntryCssSource']),
            new TwigFunction('render_widget', [WidgetExtension::class, 'renderWidget'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new TwigFunction('icon', [RuntimeExtension::class, 'createIcon'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('qr_code_data_uri', [QrCodeExtension::class, 'qrCodeDataUriFunction']),
            new TwigFunction('user_shortcuts', [MenuExtension::class, 'getUserShortcuts']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [MarkdownExtension::class, 'markdownToHtml'], [
                'pre_escape' => 'html',
                'is_safe' => ['html'],
            ]),
            new TwigFilter('desc2html', [MarkdownExtension::class, 'timesheetContent'], [
                'is_safe' => ['html'],
            ]),
            new TwigFilter('comment2html', [MarkdownExtension::class, 'commentContent'], [
                'is_safe' => ['html'],
            ]),
            new TwigFilter('comment1line', [MarkdownExtension::class, 'commentOneLiner'], [
                'pre_escape' => 'html',
                'is_safe' => ['html'],
            ]),
            new TwigFilter('colorize', [ThemeExtension::class, 'colorize']),
            new TwigFilter('icon', [RuntimeExtension::class, 'icon']),
            new TwigFilter('sanitize_dde', StringHelper::sanitizeDDE(...)),
        ];
    }
}
