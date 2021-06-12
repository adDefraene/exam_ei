<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends ApplicationType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('review', TextareaType::class, [
                'attr' => ['class' => "col-10 offset-1", 'maxLength' => 120, 'style' => 'resize:none']
            ])
            ->add('starsQuality', HiddenType::class)
            ->add('starsService', HiddenType::class)
            ->add('starsPunctuality', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
