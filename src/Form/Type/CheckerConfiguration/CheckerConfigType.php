<?php

declare(strict_types=1);

namespace App\Form\Type\CheckerConfiguration;

use App\Checker\ConfigurableChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckerConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
                $configuredCheck = $event->getForm()->getParent()->getData();

                if (!is_a($configuredCheck->getCheck(), ConfigurableChecker::class, true)) {
                    return;
                }

                $event->getForm()->getParent()->add(
                    'config',
                    call_user_func([$configuredCheck->getCheck(), 'getConfigFormType']),
                );
            });
    }
}
