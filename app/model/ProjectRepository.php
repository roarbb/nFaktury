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

    public function updateProject($projectId, $data)
    {
        $this->fetchById($projectId)->update($data);
    }

    public function insertProject($data)
    {
        return $this->getTable()->insert($data);
    }

    public function deleteProject($projectId)
    {
        $this->fetchById($projectId)->delete();
    }

    public function getProjectsForUser($userId)
    {
        return $this->findBy(array(
                                 'user_id' => $userId,
                             ))->fetchPairs('id', 'name');
    }
} 