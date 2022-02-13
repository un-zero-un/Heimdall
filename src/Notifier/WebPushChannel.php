<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Repository\BrowserNotificationSubscriptionRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Serializer\SerializerInterface;

class WebPushChannel implements ChannelInterface
{
    private WebPush $webPush;

    private BrowserNotificationSubscriptionRepository $subscriptionRepository;

    private SerializerInterface $serializer;

    public function __construct(
        WebPush $webPush,
        BrowserNotificationSubscriptionRepository $subscriptionRepository,
        SerializerInterface $serializer
    )
    {
        $this->webPush                = $webPush;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->serializer             = $serializer;
    }

    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null): void
    {
        foreach ($this->subscriptionRepository->findAll() as $subscription) {
            $pushSubscription = Subscription::create([
                'endpoint' => $subscription->getEndpoint(),
                'keys'     => $subscription->getKeys(),
            ]);

            $this->webPush->sendNotification(
                $pushSubscription,
                $this->serializer->serialize($notification, 'json')
            );
        }

        foreach ($this->webPush->flush() as $report) {

        }
    }

    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return true;
    }
}
