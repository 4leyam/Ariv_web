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
use App\Repository\AgencesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormAgenceAdminType extends AbstractType {



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $add = $options['block_name'];
        $builder->

        add('agenceLogo' , FileType::class , ['label'=>false,
            'required'=>false,
            'data_class' => null
           ])
            ->add('nomAgence' , TextType::class , [
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
            ->add('adresseAgence' , TextType::class , ['label'=>false,
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
            ->add('contactAgence' , TextType::class , ['label'=>false,
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                                   "max"=>150,
                                   'maxMessage'=>'Reduisez le nombre de caractere SVP (max 150)'
                               ])
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]])
            ->add('emailAgence' , TextType::class , ['label'=>false,
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                                   "max"=>150,
                                   'maxMessage'=>'Reduisez le nombre de caractere SVP (max 150)'
                               ])
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]])
            ->add('tab' , NumberType::class , ['mapped'=>false,
                'label'=>false,
                'attr'=>[
                    'hidden'=>'hidden',
                ]
            ])
            ->add('plusInfo' , TextareaType::class , ['label'=>false,
                'constraints'=>[
                    new NotBlank(),
                    new Length([
                                   "max"=>4000,
                                   'maxMessage'=>'Text trop long'
                               ])
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]])
            ->add('id' , NumberType::class , [
                'required'=>false,
                'label'=>false,
                'mapped'=>true,
                'attr'=>[
                    'hidden'=>'hidden'
                ]
            ]);



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
        $resolver->setDefaults(array(
                                   'data_class' => Agences::class,
                               ));
    }


}