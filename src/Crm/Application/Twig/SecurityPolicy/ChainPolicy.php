<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\SecurityPolicy;

use Twig\Sandbox\SecurityPolicyInterface;

final class ChainPolicy implements SecurityPolicyInterface
{
    /**
     * @var array<SecurityPolicyInterface>
     */
    private array $policies = [];

    public function __construct()
    {
    }

    public function addPolicy(SecurityPolicyInterface $policy): void
    {
        $this->policies[] = $policy;
    }

    public function checkSecurity($tags, $filters, $functions): void
    {
        foreach ($this->policies as $policy) {
            $policy->checkSecurity($tags, $filters, $functions);
        }
    }

    public function checkMethodAllowed($obj, $method): void
    {
        foreach ($this->policies as $policy) {
            $policy->checkMethodAllowed($obj, $method);
        }
    }

    public function checkPropertyAllowed($obj, $property): void
    {
        foreach ($this->policies as $policy) {
            $policy->checkPropertyAllowed($obj, $property);
        }
    }
}
