<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ParameterProvider\ReadLinkParameterProvider;
use App\Repository\PairingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/redirection-plans/{plan}/pairings',
            uriVariables: [
                'plan' => new Link(
                    fromClass: Plan::class,
                    provider: ReadLinkParameterProvider::class,
                    security: "true", // We fake it, so simplify the reproducer !
                ),
            ],
            security: 'true', // Security is done at the uriVariable level
        ),
        new Patch(
            uriTemplate: '/redirection-plans/{plan}/pairings/{id}',
            uriVariables: [
                'plan' => new Link(
                    fromClass: Plan::class,
                    provider: ReadLinkParameterProvider::class,
                    security: "true", // We fake it, so simplify the reproducer !
                ),
            ],
            security: 'true', // Security is done at the uriVariable level
        ),
    ],
)]
#[ORM\Entity(repositoryClass: PairingRepository::class)]
class Pairing
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::GUID)]
    public private(set) readonly string $id;

    /** @var Collection<int, PairingDestination> */
    #[ORM\OneToMany(targetEntity: PairingDestination::class, mappedBy: 'pairing', cascade: ['persist', 'remove'])]
    public private(set) Collection $destinations;

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?PairingDestination $selectedDestination = null;

    public function __construct(
        #[ORM\ManyToOne()]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        public private(set) readonly Plan $plan,

        #[ORM\Column(type: Types::TEXT)]
        public string $sourceUrl,
    ) {

        $this->id = Uuid::v7()->toRfc4122();
        $this->destinations = new ArrayCollection();
    }
}
