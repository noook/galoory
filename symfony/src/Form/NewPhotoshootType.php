<?php

namespace App\Form;

use App\Entity\PhotoPackage;
use App\Entity\User;
use App\Form\DataTransformer\DateTransformer;
use App\Form\DataTransformer\PhotoPackageTransformer;
use App\Form\DataTransformer\UserTransformer;
use App\Repository\PhotoPackageRepository;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPhotoshootType extends AbstractType
{
    private PhotoPackageRepository $photoPackageRepository;
    private UserTransformer $userTransformer;
    private DateTransformer $dateTransformer;
    private PhotoPackageTransformer $packageTransformer;
        

    public function __construct(
        PhotoPackageRepository $photoPackageRepository,
        UserTransformer $userTransformer,
        DateTransformer $dateTransformer,
        PhotoPackageTransformer $packageTransformer
    )
    {
        $this->photoPackageRepository = $photoPackageRepository;
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
            ->add('expiration', DateType::class)
            ->add('package', EntityType::class, [
                'class' => PhotoPackage::class,
                'choices' => $this->photoPackageRepository->findAll(),
            ])
        ;

        $builder->get('user')
            ->addModelTransformer($this->userTransformer);
        $builder->get('expiration')
            ->addModelTransformer($this->dateTransformer);
        $builder->get('package')
            ->addModelTransformer($this->packageTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
