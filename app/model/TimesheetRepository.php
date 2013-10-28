<?php
/**
 * Projet: faktury
 * Author: Matej Sajgal
 * Date: 27.10.2013
 */

class TimesheetRepository extends Repository
{
    public function insertTimeRow($data)
    {
        return $this->getTable()->insert($data);
    }
}