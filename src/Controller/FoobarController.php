<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class FoobarController extends AbstractController
{
    #[Route('/foobar', name: 'app_foobar')]
    public function index()
    {
        return $this->render('foobar/index.html.twig');
    }
}
