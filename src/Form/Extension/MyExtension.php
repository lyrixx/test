<?php
namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MyExtension extends AbstractTypeExtension
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['class'] = 'border-red';
    }

    public static function getExtendedTypes(): iterable
    {
        yield TextType::class;
        /*
        1. uncomment the line below
        2. don't clear your cache
        3. reload the homepage
        4. see the "Age" field has NO red border
        5. clear your cache
        6. reload the homepage
        7. see the "Age" field has a red border
        */
        // yield NumberType::class;
    }
}
