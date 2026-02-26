<?php

namespace App\Entity;

use App\Repository\PairingDestinationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PairingDestinationRepository::class)]
class PairingDestination
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::GUID)]
    public private(set) readonly string $id;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'destinations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        public private(set) readonly Pairing $pairing,

        #[ORM\Column(type: Types::TEXT)]
        public string $destinationUrl,
    ) {
        $this->id = Uuid::v7()->toRfc4122();
    }
}
