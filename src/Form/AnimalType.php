<?php
/**
 * Created by IntelliJ IDEA.
 * User: Elio
 * Date: 21/03/2018
 * Time: 9:17
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'Dog' => "dog",
                    'Cat' => "cat",
                    'Reptile' => "reptile",
                    'Birds' => "bird",
                    'Other' => "other",
                ),
            ))
            ->add('other',TextType::class, array('required' => false,))
            ->add('birthdate', BirthdayType::class)
            ->add('create', SubmitType::class, array("attr" => array("class" => "button")));
            //->add('cancel',ResetType::class);
    }
}