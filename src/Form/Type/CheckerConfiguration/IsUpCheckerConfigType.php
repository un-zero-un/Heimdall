<?php

declare(strict_types=1);

namespace App\Form\Type\CheckerConfiguration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class IsUpCheckerConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('max_retries', IntegerType::class, ['label' => 'Max retries']);
    }
}
