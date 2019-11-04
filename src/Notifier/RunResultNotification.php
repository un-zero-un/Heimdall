<?php

declare(strict_types=1);

namespace App\Notifier;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RunResultNotification extends Notification
{
    private const IMPORTANCES = [
        'error'   => Notification::IMPORTANCE_URGENT,
        'warning' => Notification::IMPORTANCE_HIGH,
        'success' => Notification::IMPORTANCE_LOW,
    ];

    private const EMOJIS = [
        'error'   => "\u{1F6A8}",
        'warning' => "\u{26A0}",
        'success' => "\u{1F49A}",
    ];

    private string $worstLevel;

    /**
     * @var Run[]
     */
    private array $runs;

    public function __construct(string $worstLevel, array $runs)
    {
        parent::__construct();

        $this->worstLevel = $worstLevel;
        $this->runs       = $runs;

        $this->emoji(self::EMOJIS[$worstLevel]);
        $this->importance(self::IMPORTANCES[$worstLevel]);
    }

    public function withTranslator(TranslatorInterface $translator): self
    {
        $this->subject(
            $this->getEmoji() . ' ' . $translator->trans('notification.run_result.subject.' . $this->worstLevel)
        );

        return $this;
    }

    public function withTwig(Environment $twig): self
    {
        $this->content($twig->render(
            'notification/runResult.md.twig',
            ['runs' => $this->runs, 'worst_level' => $this->worstLevel],
        ));

        return $this;
    }
}
