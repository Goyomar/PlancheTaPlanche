<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email : ', 'attr' => ['class' => 'input-full']])
            ->add('nom', TextType::class, ['label' => 'Nom : ', 'attr' => ['class' => 'input-full'], 'constraints' => [
                new Length([
                    'min' => 1,
                    'max' => 30,
                ]),
            ],])
            ->add('prenom', TextType::class, ['label' => 'Prenom : ', 'attr' => ['class' => 'input-full'], 'constraints' => [
                new Length([
                    'min' => 2,
                    'minMessage' => "Le prénom doit posséder 2 caractère minimum",
                    'max' => 30,
                ]),
            ],])
            ->add('newletter', CheckboxType::class, ['label' => 'Inscription a la newsletter : '])
            ->add('modifier', SubmitType::class, ['attr' => ['class' => 'btn-account my-1']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
