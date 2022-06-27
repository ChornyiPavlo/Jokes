<?php

namespace App\Form;

use App\Entity\Categories;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends AbstractType
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->doctrine->getRepository(Categories::class)->findAll();
        $builder
            ->add('id', ChoiceType::class,
                [
                    'choices' => $categories,
                    'choice_label' => function (Categories $category) {
                        return $category->getName();
                    },
                    'choice_value' => 'id',
                    'label' => 'Choosing a category of jokes',
                    'placeholder' => 'Choose category',
                ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}
