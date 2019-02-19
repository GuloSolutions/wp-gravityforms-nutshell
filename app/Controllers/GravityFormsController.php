<?php

namespace Controllers;

use Controllers\NutshellController;
use Controllers\GravityFormsDataController;

class GravityFormsController
{
    public $nutshell;
    private static $instance;
    private $contacts = [];
    public $gf_data;

    public function __construct()
    {
        if (!$this->checkIfGFActive()) {
            return;
        }

        $this->nutshell->getInstanceData();
        $this->nutshell->getUser();
        $this->contacts = $this->nutshell->findNutshellContacts();
    }

    public function checkIfGFActive()
    {
        if (class_exists('GFCommon')) {
            $this->nutshell = new NutshellController();
            return true;
        }
        return false;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function addContact($params)
    {
        $copy_params = $params;
        $this->nutshell->addContact($copy_params);
    }

    public function addNote($params)
    {
        error_log(print_r('add note', true));
        $this->nutshell->addNote($params);
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new GravityFormsController();
            return self::$instance;
        }
    }
}
