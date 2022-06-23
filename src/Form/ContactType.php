<?php

namespace App\Form;

use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email : ', 'attr' => ['class' => 'input-full']])
            ->add('objet', TextType::class, ['label' => 'Objet : ', 'attr' => ['class' => 'input-full']])
            ->add('message', TextareaType::class, ['label' => 'Message : ', 'attr' => ['class' => 'input-full']])  
            ->add('envoyez', SubmitType::class, ['label' => 'Envoyez', 'attr' => ['class' => 'btn-send']])
        ;
    }
}
