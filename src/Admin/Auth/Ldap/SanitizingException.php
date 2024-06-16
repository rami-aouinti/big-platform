<?php

declare(strict_types=1);

namespace App\Admin\Auth\Ldap;

final class SanitizingException extends \Exception
{
    public function __construct(
        private \Exception $actualException,
        private string $secret
    ) {
        parent::__construct(
            $this->stripSecret($actualException->getMessage(), $secret),
            $actualException->getCode()
        );
    }

    public function __toString(): string
    {
        return $this->stripSecret($this->actualException->__toString(), $this->secret);
    }

    protected function stripSecret(string $message, string $secret): string
    {
        return str_replace($secret, '****', $message);
    }
}
