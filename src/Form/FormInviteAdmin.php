<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/26/2018
 * Time: 6:17 PM
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormInviteAdmin extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add("email" , EmailType::class ,  [
            "mapped"=>false ,
            "constraints"=>[
                new NotBlank()
            ],
            'attr'=>[
            'class'=>'form-control'
        ]
        ])
        ->add("inviter" , SubmitType::class , [
            'label'=>"Inviter" ,
            'attr'=>[
                'class'=>'btn btn-warning'
            ]
        ])
        ;
    }


}