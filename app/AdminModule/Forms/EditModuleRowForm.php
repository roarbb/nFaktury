<?php

namespace AdminModule\Forms;

use Nette\Application\UI\Form as AppForm,
    Nette\Forms\Form,
    Nette\Utils\Html;


class EditModuleRowForm extends AppForm
{
    private $table;

    public function __construct($parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);

        $params = $parent->request->getParameters();
        $module = $parent->context->listingService->getModule($params['moduleid']);
        $this->table = $module->table;
        $fields = $parent->context->listingService->getFields($this->table);
        $showFields = $module->related('admin_module_column')->order('admin_module_column.id')->where('editable',"1");
        $moduleColumns = $parent->context->listingService->getModuleFields($module->id);

        $this->getElementPrototype()->setClass('form-horizontal');

        foreach ($showFields as $columnRow) {
            $columnDbInfo = $this->search($fields, $columnRow->name);
            if($columnDbInfo['Comment'] != "") {
                $columnName = str_replace('[*]', '', $columnDbInfo['Comment']);
            } else {
                $columnName = $columnRow->name;
            }

            //skontrolujem ci nemam vykreslit select
            if($columnRow->replacement_table) {
                $replacementArray = $parent->context->listingService->getReplacementArray($columnRow);
                $this->addSelect($columnRow->name, $columnName, $replacementArray);
                continue;
            }

            if($columnDbInfo['Type'] == 'text') { 
                $this->addTextArea($columnRow->name, $columnName); 
                continue; 
            }

            if( preg_match('/int\(.*?\)/', $columnDbInfo['Type'])) { 
                $this->addText($columnRow->name, $columnName);
                continue;
            }

            if( preg_match('/enum\((.*?)\)/', $columnDbInfo['Type'])) {

                //ziskam moznosti ktore su uvedene v zatvorke enum('1','0')
                preg_match('/\((.*?)\)/', $columnDbInfo['Type'], $matches);
                $options = explode(',', $matches[1]);
                $out = array(''=>'');

                //spavim si z moznosti pole pre selectbox
                foreach ($options as $option) {
                    $option = str_replace("'", '', $option);
                    $out[$option] = (string)$option;
                }

                $this->addSelect($columnRow->name, $columnName, $out);
                continue; 
            }

            if( preg_match('/datetime/', $columnDbInfo['Type'])) { 
                //pripojim do grida bunku, datetime sa bude chovat ako date
                $this->addText($columnRow->name, $columnName);

                continue; 
            }

            //ak to nie je specialny typ,
            //spravim len input
            $this->addText($columnRow->name, $columnName);
        }

        $this->onSuccess[] = array($this, 'processSuccess');
        $this->getElementPrototype()->novalidate = 'novalidate';

        // \Nette\Diagnostics\Debugger::dump($this->table);
        // \Nette\Diagnostics\Debugger::dump($params['id']);

        $defaults = $parent->context->listingService->getModuleEditRow($this->table, $params['id']);
        // \Nette\Diagnostics\Debugger::dump($defaults);
        
        $this->setDefaults($defaults);

        $this->addSubmit('submit', 'Uložiť')->setAttribute('class', 'btn btn-primary');
    }


    public function processSuccess(AppForm $form)
    {
        $values = $form->getValues();
        $params = $this->presenter->request->getParameters();
        $id = $params['id'];

        $this->presenter->context->listingService->updateModuleEditRow($this->table, $id, $values);

        $this->presenter->flashMessage('Dáta úspešne zmenené', 'success');
        $this->presenter->redirect(':admin:module:list', $params['moduleid']);
    }

    private function search($fields, $fieldname) {
        foreach ($fields as $field) {
            if($field['Field'] == $fieldname) {
                return $field;
            }
        }
    }
}