<?php

namespace App\Form;

use App\Entity\PhotoPackage;
use App\Entity\User;
use App\Form\DataTransformer\DateTransformer;
use App\Form\DataTransformer\PhotoPackageTransformer;
use App\Form\DataTransformer\UserTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPhotoshootType extends AbstractType
{
    private UserTransformer $userTransformer;
    private DateTransformer $dateTransformer;
    private PhotoPackageTransformer $packageTransformer;
        

    public function __construct(
        UserTransformer $userTransformer,
        DateTransformer $dateTransformer,
        PhotoPackageTransformer $packageTransformer
    )
    {
        $this->userTransformer = $userTransformer;
        $this->dateTransformer = $dateTransformer;
        $this->packageTransformer = $packageTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
            ])
            ->add('date', DateType::class)
            ->add('package', EntityType::class, [
                'class' => PhotoPackage::class,
            ])
            ->add('comment', TextType::class)
        ;

        $builder->get('user')
            ->addModelTransformer($this->userTransformer);
        $builder->get('date')
            ->addModelTransformer($this->dateTransformer);
        $builder->get('package')
            ->addModelTransformer($this->packageTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
