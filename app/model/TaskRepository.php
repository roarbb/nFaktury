<?php
/**
 * User: roarbb
 * Date: 12.10.2013
 * Time: 19:50
 */

class TaskRepository extends Repository
{
    /**
     * Funkcia zisti, ci dany task patri pouzivatelovi
     *
     * @param $taskId
     * @param $userId
     * @return bool
     */
    public function isMineTask($taskId, $userId)
    {
        $owner = $this->fetchById($taskId);

        if ($owner !== FALSE && $owner->user_id == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function updateTask($taskId, $data)
    {
        $this->fetchById($taskId)->update($data);
    }

    public function insertTask($data)
    {
        return $this->getTable()->insert($data);
    }

    public function deleteTask($taskId, $userId)
    {
        if($this->isMineTask($taskId, $userId)) {
            $this->fetchById($taskId)->delete();
            return true;
        } else {
            return false;
        }
    }
} 