<?php

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
}