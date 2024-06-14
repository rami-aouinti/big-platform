<?php

declare(strict_types=1);

namespace App\Twig\SecurityPolicy;

use Twig\Sandbox\SecurityPolicyInterface;

/**
 * The Twig environment needs the sandbox extension, which itself needs a policy to start working.
 */
final class DefaultPolicy implements SecurityPolicyInterface
{
    public function checkSecurity($tags, $filters, $functions): void
    {
    }

    public function checkMethodAllowed($obj, $method): void
    {
    }

    public function checkPropertyAllowed($obj, $property): void
    {
    }
}
