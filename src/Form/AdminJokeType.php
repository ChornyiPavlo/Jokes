<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Joke;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminJokeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class,
                [
                    'class' => Categories::class,
                    'choice_label' => function (Categories $category) {
                        return $category->getName();
                    },
                    'choice_value' => 'id',
                ])
            ->add('joke', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joke::class,
        ]);
    }
}
