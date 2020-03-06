<?php

declare(strict_types=1);

namespace App\Extension\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CheckerExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_check', [$this, 'formatCheck']),
        ];
    }

    public function formatCheck(?string $checker): string
    {
        if (!$checker) {
            return '';
        }

        if (!class_exists($checker)) {
            return '';
        }

        return call_user_func([$checker, 'getName']);
    }
}
