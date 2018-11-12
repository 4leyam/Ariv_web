<?php
/**
 * Created by PhpStorm.
 * User: El_Tailor
 * Date: 20/09/2018
 * Time: 13:50
 */

namespace App\Form;


use App\Entity\Departs;
use App\Repository\DepartsRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ComparateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("departs", EntityType::class, [
            'class' => Departs::class,
            'query_builder' => function (EntityRepository $repository){
                /** $@var DepartsRepository $repository */
                return $repository->find('origin');
            }
        ]);
    }

}