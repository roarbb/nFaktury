<?php
use Nette\Database\SqlLiteral;

/**
 * Projet: faktury
 * Author: Matej Sajgal
 * Date: 12.11.2013
 */

class Timesheet_dataRepository extends Repository
{
    public function setLunchTime($userId, $lunchTime)
    {
        $timeInfo = $this->findBy(array('user_id' => $userId, 'day' => date('Y-m-d')))->fetch();

        if($timeInfo) {
            //update
            $timeInfo->update(array('lunch_in_minutes' => $lunchTime));
        } else {
            //insert
            $data = array(
                'user_id' => $userId,
                'lunch_in_minutes' => $lunchTime,
                'day' => date('Y-m-d'),
            );
            $this->getTable()->insert($data);
        }
    }

    public function getLunchTime($userId, $date)
    {
        return $this->findBy(array(
            'user_id' => $userId,
            'day' => $date,
        ))->fetch();
    }

    public function getMonthlyLunchTime($userId, $month, $year)
    {
        $out = 0;

        $timesheetData = $this->getMonthData($userId, $month, $year);

        foreach ($timesheetData as $data) {
            $out += (int)$data->lunch_in_minutes;
        }

        return $out;
    }

    private function getMonthData($userId, $month, $year)
    {
        return $this->getTable()
             ->where('user_id', $userId)
             ->where('? = ?', new SqlLiteral('MONTH(`day`)'), $month)
             ->where('? = ?', new SqlLiteral('YEAR(`day`)'), $year)
             ->fetchAll();
    }

    public function getMonthDataArray($userId, $month, $year)
    {
        $monthData = $this->getMonthData($userId, $month, $year);

        if(!$monthData) return false;

        $out = array();
        foreach ( $monthData as $data ) {
            $out[$data->day->format('d')] = $data;
        }

        return $out;
    }
}