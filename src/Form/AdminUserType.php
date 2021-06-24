<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminUserType extends ApplicationType
{
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
            ->add('Roles', ChoiceType::class, $this->getConfiguration("RÔLE", "", [
                'choices' => [
                    "User" => "ROLE_USER",
                    "Admin" => "ROLE_ADMIN"
                ]
            ]))
        ;

                // Data transformer
                $builder->get('Roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesArray) {
                         // transform the array to a string
                         return count($rolesArray)? $rolesArray[0]: null;
                    },
                    function ($rolesString) {
                         // transform the string back to an array
                         return [$rolesString];
                    }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
