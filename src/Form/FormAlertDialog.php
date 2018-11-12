<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/29/2018
 * Time: 10:24 AM
 */

namespace App\Form;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormAlertDialog extends AbstractType {

    public const DIALOG_ID = "confirmModal";
    public const POSITIF_ID = "AlertDialogID";
    private $dialogText = null;
    private $confirmText = null;
    private $ci =  null;
    private $alertExtraData = null;


    /**
     * FormAlertDialog constructor.
     *
     * the extraData are optionnal dependinding of the purpose in which the alertDialog is used.
     *
     *
     * @param null|string $dialogText
     * @param null|string $confirmText
     * @param ContainerInterface|null $ci {ce parametre est gerer par symphony donc a ne pas se soucier}
     * @param array|null $alertExtraData
     */
    public function __construct(?string $dialogText = null , ?string $confirmText = null ,string $alertExtraData = null, ContainerInterface $ci = null) {

        $this->confirmText = $confirmText;
        $this->dialogText = $dialogText;
        $this->ci = $ci;
        $this->alertExtraData = $alertExtraData;

    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add("ralonger" , ButtonType::class , [
            'attr'=>[
                "data-whatever"=>$this->ci->get('router')->generate('depart_admin', [], UrlGeneratorInterface::ABSOLUTE_PATH),
                'class'=>"btn btn-primary",
                "data-toggle"=>"modal",
                'data-target'=>"#".self::DIALOG_ID
            ]
        ])
        ->add("departId" , HiddenType::class , [
            'label'=>false,
            'mapped'=>false,
        ] )
        ->add("alertExtraData" , HiddenType::class , [
            "mapped"=>false,
            "label"=>false,
            "data"=>$this->alertExtraData,
        ]);
    }

    public function getDialogView() {
        return (!is_null($this->dialogText ) and !is_null($this->confirmText))
            ?'
                <div class="modal" id="'.self::DIALOG_ID.'">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Information</h4>
                      </div>
                      <div class="modal-body">
                        <p>'.$this->dialogText.'</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="button" id="AlertDialogID" class="btn btn-primary">'.$this->confirmText.'</button>
                      </div>
                    </div>
                  </div>
                </div>'
            :'';
    }
}