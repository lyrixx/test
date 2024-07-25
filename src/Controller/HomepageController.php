<?php

namespace App\Controller;

use App\Jerome\JeromeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    public function index(JeromeInterface $jerome): never
    {
        dd($jerome->getName());
    }
}
