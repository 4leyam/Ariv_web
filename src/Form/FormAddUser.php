<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/3/2018
 * Time: 4:34 AM
 */

namespace App\Form;


use App\Entity\NzelaUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormAddUser extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->

        add('pseudo' , TextType::class , ['label'=>false,'required'=>false,
            'constraints'=>[
                new Length([
                                "max"=>40,
                                'maxMessage'=>'Pseudo trop long synthetisez le svp'
                           ])
            ]])
            ->add('username' , TextType::class , ['label'=>false,
            'constraints'=>[
                new NotBlank(),
                new Length([
                               "max"=>30,
                               'maxMessage'=>'Nom trop long synthetisez le svp'
                           ])
            ]])
            ->add('emaiId' , EmailType::class , ['label'=>false,
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                                   "max"=>60,
                                   'maxMessage'=>'Adresse trop longue'
                               ])
//                    ,
//                    new Email( [
//                                    'message' => "Email Invalide.",
//                                    'checkMX' => true])
                    ]])
            ->add('prenom' , TextType::class , ['label'=>false ,
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                        "max"=>30,
                        'maxMessage'=>'Prenom trop long synthetisez le svp'
                               ])
                ]])
            ->add('telephone' , TelType::class , ['label'=>false ,
                'constraints'=>[
                    new NotBlank()
                ]])
            ->add('password' , PasswordType::class , ['label'=>false ,
                  'constraints'=>[
                        new NotBlank() ,
                        new Length(
                            [    'min'=>8
                                ,'max'=>100
                                ,'minMessage'=>"le mot de pass doit contenir 10 caractere Minimum"
                                ,'maxMessage'=>"le mot de pass doit contenir 10 caractere Minimum"
                            ])
                ]])
            ->add('passConfirm' , PasswordType::class , ['label'=>false ,
                'constraints'=>[
                    new NotBlank(),
                ]
            ]);

    }

}