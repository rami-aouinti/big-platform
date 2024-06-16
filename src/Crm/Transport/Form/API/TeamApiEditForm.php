<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\API;

use App\Crm\Transport\Form\TeamEditForm;
use Symfony\Component\Form\FormBuilderInterface;

final class TeamApiEditForm extends TeamEditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->remove('users');
    }
}
