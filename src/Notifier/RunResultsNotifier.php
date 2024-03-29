<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Model\Run;
use App\Repository\RunRepository;
use App\ValueObject\ResultLevel;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RunResultsNotifier
{
    private NotifierInterface $notifier;

    private Environment $twig;

    private TranslatorInterface $translator;

    private array $notificationRecipients;

    private RunRepository $runRepository;

    public function __construct(
        NotifierInterface $notifier,
        Environment $twig,
        TranslatorInterface $translator,
        RunRepository $runRepository,
        array $notificationRecipients
    )
    {
        $this->notifier               = $notifier;
        $this->translator             = $translator;
        $this->twig                   = $twig;
        $this->notificationRecipients = $notificationRecipients;
        $this->runRepository = $runRepository;
    }

    /**
     * @param Run[] $runs
     */
    public function notify(array $runs): void
    {
        $worstLevel = ResultLevel::findWorst(array_map(
            static fn(Run $run) => ResultLevel::fromString($run->getSiteResult() ?: ResultLevel::UNKNOWN),
            $runs
        ))->toString();

        $previousWorstLevel = ResultLevel::findWorst(array_filter(array_map(
            function (Run $run) {
                $previousRun = $this->runRepository->findPrevious($run);

                return $previousRun && $previousRun->getSiteResult() ? ResultLevel::fromString($previousRun->getSiteResult()) : null;
            },
            $runs
        )))->toString();

        if ($previousWorstLevel === $worstLevel) {
            return;
        }

        $notification = (new RunResultNotification($worstLevel, $runs))
            ->withTranslator($this->translator)
            ->withTwig($this->twig);

        $this->notifier->send(
            $notification,
            ...array_map(
                static function (array $recipient) {
                    return (isset($recipient['phone']) && $recipient['phone']) ?
                        new Recipient($recipient['email'], $recipient['phone']) :
                        new Recipient($recipient['email']);
                },
                $this->notificationRecipients
            )
        );
    }
}
