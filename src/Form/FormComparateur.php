<?php
/**
 * Created by PhpStorm.
 * User: layay
 * Date: 02-10-2018
 * Time: 09:07
 */

namespace App\Form;

use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class FormComparateur extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("origine" , EntityType::class , [
            'label'=>false,
            "mapped"=>false,
            'class'=>Location::class,
            'choice_label'=>function(Location $location =null){
                return ($location != null)
                    ?$location->getVille().'-|-'.$location->getPays()
                    :"";
            },
            'attr'=>[
                "class"=>"form-control"
            ]
        ])
            ->add("destination" , EntityType::class , [
            'label'=>false,
                'class'=>Location::class,
                'choice_label'=>function(Location $location=null){
                    return ($location != null)
                        ?$location->getVille().'-|-'.$location->getPays()
                        :"";
                },
            "mapped"=>false,
            'class'=>Location::class,
            'attr'=>[
                "class"=>"form-control"
            ]
        ])
            ->add("date" , DateType::class , [
                'label'=>false ,
                "mapped"=>false,
                "widget"=>'single_text',

                'attr'=>[
                    'class'=>"form-control",
                    'type'=>'date'
                ]
            ])
            ->add("prix" , NumberType::class , [
                'label'=>false ,
                "mapped"=>false,
                'attr'=>[
                    'class'=>"form-control"
                ]
            ])
            ->add("valider" , SubmitType::class , [
                'attr'=>[
                    'class'=>'btn btn-warning'
                ]
            ])
        ;
    }

}