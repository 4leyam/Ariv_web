<?php
/**
 * Created by PhpStorm.
 * User: El_Tailor
 * Date: 22/09/2018
 * Time: 14:39
 */

namespace App\Form;


use App\Entity\Commentaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("userName", TextType::class, [
            "attr" => [
                'class' => "form-control"
            ]
        ])->add("Commentaire", TextareaType::class, [
            "attr" => [
                'class' => "form-control",
            ]
        ])->add("avis", NumberType::class, [
            "attr" => [
                'class' => "form-control"
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    "max" => 1,
                    'maxMessage' => 'La note doit etre comprise entre 1 et 5'
                ])
            ]])->add("Commenter", SubmitType::class, [
            "attr" => [
                "class" => "btn btn-primary",
                "style" => "margin-top: 15px;margin-bottom: 15px"
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Commentaires::class]);
    }


}