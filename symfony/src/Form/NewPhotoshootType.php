<?php

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\DateTransformer;
use App\Form\DataTransformer\UserTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPhotoshootType extends AbstractType
{
    private UserTransformer $userTransformer;
    private DateTransformer $dateTransformer;
        

    public function __construct(
        UserTransformer $userTransformer,
        DateTransformer $dateTransformer
    )
    {
        $this->userTransformer = $userTransformer;
        $this->dateTransformer = $dateTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
            ])
            ->add('date', DateType::class)
            ->add('quantity', NumberType::class)
            ->add('comment', TextType::class, [
                'required' => false,
                'empty_data' => '',
            ])
        ;

        $builder->get('user')
            ->addModelTransformer($this->userTransformer);
        $builder->get('date')
            ->addModelTransformer($this->dateTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
