<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Checker\CheckResult;
use App\Model\Run;
use App\ValueObject\ResultLevel;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RunResultsNotifier
{
    private NotifierInterface $notifier;

    private Environment $twig;

    private TranslatorInterface $translator;

    /**
     * @var Map<string, string>[]
     */
    private array $notificationRecipients;

    public function __construct(
        NotifierInterface $notifier,
        Environment $twig,
        TranslatorInterface $translator,
        array $notificationRecipients
    )
    {
        $this->notifier               = $notifier;
        $this->translator             = $translator;
        $this->notificationRecipients = $notificationRecipients;
        $this->twig                   = $twig;
    }

    /**
     * @param Run[] $runs
     */
    public function notify(array $runs): void
    {
        $worstLevel = ResultLevel::findWorst(array_map(
            fn (Run $run) => ResultLevel::fromString($run->getSiteResult()),
            $runs
        ))->toString();

        $notification = (new RunResultNotification($worstLevel, $runs))
            ->withTranslator($this->translator)
            ->withTwig($this->twig);

        $this->notifier->send(
            $notification,
            ...array_map(
                static function (array $recipient) {
                    return (isset($recipient['phone']) && $recipient['phone']) ?
                        new AdminRecipient($recipient['email'], $recipient['phone']) :
                        new Recipient($recipient['email']);
                },
                $this->notificationRecipients
            )
        );
    }
}
