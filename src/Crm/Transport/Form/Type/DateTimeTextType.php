<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class DateTimeTextType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }
}
