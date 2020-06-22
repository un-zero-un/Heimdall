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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                static function (FormEvent $event) {
                    $parentForm = $event->getForm()->getParent();

                    if (null === $parentForm) {
                        throw new FormContextException(
                            sprintf(
                                'The %s FormType cannot be used as a root FormType',
                                self::class
                            )
                        );
                    }

                    $configuredCheck = $parentForm->getData();
                    if (!is_a($configuredCheck->getCheck(), ConfigurableChecker::class, true)) {
                        return;
                    }

                    $parentForm->add(
                        'config',
                        call_user_func([$configuredCheck->getCheck(), 'getConfigFormType']),
                    );
                }
            );
    }
}
