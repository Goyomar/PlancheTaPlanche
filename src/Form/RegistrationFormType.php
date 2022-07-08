<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
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
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                 'attr' => ['placeholder' => 'Dupont', 'class' => 'input-full'],
                 'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 30,
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :', 
                'attr' => ['placeholder' => 'Thierry', 'class' => 'input-full'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => "Le prénom doit posséder 2 caractère minimum",
                        'max' => 30,
                    ]),
                ],
                ])
            ->add('email', EmailType::class, ['label' => 'Adresse mail :', 'attr' => ['placeholder' => 'do@kickflip.sb', 'class' => 'input-full']])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe :', 'attr' => ['placeholder' => 'Password', 'class' => 'input-full']],
                'second_options' => ['label' => 'Répeter mot de passe :', 'attr' => ['placeholder' => 'Repeat Password', 'class' => 'input-full']],
                'mapped' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'match' => true,
                        'message' => 'Le mot de passe doit contenir : min 8 caractère, un nombre, une minuscule, une majuscule et un caractère spécial',
                    ]),
                ]
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
