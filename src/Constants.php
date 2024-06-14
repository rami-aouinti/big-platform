<?php

declare(strict_types=1);

namespace App;

/**
 * @package App
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class Constants
{
    /**
     * The current release version
     */
    public const string VERSION = '2.17.0';
    /**
     * The current release: major * 10000 + minor * 100 + patch
     */
    public const int VERSION_ID = 21700;
    /**
     * The software name
     */
    public const string SOFTWARE = 'Kimai';
    /**
     * Used in multiple views
     */
    public const string GITHUB = 'https://github.com/kimai/kimai/';
    /**
     * The GitHub repository name
     */
    public const string GITHUB_REPO = 'kimai/kimai';
    /**
     * Homepage, used in multiple views
     */
    public const string HOMEPAGE = 'https://www.kimai.org';
    /**
     * Default color for Customer, Project and Activity entities
     */
    public const string DEFAULT_COLOR = '#d2d6de';
}
