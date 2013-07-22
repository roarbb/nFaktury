<?php

class Admin_module_columnRepository extends Repository
{
    public function insertModuleField($data)
    {
        try {
            $this->getTable()->insert($data);
        } catch(PDOException $e) {
            $where = array(
                "admin_module_id" => $data['admin_module_id'],
                "name" => $data['name'],
            );
            $this->findBy($where)->update($data);
        }
    }

    public function getModuleFields($moduleId)
    {
        return $this->findBy(array('admin_module_id' => $moduleId))->fetchPairs('name');
    }
}