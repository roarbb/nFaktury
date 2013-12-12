<?php
/**
 * User: roarbb
 * Date: 10.12.2013
 * Time: 22:32
 */

class Timesheet_shareRepository extends Repository
{
    public function canViewThisUser($userid, $timesheetOwner)
    {
        $find = $this->findBy(array(
            'user_id' => $userid,
            'timesheet_owner_id' => $timesheetOwner,
            'active' => 1,
        ));

        if($find->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function setShare($timesheetOwnerId, $timesheetRecieverId, $timesheetRecieverMail, $timesheetOwnerMail)
    {
        $insertData = array(
            'user_id' => $timesheetRecieverId,
            'timesheet_owner_id' => $timesheetOwnerId,
            'timesheet_owner_email' => $timesheetOwnerMail,
            'user_email' => $timesheetRecieverMail,
        );

        $this->getTable()->insert($insertData);
    }

    public function getOtherTimesheets($myUserId)
    {
        $where = array('user_id' => $myUserId, 'active' => 1);
        return $this->findBy($where);
    }

    public function getMyShares($myUserId)
    {
        $where = array('timesheet_owner_id' => $myUserId);
        return $this->findBy($where);
    }
} 