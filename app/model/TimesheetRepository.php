<?php
use Nette\Database\SqlLiteral;

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

    public function updateTimeRow($rowId, $userId, $data)
    {
        $this->findBy(array('id' => $rowId, 'user_id' => $userId))->update($data);
    }

    public function isMineTimesheet($timesheetId, $userId)
    {
        $owner = $this->fetchById($timesheetId);

        if ($owner !== FALSE && $owner->user_id == $userId) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $user_id
     *
     * @return array|IRow[]
     */
    public function getTodaysTimesheets($user_id)
    {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->where('? = ?', new SqlLiteral('DATE_FORMAT(NOW(), "%Y-%m-%d")'), new SqlLiteral('DATE_FORMAT(`from`,"%Y-%m-%d")'))
            ->order('from')
            ->fetchAll();

    }
}