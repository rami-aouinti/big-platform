<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Toolbar;

use Symfony\Component\Form\AbstractType;

/**
 * Defines the base form used for all toolbars.
 *
 * Extend this class and stack the elements defined here, they are coupled to each other and connected with javascript.
 *
 * @deprecated since 2.0, will be removed with 2.1 - use ToolbarFormTrait instead
 */
abstract class AbstractToolbarForm extends AbstractType
{
    use ToolbarFormTrait;

    public function getBlockPrefix(): string
    {
        @trigger_error('The "AbstractToolbarForm" is deprecated and will be removed with 2.1, use the "ToolbarFormTrait" instead', E_USER_DEPRECATED);

        // Dirty hack to enable easy handling of GET form in controller and javascript.
        // Cleans up the name of all form elements (and unfortunately of the form itself).
        return '';
    }
}
