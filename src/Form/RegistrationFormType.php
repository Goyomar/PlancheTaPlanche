<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom :', 'attr' => ['placeholder' => 'Dupont', 'class' => 'input-split']])
            ->add('prenom', TextType::class, ['label' => 'Prénom :', 'attr' => ['placeholder' => 'Thierry', 'class' => 'input-split']])
            ->add('email', EmailType::class, ['label' => 'Adresse mail :', 'attr' => ['placeholder' => 'do@kickflip.sb', 'class' => 'input-full']])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe :', 'attr' => ['placeholder' => 'Password', 'class' => 'input-full']],
                'second_options' => ['label' => 'Répeter mot de passe :', 'attr' => ['placeholder' => 'Repeat Password', 'class' => 'input-full']],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 30,
                    ]),
                    // NEW REGEX
                ],
            ])
            ->add('newletter', CheckboxType::class, [
                'label' => 'S\'inscrire a la newsletter',
                'required' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter nos CGU et CGV',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisations',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
