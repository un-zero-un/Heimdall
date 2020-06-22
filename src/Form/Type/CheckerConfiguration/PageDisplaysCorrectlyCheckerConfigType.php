<?php

declare(strict_types=1);

namespace App\Form\Type\CheckerConfiguration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PageDisplaysCorrectlyCheckerConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('page', TextType::class, ['label' => 'Page URL'])
            ->add('selector', TextType::class, ['label' => 'CSS selector'])
            ->add('expected', TextType::class, ['label' => 'Expected result']);
    }
}
