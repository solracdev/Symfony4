<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        // Al crear un formulario, si se defina en el metodo configureoptions la data_class,
        // este formulario cada child tiene que ser un atributo de la class, sino daria error,
        // hay una manera de añadir campos que no estan en la class / Entity, mirar mas abajo en el child ("termsAgreed")
       
        $builder->add("username", TextType::class)
                ->add("email", EmailType::class)
                ->add("plainPassword", RepeatedType::class, [
                    "type" => PasswordType::class,
                    "first_options" => ["label" => "Password"],
                    "second_options" => ["label" => "Repeated Password"] 
                ])
                ->add("fullName", TextType::class)
                // Al crear un child que no tiene atributo en la class hay que definir en el array de opciones
                // la key "mapped" como false, de otro modo daria un error.
                // Al ser un campo que no esta en la BBDD se tiene que comprobar al momento, por eso se usa
                // la key constraints con una nueva instancia del IsTrue()
                ->add("termsAgreed", CheckboxType::class, [
                    "mapped" => false,
                    "constraints" => new IsTrue(),
                    "label" => "I agree to the terms of service."
                ])
                ->add("Register", SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            "data_class" => User::class
        ]);
    }

}
