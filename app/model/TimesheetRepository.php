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

    public function isMineAndTodayTimesheet($timesheetId, $userId)
    {
        $timesheet = $this->fetchById($timesheetId);

        if ($timesheet !== FALSE && $timesheet->user_id == $userId && $timesheet->created->format('d.m.Y') == date('d.m.Y') ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $user_id
     * @return array|IRow[]
     */
    public function getTodaysTimesheets($user_id)
    {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->where('? = ?', new SqlLiteral('DATE_FORMAT(NOW(), "%Y-%m-%d")'), new SqlLiteral('DATE_FORMAT(`created`,"%Y-%m-%d")'))
            ->order('from')
            ->fetchAll();
    }

    public function deleteTimeSheet($timesheetId, $userId)
    {
        $this->findBy(array('user_id' => $userId, 'id' => $timesheetId))->delete();
    }

    public function getWorkHours($userId, $lunchTime, $month = NULL, $year = NULL)
    {
        $sumHours = 0;
        $sumMinutes = 0;
        $out = array();

        if($month && $year) {
            $timesheets = $this->getMonthlyTimesheet($month, $year, $userId);
        } else {
            $timesheets = $this->getTable()
                ->where('? = ?', new SqlLiteral('DATE_FORMAT(NOW(), "%Y-%m-%d")'), new SqlLiteral('DATE_FORMAT(`from`,"%Y-%m-%d")'))
                ->where('user_id', $userId)
                ->fetchAll();
        }

        if(!$timesheets) {
            return $this->sendEmptyWorktime();
        }

        foreach($timesheets as $timesheet) {
            $diff = $timesheet->to->diff($timesheet->from);
            $sumHours += (int)$diff->format('%h');
            $sumMinutes += (int)$diff->format('%i');
        }

        $time = (int)$sumHours * 60;
        $time += (int)$sumMinutes;
        $time -= (int)$lunchTime;

        if($time < 0) {
            return $this->sendEmptyWorktime();
        }

        $out['hours'] = (int)($time/60);
        $out['minutes'] = (int)($time%60);

        return $out;
    }

    private function sendEmptyWorktime()
    {
        $out['hours'] = 0;
        $out['minutes'] = 0;
        return $out;
    }

    public function getMonthlyTimesheetArray($month, $year, $userId)
    {
        $out = array();

        $timesheets = $this->getMonthlyTimesheet($month, $year, $userId);

        foreach ($timesheets as $timesheet) {
            $out[$timesheet->created->format('d')][] = $timesheet;
        }

        if(!empty($out)) {
            return $out;
        } else {
            return false;
        }
    }

    private function getMonthlyTimesheet($month, $year, $userId)
    {
        return $this->getTable()
            ->where('user_id', $userId)
            ->where('? = ?', new SqlLiteral('MONTH(`created`)'), $month)
            ->where('? = ?', new SqlLiteral('YEAR(`created`)'), $year)
            ->order('`from` ASC')
            ->fetchAll();
    }
}