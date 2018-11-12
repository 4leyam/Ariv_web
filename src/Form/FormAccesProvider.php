<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/24/2018
 * Time: 8:24 PM
 */

namespace App\Form;


use App\Controller\UnmappedForm;
use App\Entity\Agences;
use App\Repository\AgencesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormAccesProvider extends AbstractType{

    private $user = null;
    private $isAdminAgence = null;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->isAdminAgence = $options["isAdminAgence"];
        $this->user = $options["user"];
        $builder->
        add("agence" , EntityType::class , [
            "mapped"=>false,
            'label'=>false,
            'class'=>Agences::class,
            'query_builder' => function (EntityRepository $er) {
                /**
                 * @var $er AgencesRepository
                 */
                return $er->getFindRecentBuilder($this->isAdminAgence , $this->user);
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
        ])

        ->add("email" , EmailType::class , [
           "mapped"=>false,
           "label"=>false,
           "constraints"=>[
               new NotBlank(),

           ],
            "attr"=>[
                'class'=>'form-control',
                'placeholder'=>'e-mail utilisateur'
            ]
        ])
        ->add("submit" , SubmitType::class , array(
            "label"=>"Rechercher",
            'attr'=>[
                'class'=>"btn btn-warning"
            ]
        ));

    }


    /**
     * definit le type d'entite que ce formulaire traite.
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'isAdminAgence'=>null,
            'user'=>null,
            'data_class' => UnmappedForm::class,
        ));
    }

}