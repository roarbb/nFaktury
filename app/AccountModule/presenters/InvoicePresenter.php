<?php
/**
 * User: roarbb
 * Date: 24.7.2013
 * Time: 21:22
 */

namespace AccountModule;


use Grido\Grid;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

class InvoicePresenter extends BasePresenter
{
    /**
     * @var \ClientRepository
     */
    private $clientRepository;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var \InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var \Invoice_itemsRepository
     */
    private $invoiceItemsRepository;

    public function inject(\InvoiceRepository $invoiceRepository, \Invoice_itemsRepository $invoiceItemsRepository, Connection $connection, \ClientRepository $clientRepository)
    {
        $this->connection = $connection;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemsRepository = $invoiceItemsRepository;
        $this->clientRepository = $clientRepository;
    }

    protected function createComponentGrid($name) {

        $table = 'invoice';
        $grid = new Grid($this, $name);
        $grid->setModel($this->connection->table($table)->where('user_id', $this->user->getId()));

        $grid->addColumn('invoice_number', '#');
        $grid->addFilter('invoice_number', 'invoice_number');

        $grid->addColumn('client_id', 'ID Klienta');
        $grid->addFilter('client_id', 'client_id');

        $grid->addColumn('variable_sign', 'Variabilný symbol');
        $grid->addFilter('variable_sign', 'variable_sign');

        $grid->addColumn('date_of_issue', 'Dátum vyhotovenia');
        $grid->addFilter('date_of_issue', 'date_of_issue');

        $grid->addColumn('maturity_date', 'Dátum splatnosti');
        $grid->addFilter('maturity_date', 'maturity_date');

        $grid->addAction('edit', 'Upraviť')->setIcon('pencil');
        $grid->addAction('markPayed', 'Zaplatená')->setIcon('ok');
        $grid->addAction('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať túto faktúru?');

        $grid->setExporting($table);
    }

    protected function createComponentInsertEditInvoiceForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $clients = $this->clientRepository->findBy(array('user_id' => $this->user->getId()))->fetchPairs('id', 'name');
        $nextInvoiceNo = $this->invoiceRepository->getNextInvoiceNumber($this->user->getId());
        $today = date('d.m.Y');
        $nextWeek = date('d.m.Y', strtotime("+7 day"));

        $form->addSelect('client_id', 'Klient', $clients)->setPrompt('Vyberte klienta')->setRequired('Vyberte klienta ktorému chcete faktúru vystaviť.');
        $form->addText('invoice_number', 'Číslo faktúry')->setDefaultValue($nextInvoiceNo);
        $form->addText('variable_sign', 'Variabilný symbol')->setDefaultValue($this->invoiceRepository->getVariableSign($this->user->getId()));
        $form->addText('maturity_date', 'Dátum splatnosti')->setDefaultValue($nextWeek);
        $form->addText('tax_duty_date', 'Daňová povinnosť')->setDefaultValue($today);
        $form->addText('delivery_date', 'Dátum dodania')->setDefaultValue($today);
        $form->addText('date_of_issue', 'Dátum vyhotovenia')->setDefaultValue($today);

        $form->onSuccess[] = $this->invoiceFormSubmitted;

        if($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť');
            $invoiceId = $this->getParameter('id');
            $invoiceData = $this->invoiceRepository->fetchById($invoiceId);
            $form->setDefaults($invoiceData);
        } else {
            $form->addSubmit('submit', 'Vložiť');
        }

        return $form;
    }

    public function invoiceFormSubmitted(Form $form)
    {
        $data = $form->getValues();

        if($this->action === 'edit') {
            $invoiceId = $this->getParameter('id');
            $this->invoiceRepository->updateInvoice($invoiceId, $data);

            $this->flashMessage('Faktúra úspešne upravená.', 'success');
            $this->redirect(':Account:invoice:');
        } else {
            $data->user_id = $this->user->getId();

            $data->maturity_date = $this->getRightFormat($data->maturity_date);
            $data->tax_duty_date = $this->getRightFormat($data->tax_duty_date);
            $data->delivery_date = $this->getRightFormat($data->delivery_date);
            $data->date_of_issue = $this->getRightFormat($data->date_of_issue);

            $this->invoiceRepository->insertInvoice($data);

            $this->flashMessage('Faktúra úspešne vytvorená.', 'success');
            $this->redirect(':Account:invoice:');
        }
    }

    protected function getRightFormat($date)
    {
        return date('Y-m-d', strtotime($date));
    }
} 