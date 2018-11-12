<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/15/2018
 * Time: 11:12 AM
 */

namespace App\Controller;


class MenuContentDefinition {

    private $sideForm = true;
    private $mainForm = true;
    private $tab = 0;
    private $data = null;
    private $eventData = null;
    private $extraGlobalData = null;

    public function __construct (int $tab=null , ?bool $sideForm = null ,array $eventData = null , ?bool $mainForm = null ) {
        $this->tab = $tab;
        $this->eventData = $eventData;
        $this->sideForm = is_null($sideForm)
            ?true
            :$sideForm;
        $this->mainForm = is_null($mainForm)
            ?true
            :$mainForm;

    }

    /**
     * @return null
     */
    public function getEventData() {
        return $this->eventData;
    }

    /**
     * @param null $eventData
     */
    public function setEventData($eventData): void {
        $this->eventData = $eventData;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void {
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getData() {
        return $this->data;
    }


    /**
     * @return bool
     */
    public function isMainContent(): bool {
        return $this->mainForm;
    }

    /**
     * @return bool
     */
    public function isSideContent(): bool {
        return $this->sideForm;
    }


    /**
     * @return int
     */
    public function getTab(): int {
        return $this->tab;
    }

    /**
     * @param bool $mainForm
     */
    public function setMainForm(bool $mainForm): void {
        $this->mainForm = $mainForm;
    }

    /**
     * @param bool $sideForm
     */
    public function setSideForm(bool $sideForm): void {
        $this->sideForm = $sideForm;
    }

    /**
     * @param int $tab
     */
    public function setTab(int $tab): void {
        $this->tab = $tab;
    }

    /**
     * @return null
     */
    public function getExtraGlobalData()
    {
        return $this->extraGlobalData;
    }

    /**
     * @param null $extraGlobalData
     */
    public function setExtraGlobalData($extraGlobalData): void
    {
        $this->extraGlobalData = $extraGlobalData;
    }






}