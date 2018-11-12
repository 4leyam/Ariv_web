<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/3/2018
 * Time: 2:39 AM
 */

namespace App\Form;


use App\Entity\NzelaUser;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormLogin extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = new NzelaUser();
        $builder->add('emaiId' , TextType::class , array('label' => false ,
            "constraints"=>[
                new NotBlank(['message'=>'ce champ Obligatoire']) ,
                new Length([
                    'max'=>100,
                    'maxMessage'=>"nom d'Utilisateur trop long"
                           ])
            ]) )
            ->add('password' , PasswordType::class , array('label' => false ,
                'constraints'=> [
                    new NotBlank(['message'=>'ce champ Obligatoire']) ,
                    new Length([
                        'min'=>8,
                        'max'=>100,
                        'minMessage'=>"Le mot de passe Entree est trop Court",
                        'maxMessage'=>"Le mot de passe Entree est trop Long"
                               ])
                ]))
        ;
    }

    public function getBlockPrefix() {
        return null;
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//                                   'data_class' => Task::class,
//                               ));
//    }

}