<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      itemOperations={"get"},
 *      collectionOperations={"post"},
 * )
 * @UniqueEntity(fields={"endpoint"})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table()
 */
class BrowserNotificationSubscription
{
    use HasTimestampImpl;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    private string $endpoint;

    public function __construct(string $endpoint)
    {
        $this->id       = Uuid::uuid4();
        $this->endpoint = $endpoint;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
