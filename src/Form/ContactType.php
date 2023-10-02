<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class,[
                'attr' => [
                    'placeholder' => 'Entrez votre sujet'
                ]
            ])
            ->add('email', TextType::class,[
                'attr' => [
                    'placeholder' => 'Entrez votre adresse mail'
                ]
            ])
            ->add('message', TextareaType::class,[
                'attr' => [
                    'placeholder' => 'Entrez votre Message'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
