<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/10/2018
 * Time: 12:43 AM
 */

namespace App\Form;


use App\Entity\Agences;
use App\Entity\Departs;
use App\Entity\NzelaUser;
use App\Repository\AgencesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormDepartAdminType extends AbstractType {


    private $isAgenceAdmin = null;
    private $user = null;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $add = $options['block_name'];
        $this->user = $options["user"];
        $this->isAgenceAdmin = $options["isAgenceAdmin"];
        $builder->

        add('imageBus' , FileType::class , ['label'=>false,
            'required'=>false,
            'data_class' => null
           ])
            ->add('origine' , TextType::class , [
                'attr'=>[
                  'class'=>'form-control',
                ],
            'constraints'=>[
                new NotBlank(),
                new Length([
                               "max"=>150,
                               'maxMessage'=>'Reduisez le nombre de caractere SVP (max 150)'
                           ])
            ]])
            ->add('destination' , TextType::class , ['label'=>"Destination",
                'attr'=>[
                  'class'=>'form-control'
                ],
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                                   "max"=>150,
                                   'maxMessage'=>'Reduisez le nombre de caractere SVP (max 150)'
                               ])
                ]])
            ->add('placeInit' , NumberType::class , ['label'=>'Place Prevues',
                'constraints'=>[
                    new NotBlank()
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]])
            ->add('formalite' , TimeType::class , ['label'=>false,
                'attr'=>[
                    'class'=>"form-control",
                ],
                "widget"=>'single_text',
                'constraints'=>[
                    new NotBlank()
                ]])
            ->add('depart' , TimeType::class , ['label'=>false,
                'attr'=>[
                    'class'=>"form-control"
                ],
                "widget"=>'single_text',
                'constraints'=>[
                    new NotBlank()
                ]])
            ->add('tarifAdult' , NumberType::class , ['label'=>"Tarif Adulte" ,
                'constraints'=>[
                    new NotBlank(),
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('tarifEnfant' , NumberType::class , ['label'=>"Tarif Enfant" ,
                'constraints'=>[
                    new NotBlank(),
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('dateDepart' , DateType::class , ['label'=>false ,
                'constraints'=>[
                    new NotBlank(),
                ],
                "widget"=>'single_text',

                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('tab' , NumberType::class , ['mapped'=>false,
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'hidden'=>'hidden'
                ]
            ])
            ->add('placeRestante' , NumberType::class , [
                'required'=>false,
                'label'=>false,
                'mapped'=>true,
                'attr'=>[
                    'hidden'=>'hidden'
                ]
            ])
            ->add('id' , NumberType::class , [
                'required'=>false,
                'label'=>false,
                'mapped'=>true,
                'attr'=>[
                    'hidden'=>'hidden'
                ]
            ]);
            if($add === 'add') {
                $builder->add("agence" , EntityType::class ,
                              [
                                  'label'=>false,
                                  'class'=>Agences::class,
                                  'query_builder' => function (EntityRepository $er) {
                                      /**
                                       * @var $er AgencesRepository
                                       */
                                      return $er->getFindRecentBuilder($this->isAgenceAdmin , $this->user);
                                  },
                                  'choice_label'=>"nomAgence",
                                  'constraints'=>[
                                      new NotBlank()
                                  ],
                                  'attr'=>[
                                      'class'=>"selectpicker show-menu-arrow",
                                      'data-style'=>"btn-warning",
                                      'data-live-search'=>"true",
                                      'label'=>false,

                                  ]
                              ]
                );
            }



    }

    /**
     * definit les valeures par defaut passe du tableau d'option donnee lors de la creation du formulaire.
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            'block_name' => 'set'
        );
    }

    public function getBlockPrefix() {
        return null;
    }

    /**
     * definit le type d'entite que ce formulaire traite.
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'block_name' => 'set',
            'user'=>null,
            'isAgenceAdmin'=>null,
            'data_class' => Departs::class,
        ));
    }


}