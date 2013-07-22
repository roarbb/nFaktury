<?php

namespace AdminModule;

use DependentSelectBox\DependentSelectBox;
use Grido\Components\Actions\Action;
use Grido\Components\Columns\Column;
use Grido\Components\Columns\Date;
use Grido\Components\Filters\Filter;
use Grido\Grid;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

final class ModulePresenter extends BasePresenter {

    /**
     * @var \Admin_moduleRepository
     */
    protected $admin_moduleRepository;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var \Admin_module_columnRepository
     */
    protected $listingColumnRepository;

    /**
     * @var \General_moduleRepository
     */
    protected $generalRepository;

    public function inject(\Admin_moduleRepository $listingRepository, Connection $connection, \Admin_module_columnRepository $listingColumnRepository, \General_moduleRepository $generalRepository){
        $this->connection = $connection;
        $this->admin_moduleRepository = $listingRepository;
        $this->listingColumnRepository = $listingColumnRepository;
        $this->generalRepository = $generalRepository;
    }


    public function startup() {
        parent::startup();
        DependentSelectBox::register('addDSelect');
    }

    function renderDefault() {
        $this->redirect(':admin:module:list', 1);
    }

    protected function createComponentGrid($name) {

        $moduleId = $this->params['id'];
//        dump($moduleId);
//        exit;

        $module = $this->admin_moduleRepository->getModule($moduleId);

        $table = $module->table;
        $fields = $this->generalRepository->getFields($table);
        $showFields = $module->related('admin_module_column')->order('admin_module_column.id')->where('viewable',"1");

        $grid = new Grid($this, $name);
        $grid->setModel($this->connection->table($table));

        foreach ($showFields as $columnRow) {
            $columnDbInfo = $this->search($fields, $columnRow->name);
            if($columnDbInfo['Comment'] != "") {
                $columnName = str_replace('[*]', '', $columnDbInfo['Comment']);
            } else {
                $columnName = $columnRow->name;
            }

            //najprv skontrolujem obdobu Joinovania
            if($columnRow->replacement_table) {
                $replacementArray = $this->generalRepository->getReplacementArray($columnRow);
                $grid->addColumn($columnRow->name, $columnName)->setReplacement($replacementArray);
                $grid->addFilter($columnRow->name, $columnName, Filter::TYPE_SELECT, $replacementArray);
                continue;
            }

            if($columnDbInfo['Type'] == 'text') {
                $grid->addColumn($columnRow->name, $columnName)->setTruncate(80);
                $grid->addFilter($columnRow->name, $columnName);
                continue;
            }

            if( preg_match('/int\(.*?\)/', $columnDbInfo['Type'])) {
                $grid->addColumn($columnRow->name, $columnName)->setSortable();
                $grid->addFilter($columnRow->name, $columnName, Filter::TYPE_NUMBER);
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

                $grid->addColumn($columnRow->name, $columnName)->setReplacement($out);
                $grid->addFilter($columnRow->name, $columnName, Filter::TYPE_SELECT, $out);
                continue;
            }

            if( preg_match('/datetime/', $columnDbInfo['Type'])) {
                //pripojim do grida bunku, datetime sa bude chovat ako date
                $grid->addColumn($columnRow->name, $columnName, Column::TYPE_DATE)->setSortable()->setDateFormat(Date::FORMAT_DATE);

                //meno bunky si zapisem, aby som ho mohol pouziv v callbacku
                $columnRowName = $columnRow->name;

                //custom podmienka pre filter
                $grid->addFilter($columnRow->name, $columnName, Filter::TYPE_DATE)
                    ->setCondition(Filter::CONDITION_CALLBACK, function($value) use($columnRowName) {

                        //custom podmienka pre datetime
                        $out = array();
                        $out[0] = '['.$columnRowName.'] =%s';
                        $out[1] = date('Y-m-d',strtotime($value));

                        return $out;
                    });

                continue;
            }

            $grid->addColumn($columnRow->name, $columnName);
            $grid->addFilter($columnRow->name, $columnName);

        }

        if ($moduleId == 1) {
            $grid->addAction('edit', 'Upraviť', Action::TYPE_HREF, ":admin:module:edit", array('moduleid' => $moduleId))
                ->setIcon('pencil');
            $grid->addAction('delete', 'Vymazať', Action::TYPE_HREF, ":admin:module:delete", array('moduleid' => $moduleId))
                ->setIcon('trash')
                ->setConfirm('Naozaj chcete vymazať tento modul?');
        } else {
            $grid->addAction('editrow', 'Upraviť', Action::TYPE_HREF, ":admin:module:rowedit", array('moduleid' => $moduleId))
                ->setIcon('pencil');
            $grid->addAction('deleterow', 'Vymazať', Action::TYPE_HREF, ":admin:module:rowdelete", array('moduleid' => $moduleId))
                ->setIcon('trash')
                ->setConfirm('Naozaj chcete vymazať tento záznam?');
        }

        $grid->setExporting($table);
    }

    private function search($fields, $fieldname) {
        foreach ($fields as $field) {
            if($field['Field'] == $fieldname) {
                return $field;
            }
        }
    }

    public function renderEdit($moduleid,$id) {}

    public function renderRowedit($moduleid,$id) {}

    public function renderNewrow($moduleid) {}

    public function renderList($id)
    {
        $this->template->moduleId = $id;

        //danger! grido filters don't work w/out this
    }

    public function renderDelete($moduleid,$id) {
        $this->admin_moduleRepository->deleteModule($id);
        $this->presenter->flashMessage('Modul vymazaný', 'success');
        $this->presenter->redirect(':admin:module:list', 1);
    }

    public function renderRowDelete($moduleid,$id) {
        $module = $this->admin_moduleRepository->getModule($moduleid);
        $table = $module->table;

        $this->generalRepository->deleteRowFromModule($id, $table);
        $this->flashMessage('Dáta úspešne zmazané', 'success');
        $this->redirect(':admin:module:list', $moduleid);
    }

    public function renderNew() {
        $this->template->tables = $this->generalRepository->getTables();
    }

    public function renderSet($id) {
        $this->template->fields = $this->generalRepository->getFields($id);
    }

    protected function createComponentInsertEditModuleForm($name) {

        DependentSelectBox::register('addDSelect');

        $params = $this->request->getParameters();

        if ($this->action == "edit") {
            $submitButtonName = "Uložiť";
            $module = $this->admin_moduleRepository->getModule($params['id']);
            $moduleColumns = $this->listingColumnRepository->getModuleFields($module->id);
            $table = $module->table;
        } else {
            $table = $params['id'];
            $submitButtonName = "Vložiť nový modul";
        }

        $fields = $this->generalRepository->getFields($table);
        $tables = $this->getAllTablesFromDatabase();

        $form = new Form($this, $name);

        foreach ($fields as $field) {

            if(!empty($field->Comment)) {
                $fldName = str_replace('[*]', '', $field->Comment);
            } else {
                $fldName = $field->Field;
            }
            $form->addGroup($field->Field);
            $form->addCheckbox('viewable_'.$field->Field, 'Viditeľné');
            $form->addCheckbox('editable_'.$field->Field, 'Editovateľné');
            $form->addSelect('table_'.$field->Field, '[ ' . $field->Field . ' ]' . ' je z tabuľky ...', $tables);

            $form->addDSelect('depend_id_'.$field->Field, '... v tejto tabuľke je to ...', $form['table_'.$field->Field], function($this) use($field, $form) {
                $v = $form['table_'.$field->Field]->getValue();
                return $this->getFields($v);
            });

            $form->addDSelect('depend_name_'.$field->Field, '... ale používateľovi to ukazuj ako ...', $form['table_'.$field->Field], function($this) use($field, $form) {
                $v = $form['table_'.$field->Field]->getValue();
                return $this->getFields($v);
            });

            if($this->isAjax()) {
                $form['depend_id_'.$field->Field]->addOnSubmitCallback(array($this, "invalidateControl"), "formSnippet");

                $form['depend_name_'.$field->Field]->addOnSubmitCallback(array($this, "invalidateControl"), "formSnippet");
            }
        }

        $form->setCurrentGroup(NULL);
        $presenter = $this;

        $form->addSubmit('show', $submitButtonName)
            ->setAttribute('class', 'btn btn-primary')
            ->onClick[] = function($button) use($form, $presenter) {
            $presenter->processInsertEditForm($button->form->values);
        };

        if ($this->action == "edit") {
            //ak modul upravujem, nacitam si povodne data
            foreach ($fields as $field) {

                $form['table_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_table);
                $form['depend_id_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_id_column);
                $form['depend_name_'.$field->Field]->setDefaultValue($moduleColumns[$field->Field]->replacement_name_column);

                $form['editable_' . $field->Field]->setDefaultValue(intval($moduleColumns[$field->Field]->editable));
                $form['viewable_' . $field->Field]->setDefaultValue(intval($moduleColumns[$field->Field]->viewable));
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

        return $form;
    }

    protected function createComponentEditModuleRowForm($name) {

        $params = $this->request->getParameters();

        $module = $this->admin_moduleRepository->getModule($params['moduleid']);
        $table = $module->table;
        $fields = $this->generalRepository->getFields($table);
        $showFields = $module->related('admin_module_column')->order('admin_module_column.id')->where('editable',"1");

        $form = new Form($this, $name);
        $form->setRenderer(new BootstrapRenderer());

        foreach ($showFields as $columnRow) {
            $columnDbInfo = $this->search($fields, $columnRow->name);
            if($columnDbInfo['Comment'] != "") {
                $columnName = str_replace('[*]', '', $columnDbInfo['Comment']);
            } else {
                $columnName = $columnRow->name;
            }

            //skontrolujem ci nemam vykreslit select
            if($columnRow->replacement_table) {
                $replacementArray = $this->generalRepository->getReplacementArray($columnRow);
                $form->addSelect($columnRow->name, $columnName, $replacementArray);
                continue;
            }

            if($columnDbInfo['Type'] == 'text') {
                $form->addTextArea($columnRow->name, $columnName);
                continue;
            }

            if( preg_match('/int\(.*?\)/', $columnDbInfo['Type'])) {
                $form->addText($columnRow->name, $columnName);
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

                $form->addSelect($columnRow->name, $columnName, $out);
                continue;
            }

            if( preg_match('/datetime/', $columnDbInfo['Type'])) {
                //pripojim do grida bunku, datetime sa bude chovat ako date
                $form->addText($columnRow->name, $columnName);

                continue;
            }

            //ak to nie je specialny typ,
            //spravim len input
            $form->addText($columnRow->name, $columnName);
        }

        $form->onSuccess[] = $this->processInsertEditModuleRow;


        if($this->action == 'rowedit') {
            $defaults = $this->generalRepository->getModuleEditRow($table, $params['id']);
            $form->setDefaults($defaults);
        }

        $form->addSubmit('submit', 'Uložiť')->setAttribute('class', 'btn btn-primary');
    }

    private function getAllTablesFromDatabase()
    {
        $tables = array('' => 'x x x x x');
        $dbTables = $this->generalRepository->getTables();

        foreach ($dbTables as $table) {
            $tables[$table[0]] = $table[0];
        }

        return $tables;
    }

    public function getFields($table) {
        $fields = array();
        $dbFields = $this->generalRepository->getFields($table);

        foreach ($dbFields as $field) {
            $fields[$field[0]] = $field[0];
        }

        return $fields;
    }

    public function processInsertEditForm($data) {

        $params = $this->request->getParameters();

        if ($this->action == "edit") {
            $module = $this->admin_moduleRepository->getModule($params['id']);
            $moduleId = $module->id;
            $table = $module->table;
        } else {
            $table = $params['id'];
            $moduleId = $this->getModuleIdOrInsert($table);
        }

        $fields = $this->getFields($table);

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
            $this->listingColumnRepository->insertModuleField($toDb);
            // }
        }

        if ($this->action == "edit") {
            $this->flashMessage('Modul úspešne upravený', 'success');
        } else {
            $this->flashMessage('Modul úspešne uložený', 'success');
        }

        $this->redirect(':admin:module:list', 1);
    }

    public function getModuleIdOrInsert($tableName)
    {
        $moduleId = $this->admin_moduleRepository->getModuleId($tableName);

        if($moduleId) {
            return (int)$moduleId->id;
        } else {
            return (int)$this->admin_moduleRepository->insertNewModule($tableName);
        }
    }

    /**
     * @param Form $form
     */
    public function processInsertEditModuleRow(Form $form)
    {
        $values = $form->getValues();
        $params = $this->request->getParameters();
        $module = $this->admin_moduleRepository->getModule($params['moduleid']);
        $table = $module->table;

        if($this->action == 'rowedit')
        {
            $id = $params['id'];
            $this->generalRepository->updateModuleEditRow($table, $id, $values);
            $this->flashMessage('Dáta úspešne zmenené', 'success');
        }

        if($this->action == 'newrow')
        {
            $this->generalRepository->createRow($table, $values);
            $this->flashMessage('Záznam bol úspešne pridaný.', 'success');
        }


        $this->redirect(':admin:module:list', $params['moduleid']);
    }
}