<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\Runtime;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;
use Twig\Extension\RuntimeExtensionInterface;

final class QrCodeExtension implements RuntimeExtensionInterface
{
    public function __construct()
    {
    }

    /**
     * @param array<string, mixed> $writerOptions
     */
    public function qrCodeDataUriFunction(string $data, array $writerOptions = []): string
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->writerOptions($writerOptions)
            ->data($data)
            // if this causes errors at some point and needs to be configurable, keep this default!
            ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
            ->build()
            ->getDataUri();
    }
}
