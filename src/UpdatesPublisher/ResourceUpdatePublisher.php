<?php

declare(strict_types=1);

namespace App\UpdatesPublisher;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Model\Run;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class ResourceUpdatePublisher implements EventSubscriber
{
    private IriConverterInterface $iriConverter;

    private Publisher $publisher;

    private SerializerInterface $serializer;

    public function __construct(IriConverterInterface $iriConverter, Publisher $publisher, SerializerInterface $serializer)
    {
        $this->iriConverter = $iriConverter;
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Run) {
            return;
        }

        $this->publisher->__invoke(new Update(
            $this->iriConverter->getIriFromItem($object),
            $this->serializer->serialize($object, 'jsonld', ['groups' => ['get_run']]),
        ));
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }
}
