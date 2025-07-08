<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class Foobar
{
    private PaginationInterface $pagination;

    public function __construct(
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator,
        private RequestStack $requestStack,
    ) {
    }

    #[PreMount]
    public function preMount(): void
    {
        $dql   = "SELECT a FROM App\Entity\Post a";
        $query = $this->em->createQuery($dql);

        $this->pagination = $this->paginator->paginate(
            $query, /* query NOT result */
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );
    }

    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }
}
