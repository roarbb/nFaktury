<?php
/**
 * User: roarbb
 * Date: 25.7.2013
 * Time: 21:59
 */

class Invoice_itemsRepository extends Repository
{

    public function insertItems($invoiceId, $item)
    {
        $item->invoice_id = $invoiceId;
        return $this->getTable()->insert($item);
    }

    public function updateItems($invoiceId, $invoiceItem)
    {
        $this->findBy(array('invoice_id' => $invoiceId))->update($invoiceItem);
    }
}