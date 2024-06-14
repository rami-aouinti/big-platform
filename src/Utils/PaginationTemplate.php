<?php

declare(strict_types=1);

namespace App\Utils;

use Pagerfanta\View\Template\TwitterBootstrap5Template;

final class PaginationTemplate extends TwitterBootstrap5Template
{
    public function current(int $page): string
    {
        return $this->linkLi($this->option('css_active_class'), $this->generateRoute($page), $page);
    }
    /**
     * @return array<string, string>
     */
    protected function getDefaultOptions(): array
    {
        return array_merge(
            parent::getDefaultOptions(),
            [
                //'prev_message' = '←',
                //'next_message' = '→',
                'prev_message' => '<i class="fas fa-chevron-left"></i>',
                'next_message' => '<i class="fas fa-chevron-right"></i>',
            ]
        );
    }

    /**
     * @param int|string $text
     */
    protected function linkLi(string $class, string $href, $text, ?string $rel = null): string
    {
        $liClass = implode(' ', array_filter(['page-item', $class]));
        $rel = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li class="%s"><a class="page-link pagination-link" href="%s"%s>%s</a></li>', $liClass, $href, $rel, $text);
    }

    /**
     * @param string $text
     */
    protected function spanLi(string $class, $text): string
    {
        $liClass = implode(' ', array_filter(['page-item', $class]));

        return sprintf('<li class="%s"><span class="page-link pagination-link">%s</span></li>', $liClass, $text);
    }
}
