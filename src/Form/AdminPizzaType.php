<?php

namespace App\Form;

use App\Entity\Pizza;
use App\Entity\Ingredient;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityRepository;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminPizzaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', MoneyType::class)
            ->add('image', FileType::class, [
                "label" => "Image de la pizza (jpeg, png, gif)",
                "required" => false,
                "data_class" => null,
                'empty_data' => ''
            ])
            ->add('ingredients',  EntityType::class, array(
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'expanded'  => false,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.name', 'ASC');
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'select-tags'
                ]

            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pizza::class,
        ]);
    }
}
