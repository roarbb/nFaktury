<?php
use Nette\Utils\Strings;

/**
 * User: roarbb
 * Date: 25.7.2013
 * Time: 21:59
 */

class InvoiceRepository extends Repository
{
    public function getNextInvoiceNumber($userId)
    {
        $divideSign = "/";
        $where = array(
            'YEAR(date_of_issue)' => date('Y'),
            'user_id' => $userId,
        );

        $lastInvoice = $this->findBy($where)->order('id DESC')->limit(1)->fetch();
        if($lastInvoice) {
            $lastInvoiceNo = explode($divideSign,$lastInvoice->invoice_number);
            $nextNumber = intval($lastInvoiceNo[0])+1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 3, "0", STR_PAD_LEFT) . $divideSign . date('Y');
    }

    public function getVariableSign($userId)
    {
        $nextInvoiceNo = $this->getNextInvoiceNumber($userId);
        return Strings::reverse(str_replace('/','',$nextInvoiceNo)) . date('md');
    }

    public function updateInvoice($invoiceId, $data)
    {
        $this->fetchById($invoiceId)->update($data);
    }

    public function insertInvoice($data)
    {
        return $this->getTable()->insert($data);
    }

    public function deleteInvoice($invoiceId)
    {
        $this->fetchById($invoiceId)->delete();
    }

    public function isMine($invoiceId, $userId)
    {
        $owner = $this->fetchById($invoiceId);

        if($owner !== FALSE && $owner->user_id == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $this->fetchById($id)->delete();
    }
} 