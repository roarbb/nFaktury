<?php
/**
 * User: roarbb
 * Date: 12.10.2013
 * Time: 12:50
 */

class ProjectRepository extends Repository
{
    /**
     * Funkcia zisti, ci dany projekt patri pouzivatelovi
     *
     * @param $projectId
     * @param $userId
     * @return bool
     */
    public function isMineProject($projectId, $userId)
    {
        $owner = $this->fetchById($projectId);

        if ($owner !== FALSE && $owner->user_id == $userId) {
            return true;
        } else {
            return false;
        }
    }
} 