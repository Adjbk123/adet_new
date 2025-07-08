<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Village;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenoms')
            ->add('sexe')
            ->add('dateNaissance')
            ->add('lieuNaissance')
            ->add('telephone')
            ->add('email')
            ->add('village', EntityType::class, [
                'class' => Village::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
