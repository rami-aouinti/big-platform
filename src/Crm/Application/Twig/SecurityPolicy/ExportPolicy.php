<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\SecurityPolicy;

use Twig\Sandbox\SecurityPolicyInterface;

/**
 * Represents the security policy for custom Twig export templates.
 */
final class ExportPolicy implements SecurityPolicyInterface
{
    private ChainPolicy $policy;

    public function __construct()
    {
        $this->policy = new ChainPolicy();
        $this->policy->addPolicy(new DefaultPolicy());
    }

    public function checkSecurity($tags, $filters, $functions): void
    {
        $this->policy->checkSecurity($tags, $filters, $functions);
    }

    public function checkMethodAllowed($obj, $method): void
    {
        $this->policy->checkMethodAllowed($obj, $method);
    }

    public function checkPropertyAllowed($obj, $property): void
    {
        $this->policy->checkPropertyAllowed($obj, $property);
    }
}
