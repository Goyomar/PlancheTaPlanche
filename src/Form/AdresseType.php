<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                'required' => true,
                'label' => 'Adresse :',
                'attr' => ['class' => 'input-full']
            ])
            ->add('ville', TextType::class, [
                'required' => true,
                'label' => 'Ville :',
                'attr' => ['class' => 'input-full']
            ])
            ->add('cp', TextType::class, [
                'required' => true,
                'label' => 'Code postal :',
                'attr' => ['class' => 'input-full']
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                "attr" => [
                    "class" => "btn-account"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
