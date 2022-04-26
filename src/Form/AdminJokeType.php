<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Repository\CategoriesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminJokeType extends AbstractType
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function buildForm(FormBuilderInterface $builder, array $options, ): void
    {
        $category = $this->doctrine->getRepository(Categories::class)->findAll();

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
