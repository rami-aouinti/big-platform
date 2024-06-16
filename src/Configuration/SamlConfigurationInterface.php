<?php

declare(strict_types=1);

namespace App\Configuration;

/**
 * @CloudRequired
 */
interface SamlConfigurationInterface
{
    /**
     * Whether SAML login is activated.
     */
    public function isActivated(): bool;

    /**
     * Returns the title  that is exclusively used in the frontend.
     * Currently, used to display the button in the login screen.
     */
    public function getTitle(): string;

    /**
     * Returns the provider name that is exclusively used in the frontend.
     * Currently, used to display an icon in the login screen.
     */
    public function getProvider(): string;

    public function getAttributeMapping(): array;

    public function getRolesAttribute(): ?string;

    public function getRolesMapping(): array;

    public function isRolesResetOnLogin(): bool;

    public function getConnection(): array;
}
