<?php

declare(strict_types=1);

namespace App\Command;

use App\Checker\CheckResult;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

class CheckResultCliFormatter
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function formatCheckResult(SymfonyStyle $io, CheckResult $result): void
    {
        $message = $this->translator->trans(
            'check_result.type.' . $result->getType(),
            array_combine(
                array_map(static function (string $key) { return '%' . $key . '%'; }, array_keys($result->getData())),
                array_values($result->getData())
            )
        );

        switch ($result->getLevel()) {
            case 'error':
                $io->error($message);
                break;
            case 'warning':
                $io->warning($message);
                break;
            case 'success':
                $io->success($message);
                break;
            default:
                $io->comment($message);
        }
    }
}
