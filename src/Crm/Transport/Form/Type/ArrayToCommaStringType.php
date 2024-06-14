<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package App\Crm\Transport\Form\Type
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ArrayToCommaStringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if ($value === null) {
                    return null;
                }

                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
