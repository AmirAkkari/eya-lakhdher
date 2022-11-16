<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('service')
            ->add('client')
            ->add('description')
            ->add('service' , ChoiceType::class , [
                "choices" => [
                    "Choisissez le service" => "",
                    "Nettoyage" => "Nettoyage",
                    "Babysitting" => "Babysitting",
                    "Infirmerie" => "Infirmerie"
                ]
            ])    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
