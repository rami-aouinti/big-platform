<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AccessDeniedException extends AccessDeniedHttpException
{
}
