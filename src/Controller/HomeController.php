<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('age', NumberType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
            ->getForm();

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}
