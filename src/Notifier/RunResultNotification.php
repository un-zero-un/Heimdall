<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Model\Run;
use App\ValueObject\ResultLevel;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RunResultNotification extends Notification
{
    private const IMPORTANCES = [
        ResultLevel::ERROR   => Notification::IMPORTANCE_URGENT,
        ResultLevel::WARNING => Notification::IMPORTANCE_HIGH,
        ResultLevel::SUCCESS => Notification::IMPORTANCE_LOW,
    ];

    private const EMOJIS = [
        ResultLevel::ERROR   => "\u{1F6A8}",
        ResultLevel::WARNING => "\u{26A0}",
        ResultLevel::SUCCESS => "\u{1F49A}",
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
        $this->content(
            $twig->render(
                'notification/runResult.md.twig',
                ['runs' => $this->runs, 'worst_level' => $this->worstLevel],
            )
        );

        return $this;
    }
}
