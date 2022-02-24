<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      attributes={"order"={"name": "ASC"}},
 *      mercure=true,
 *      normalizationContext={"groups": {"get_site", "timestamp"}},
 *      collectionOperations={"get": {"normalization_context": {"groups": {"get_sites", "timestamp"}}}},
 *      itemOperations={"get": {"normalization_context": {"groups": {"get_site", "timestamp"}}}}
 * )
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="site_slug_unique",columns={"slug"})},
 * )
 */
class Site implements HasTimestamp
{
    use HasTimestampImpl;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private ?string $name;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string")
     */
    private ?string $slug = null;

    /**
     * @Groups({"get_sites", "get_site", "get_run"})
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private ?string $url;

    /**
     * @Groups({"get_site"})
     * @ORM\OneToMany(targetEntity=ConfiguredCheck::class, mappedBy="site", cascade={"remove"})
     *
     * @var Collection<ConfiguredCheck>
     * @psalm-var Collection<int, ConfiguredCheck>
     */
    private /*Collection */$configuredChecks;

    public function __construct(string $name = null, string $url = null)
    {
        $this->id               = Uuid::uuid4();
        $this->name             = $name;
        $this->url              = $url;
        $this->configuredChecks = new ArrayCollection;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return Collection<ConfiguredCheck>
     * @psalm-return Collection<int, ConfiguredCheck>
     */
    public function getConfiguredChecks(): Collection
    {
        return $this->configuredChecks;
    }

    public function __toString(): string
    {
        return $this->getName() ?: 'A site with no name';
    }
}
