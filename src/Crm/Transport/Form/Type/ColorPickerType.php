<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Constants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ColorPickerType extends AbstractType implements DataTransformerInterface
{
    public const DEFAULT_COLOR = Constants::DEFAULT_COLOR;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'documentation' => [
                'type' => 'string',
                'description' => sprintf('The hexadecimal color code (default: %s)', self::DEFAULT_COLOR),
            ],
            'label' => 'color',
            'empty_data' => null,
        ]);
    }

    public function transform(mixed $data): mixed
    {
        if (empty($data)) {
            return self::DEFAULT_COLOR;
        }

        return $data;
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value === null ? self::DEFAULT_COLOR : $value;
    }

    public function getParent(): string
    {
        return ColorType::class;
    }
}
