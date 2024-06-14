<?php

declare(strict_types=1);

namespace App\Crm\Transport\API;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Not found', \Exception $previous = null, int $code = 404, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);
    }
}
