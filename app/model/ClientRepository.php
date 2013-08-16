<?php
/**
 * Created by PhpStorm.
 * User: roarbb
 * Date: 23.7.2013
 * Time: 22:22
 */

class ClientRepository extends Repository
{
    /**
     * Funkcia zisti, ci dany klient patri pouzivatelovi
     *
     * @param $clientId
     * @param $userId
     * @return bool
     */
    public function isMineClient($clientId, $userId)
    {
        $owner = $this->fetchById($clientId);

        if($owner !== FALSE && $owner->user_id == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function updateClient($clientId, $data)
    {
        $this->fetchById($clientId)->update($data);
    }

    public function insertClient($data)
    {
        return $this->getTable()->insert($data);
    }

    public function deleteClient($clientId)
    {
        $this->fetchById($clientId)->delete();
    }

    public function getClientsForUser($userId)
    {
        return $this->findBy(array(
            'user_id' => $userId,
        ))->fetchPairs('id', 'name');
    }
} 