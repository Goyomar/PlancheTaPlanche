<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SortBarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', EntityType::class, [
                'required' => false,
                'class' => Categorie::class,
                'label' => 'Categorie :'
            ])
            // ->add('taille')
            ->add('promo', CheckboxType::class, [
                'required' => false,
                'label' => 'Promo :'
            ])
            ->add('prixUn', NumberType::class, [
                'required' => false,
                'label' => 'Prix de :'
            ])
            ->add('prixDeux', NumberType::class, [
                'required' => false,
                'label' => ' à '
            ])
            ->add('filtrer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-action'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
