<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\JokeModeration;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ModerJokeType extends AbstractType
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine,
    )
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options,): void
    {
        $categories = $this->doctrine->getRepository(Categories::class)->findAll();
        $builder
            ->add('category', EntityType::class,
                [
                    'class' => Categories::class,
                    'choice_label' => function (Categories $category) {
                        return $category->getName();
                    },
                    'placeholder' => 'Choose category',
                    'choices' => $categories,
                ])
            ->add('joke', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JokeModeration::class,

        ]);
    }
}
