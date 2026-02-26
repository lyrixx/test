<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PlanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/redirection-plans',
        ),
        new Get(
            uriTemplate: '/redirection-plans/{id}',
        ),
    ],
)]
#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    public private(set) string $id;

    public function __construct(
        #[ORM\Column()]
        public string $name,
    ) {
        $this->id = Uuid::v7()->toRfc4122();
    }
}
