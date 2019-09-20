<?php

declare(strict_types=1);

namespace App\Model;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util\Urlizer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
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
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private string $url;

    /**
     * @ORM\OneToMany(targetEntity=ConfiguredCheck::class, mappedBy="site")
     *
     * @var Collection<int, ConfiguredCheck>
     */
    private Collection $configuredChecks;

    public function __construct(string $name, string $url)
    {
        $this->id               = Uuid::uuid4();
        $this->slug             = Urlizer::urlize($name);
        $this->name             = $name;
        $this->url              = $url;
        $this->configuredChecks = new ArrayCollection;

        $this->initialize();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Collection<int, ConfiguredCheck>
     */
    public function getConfiguredChecks(): Collection
    {
        return $this->configuredChecks;
    }
}
