<?php

// src/Form/PresenceType.php
namespace App\Form;

use App\Entity\Presence;
use App\Entity\Employe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'nom', // ou autre champ comme prénom
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('statut', TextType::class)
            ->add('heureArrivee', TimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('heureDepart', TimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Presence::class,
        ]);
    }
}
