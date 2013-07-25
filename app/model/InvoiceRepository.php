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
        $where = array(
            'YEAR(date_of_issue)' => date('Y'),
            'user_id' => $userId,
        );
        $count = $this->findBy($where)->count();

        return str_pad($count+1, 3, "0", STR_PAD_LEFT) . '/' . date('Y');
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
} 