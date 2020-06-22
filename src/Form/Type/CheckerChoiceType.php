<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Checker\CheckerCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckerChoiceType extends AbstractType
{
    private CheckerCollection $checkerCollection;

    public function __construct(CheckerCollection $checkerCollection)
    {
        $this->checkerCollection = $checkerCollection;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(
                [
                    'placeholder'  => '',
                    'choices'      => $this->checkerCollection->all(),
                    'choice_label' => 'name',
                    'choice_value' => static function ($checker): string {
                        if (is_string($checker)) {
                            return $checker;
                        }

                        return get_class($checker);
                    },
                ]
            );
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
