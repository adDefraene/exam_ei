<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminReviewType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('review', TextareaType::class, [
                'attr' => ['class' => "col-10 offset-1", 'maxLength' => 120, 'style' => 'resize:none']
            ])
            ->add('starsQuality', ChoiceType::class, 
                ['choices' => ["1" => 1,"2" => 2,"3" => 3,"4" => 4,"5" => 5,"6" => 6,"7" => 7,"8" => 8,"9" => 9,"10" => 10,]]
            )
            ->add('starsService', ChoiceType::class, 
                ['choices' => ["1" => 1,"2" => 2,"3" => 3,"4" => 4,"5" => 5,"6" => 6,"7" => 7,"8" => 8,"9" => 9,"10" => 10,]]
            )
            ->add('starsPunctuality', ChoiceType::class, 
                ['choices' => ["1" => 1,"2" => 2,"3" => 3,"4" => 4,"5" => 5,"6" => 6,"7" => 7,"8" => 8,"9" => 9,"10" => 10,]]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
