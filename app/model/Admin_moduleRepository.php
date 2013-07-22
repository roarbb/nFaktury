<?php

class Admin_moduleRepository extends Repository {

    public function getModule($id) {
        return $this->getTable()->get($id);
    }

    public function getModuleId($tableName) {
        return $this->connection->table('admin_module')->where('table', $tableName)->fetch();
    }

    public function insertNewModule($tableName) {
        $data = array(
            'name' => $tableName,
            'table' => $tableName,
        );

        $row = $this->connection->table("admin_module")->insert($data);
        return $row["id"];
    }

    public function deleteModule($moduleId) {
        $this->connection->table("admin_module")->get($moduleId)->delete();
    }
}