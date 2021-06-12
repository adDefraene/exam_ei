<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends ApplicationType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("PRÉNOM", "Votre prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("NOM", "Votre nom"))
            ->add('email', EmailType::class, $this->getConfiguration("ADRESSE MAIL", "Votre adresse mail"))
            ->add('address', TextType::class, $this->getConfiguration("ADRESSE", "Votre rue et numéro"))
            ->add('city', ChoiceType::class, $this->getConfiguration("VILLE", "", [
                'choices' => [
                    "Ath" => "Ath",
                    "Arbre" => "Arbre",
                    "Bouvignies" => "Bouvignies",
                    "Ghislenghien" => "Ghislenghien",
                    "Gibecq" => "Gibecq",
                    "Houtaing" => "Houtaing",
                    "Irchonwelz" => "Irchonwelz",
                    "Isières" => "Isières",
                    "Lanquesaint" => "Lanquesaint",
                    "Ligne" => "Ligne",
                    "Maffle" => "Maffle",
                    "Mainvault" => "Mainvault",
                    "Meslin-L'Évèque" => "Meslin",
                    "Moulbaix" => "Moulbaix",
                    "Ormeignies" => "Ormeignies",
                    "Ostiches" => "Ostiches",
                    "Rebaix" => "Rebaix",
                    "Villers-Notre-Dame" => "Villers-Notre-Dame",
                    "Villers-Saint-Amand" => "Villers-Saint-AMand"]
            ]))
            ->add('password',PasswordType::class, $this->getConfiguration("MOT DE PASSE", "Veuillez entrer un mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
