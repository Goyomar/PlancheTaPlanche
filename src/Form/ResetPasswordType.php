<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['label' => 'Ancien mot de passe : ', 'attr' => ['class' => 'input-full']])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Nouveau mot de passe : ', 'attr' => ['class' => 'input-full']],
                'second_options' => ['label' => 'Répéter le mot de passe : ', 'attr' => ['class' => 'input-full']]
                ])
                
            ->add('reset', SubmitType::class, ['label' => 'Réinitilisater', 'attr' => ['class' => 'btn-danger my-1']])
        ;
    }

}
