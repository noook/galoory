<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Missing field "firstname"'
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Missing field "lastname"'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Missing field "email"'
                    ]),
                    new Email(),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Missing field "password"'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'The password must be at least 8 characters long'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
