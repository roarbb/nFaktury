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

    public function setShare($userId, $timesheetOwnerId)
    {
        $insertData = array(
            'user_id' => $userId,
            'timesheet_owner_id' => $timesheetOwnerId,
        );

        $this->getTable()->insert($insertData);
    }
} 