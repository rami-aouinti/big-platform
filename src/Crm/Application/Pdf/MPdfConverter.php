<?php

declare(strict_types=1);

namespace App\Crm\Application\Pdf;

use App\Constants;
use App\Crm\Application\Utils\FileHelper;
use Exception;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

use function array_key_exists;
use function count;
use function in_array;
use function ini_get;
use function is_array;

/**
 * @package App\Crm\Application\Pdf
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class MPdfConverter implements HtmlToPdfConverter
{
    public function __construct(
        private FileHelper $fileHelper,
        private string $cacheDirectory
    ) {
    }

    /**
     * @throws MpdfException
     */
    public function convertToPdf(string $html, array $options = []): string
    {
        $sanitized = array_merge(
            $this->sanitizeOptions($options),
            [
                'tempDir' => $this->cacheDirectory,
                'exposeVersion' => false,
            ]
        );

        $mpdf = $this->initMpdf($sanitized);

        // some OS'es do not follow the PHP default settings
        if ((int)ini_get('pcre.backtrack_limit') < 1000000) {
            @ini_set('pcre.backtrack_limit', '1000000');
        }

        // large amount of data take time
        @ini_set('max_execution_time', '120');

        // reduce the size of content parts that are passed to MPDF, to prevent
        // https://mpdf.github.io/troubleshooting/known-issues.html#blank-pages-or-some-sections-missing
        $parts = explode('<pagebreak>', $html);
        for ($i = 0; $i < count($parts); $i++) {
            if (stripos($parts[$i], '<!-- CONTENT_PART -->') !== false) {
                $subParts = explode('<!-- CONTENT_PART -->', $parts[$i]);
                foreach ($subParts as $subPart) {
                    $mpdf->WriteHTML($subPart);
                }
            } else {
                $mpdf->WriteHTML($parts[$i]);
            }

            if ($i < count($parts) - 1) {
                $mpdf->WriteHTML('<pagebreak>');
            }
        }

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    private function sanitizeOptions(array $options): array
    {
        $configs = new ConfigVariables();
        $fonts = new FontVariables();
        $allowed = [
            'mode', 'format', 'default_font_size', 'default_font', 'margin_left', 'margin_right', 'margin_top',
            'margin_bottom', 'margin_header', 'margin_footer', 'orientation', 'fonts',
        ];

        $filtered = array_filter($options, function ($key) use ($allowed, $configs, $fonts): bool {
            if (!in_array($key, $allowed)) {
                if (!array_key_exists($key, $configs->getDefaults())) {
                    return array_key_exists($key, $fonts->getDefaults());
                }
            }

            return true;
        }, ARRAY_FILTER_USE_KEY);

        if (array_key_exists('tempDir', $filtered)) {
            unset($filtered['tempDir']);
        }

        return $filtered;
    }

    /**
     * @param array<string, array> $options
     *
     * @throws MpdfException
     * @throws Exception
     */
    private function initMpdf(array $options): Mpdf
    {
        $options['fontDir'] = $this->getFontDirectories();
        $options['fontdata'] = $this->mergeFontData($options);

        $mpdf = new Mpdf($options);
        $mpdf->creator = Constants::SOFTWARE;

        return $mpdf;
    }

    /**
     * @throws Exception
     * @return array<string>
     */
    private function getFontDirectories(): array
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirectories = $defaultConfig['fontDir'];
        $fontDirectories[] = rtrim($this->fileHelper->getDataDirectory('fonts'), DIRECTORY_SEPARATOR);

        return $fontDirectories;
    }

    /**
     * @param array<string, array> $options
     * @return array<string, array>
     */
    private function mergeFontData(array $options): array
    {
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // lowercase all font names, otherwise they cannot be loaded
        // see https://github.com/kimai/www.kimai.org/issues/280
        if (array_key_exists('fonts', $options) && is_array($options['fonts'])) {
            $fonts = [];
            foreach ($options['fonts'] as $name => $values) {
                $fonts[strtolower($name)] = $values;
            }
            $fontData = array_merge($fontData, $fonts);
        }

        return $fontData;
    }
}
