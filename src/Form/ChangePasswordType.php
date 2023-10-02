<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'disabled' => true
            ])
            
            ->add('firstname', TextType::class,[
                'disabled' => true,
                'label' => 'Mon prénom'
                ])
                ->add('lastname', TextType::class,[
                    'disabled' => true,
                    'label' => 'Mon nom'
                    ])
                ->add('old_password', PasswordType::class,[
                    'label'=> 'Mon mot de passe',
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Veuollez saisir votre mot de passe actuel'
                    ]
                ])
                ->add('new_password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'label' => 'Mon nouveau mot de passe',
                    'required' => true,
                    'first_options'  => [
                        'label' => 'Mon nouveau mot de Passe',
                        'attr' => [
                            'placeholder' => 'Merci de saisir votre nouveau mot de passe'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Répéter Mot de Passe',
                        'attr' => [
                            'placeholder' => 'Merci de confirmer nouveau votre email'
                        ]
                    ],    
                ])
                -> add('submit', SubmitType::class,[
                    'label' => "Mettre à jour"
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
