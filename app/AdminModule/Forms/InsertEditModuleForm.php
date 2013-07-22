<?php

namespace AdminModule\Forms;

use DependentSelectBox\DependentSelectBox;
use Nette\Application\UI\Form as AppForm;
use Nette\Forms\Form;
use Nette\Utils\Strings;

class InsertEditModuleForm extends AppForm {

    public $presenterObj;

    public function __construct($parent = NULL, $name = NULL) {

        parent::__construct($parent, $name);

        DependentSelectBox::register('addDSelect');
        $this->presenterObj = $parent;

        $params = $this->presenterObj->request->getParameters();

        if ($this->presenterObj->action == "edit") {
            $submitButtonName = "Uložiť";
            $module = $this->presenterObj->context->listingService->getModule($params['id']);
            // \Nette\Diagnostics\Debugger::dump($module);
            // \Nette\Diagnostics\Debugger::dump($params);
            // exit;
            $moduleColumns = $this->presenterObj->context->listingService->getModuleFields($module->id);
            $table = $module->table;
            // $this->setAction($this->presenterObj->link(":Admin:Module:edit", $params));
        } else {
            $table = $params['id'];
            $submitButtonName = "Vložiť nový modul";
        }
        
        $fields = $this->presenterObj->context->listingService->getFields($table);
        $tables = $this->getTables($this);

        //iba preto ze $this, nemozem pouzit v anonymnej funkcii
        //use($this) je hlupost :)
        $form = $this;

        foreach ($fields as $field) {

            if(!empty($field->Comment)) {
                $fldName = str_replace('[*]', '', $field->Comment);
            } else {
                $fldName = $field->Field;
            }
            $this->addGroup($field->Field);
            $this->addCheckbox('viewable_'.$field->Field, 'Viditeľné');
            $this->addCheckbox('editable_'.$field->Field, 'Editovateľné');
            $this->addSelect('table_'.$field->Field, '[ ' . $field->Field . ' ]' . ' je z tabuľky ...', $tables);

            $this->addDSelect('depend_id_'.$field->Field, '... v tejto tabuľke je to ...', $this['table_'.$field->Field], function($this) use($field, $form) {
                $v = $this['table_'.$field->Field]->getValue();
                return $form->getFields($v);
            });

            $this->addDSelect('depend_name_'.$field->Field, '... ale používateľovi to ukazuj ako ...', $this['table_'.$field->Field], function($this) use($field, $form) {
                $v = $this['table_'.$field->Field]->getValue();
                return $form->getFields($v);
            });

            if($this->presenterObj->isAjax()) {
                $this['depend_id_'.$field->Field]->addOnSubmitCallback(function() use($form) {
                    $form->presenterObj->invalidateControl('formSnippet');
                });

                $this['depend_name_'.$field->Field]->addOnSubmitCallback(function() use($form) {
                    $form->presenterObj->invalidateControl('formSnippet');
                });
            }
        }
        
        $this->setCurrentGroup(NULL);

        $this->addSubmit('show', $submitButtonName)
            ->setAttribute('class', 'btn btn-primary')
            ->onClick[] = function($button) use($form) {
                $form->processSuccess($button->form->values);
            };

        if ($parent->action == "edit") {
            //ak modul upravujem, nacitam si povodne data
            foreach ($fields as $field) {
                
                $this['table_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_table);
                $this['depend_id_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_id_column);
                $this['depend_name_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_name_column);

                $this['editable_' . $field->Field]->setDefaultValue(intval($moduleColumns[$field->Field]->editable));
                $this['viewable_' . $field->Field]->setDefaultValue(intval($moduleColumns[$field->Field]->viewable));
            }
            
            // \Nette\Diagnostics\Debugger::dump($moduleColumns['id']->viewable);

            // \Nette\Diagnostics\Debugger::dump('-----------------');

            // foreach ($moduleColumns as $name => $data) {
            //     \Nette\Diagnostics\Debugger::dump($name . ': ' . $data->viewable);
            // }
            // \Nette\Diagnostics\Debugger::dump($module);
        }

        // \Nette\Diagnostics\Debugger::dump($this);
        // exit;

        return $this;
    }

    public function processSuccess($data) {

        $params = $this->presenterObj->request->getParameters();

        if ($this->presenterObj->action == "edit") {
            $module = $this->presenterObj->context->listingService->getModule($params['id']);
            $moduleId = $module->id;
            $table = $module->table;
        } else {
            $table = $params['id'];
            $moduleId = $this->getModuleIdOrInsert($table);
        }

        $fields = $this->getFields($table);
        $toDb = array();

        $prefixes = array(
            'editable' => 'editable_',
            'viewable' => 'viewable_',
            'replacement_table' => 'table_',
            'replacement_id_column' => 'depend_id_',
            'replacement_name_column' => 'depend_name_',
        );

        foreach ($fields as $fieldName) {
            $toDb = array();
            $toDb['admin_module_id'] = $moduleId;
            $toDb['name'] = $fieldName;
            foreach ($prefixes as $dbColumnName => $prefix) {
                if(is_bool($data[$prefix.$fieldName])) {
                    if($data[$prefix.$fieldName]) {
                        $toDb[$dbColumnName] = "1";
                    } else {
                        $toDb[$dbColumnName] = "0";
                    }
                } else {
                    if (isset($data[$prefix.$fieldName]) && !empty($data[$prefix.$fieldName])) {
                        $toDb[$dbColumnName] = $data[$prefix.$fieldName];
                    }
                }
            }

            //edit or update field in module
            // if ($this->presenterObj->action == "edit") {
            //     //@TODO: upravit zaznam v DB - dokoncit
            // } else {
                $this->presenterObj->context->listingService->insertModuleField($toDb);
            // }
        }

        if ($this->presenterObj->action == "edit") {
            $this->presenterObj->flashMessage('Modul úspešne upravený', 'success');
        } else {
            $this->presenterObj->flashMessage('Modul úspešne uložený', 'success');
        }

        $this->presenterObj->redirect(':admin:module:list', 1);
    }

    public function getModuleIdOrInsert($tableName) {
        $moduleId = $this->presenterObj->context->listingService->getModuleId($tableName);

        if($moduleId) {
            return (int)$moduleId->id;
        } else {
            return (int)$this->presenterObj->context->listingService->insertNewModule($tableName);
        }
    }

    public function getTables($presenter) {
        $tables = array('' => 'x x x x x');
        $dbTables = $this->presenterObj->context->listingService->getTables();

        foreach ($dbTables as $table) {
            $tables[$table[0]] = $table[0];
        }

        return $tables;
    }

    public function getFields($table) {
        $fields = array();
        $dbFields = $this->presenterObj->context->listingService->getFields($table);

        foreach ($dbFields as $field) {
            $fields[$field[0]] = $field[0];
        }

        return $fields;
    }
}