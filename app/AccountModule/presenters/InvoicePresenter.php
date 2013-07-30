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
use Nette\Database\SelectionFactory;
use PdfResponse;

class InvoicePresenter extends BasePresenter
{

    /**
     * @var \UserRepository
     */
    private $userRepository;

    /**
     * @var \ClientRepository
     */
    private $clientRepository;
    /**
     * @var SelectionFactory
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

    public function inject(\InvoiceRepository $invoiceRepository,
                           \Invoice_itemsRepository $invoiceItemsRepository,
                           SelectionFactory $connection,
                           \UserRepository $userRepository,
                           \ClientRepository $clientRepository)
    {
        $this->connection = $connection;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemsRepository = $invoiceItemsRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function actionEdit($id)
    {
        $this->setView('new');
    }

    public function actionShow($id)
    {
        $invoice = $this->invoiceRepository->fetchById($id);
        $invoiceItem = $this->invoiceItemsRepository->findBy(array('invoice_id' => $id))->fetch();
        $user = $this->userRepository->fetchById($this->user->getId());
        $client = $this->clientRepository->fetchById($invoice->client_id);

        $template = $this->createTemplate()->setFile(__DIR__ . '/../templates/' . THEME_FOLDER . '/PdfTemplates/invoice.latte');
        $template->invoice = $invoice;
        $template->invoiceItem = $invoiceItem;
        $template->user = $user;
        $template->client = $client;

        $pdf = new PdfResponse($template);
        $pdf->setSaveMode(PdfResponse::INLINE);

        $this->sendResponse($pdf);
        $this->terminate();
    }

    public function actionDelete($id)
    {
        $isMineClient = $this->invoiceRepository->isMine($id, $this->user->getId());
        if(!$isMineClient) {
            $this->flashMessage('Táto faktúra Vám nepatrí.', 'error');
            $this->redirect(':Account:invoice:');
        } else {
            $this->invoiceRepository->delete($id);

            $this->flashMessage('Faktúra úspešne odstránená.', 'success');
            $this->redirect(':Account:invoice:');
        }
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
        $grid->addAction('show', 'Ukáž faktúru')->setIcon('list-alt');
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
        $units = array(
            'hour' => 'hodina',
            'piece' => 'kus'
        );

        $form->addGroup('Fakturačné údaje');
        $form->addSelect('client_id', 'Klient', $clients)->setPrompt('Vyberte klienta')->setRequired('Vyberte klienta ktorému chcete faktúru vystaviť.');
        $form->addText('invoice_number', 'Číslo faktúry')->setDefaultValue($nextInvoiceNo);
        $form->addText('variable_sign', 'Variabilný symbol')->setDefaultValue($this->invoiceRepository->getVariableSign($this->user->getId()));
        $form->addText('maturity_date', 'Dátum splatnosti')->setDefaultValue($nextWeek);
        $form->addText('tax_duty_date', 'Daňová povinnosť')->setDefaultValue($today);
        $form->addText('delivery_date', 'Dátum dodania')->setDefaultValue($today);
        $form->addText('date_of_issue', 'Dátum vyhotovenia')->setDefaultValue($today);

        $form->addGroup('Fakturujem Vám');
        $form->addContainer('item');
        $form['item']->addText('text', 'Popis');
        $form['item']->addSelect('unit', 'Jednotka fakturácie', $units);
        $form['item']->addText('unit_count', 'Počet jednotiek');
        $form['item']->addText('unit_price', 'Cena jednotky');
        $form['item']->addText('vat', 'Daň v %');
        $form['item']->addText('discount_percentage', 'Zľava v %');

        $form->onSuccess[] = $this->invoiceFormSubmitted;

        if($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť');
            $invoiceId = $this->getParameter('id');
            $invoiceData = $this->invoiceRepository->fetchById($invoiceId)->toArray();
            $item = $this->invoiceItemsRepository->findBy(array('invoice_id' => $invoiceId))->fetch()->toArray();
            $invoiceData['item'] = $item;

            $form->setDefaults($invoiceData);
        } else {
            $form->addSubmit('submit', 'Vložiť');
        }

        return $form;
    }

    public function invoiceFormSubmitted(Form $form)
    {
        $data = $form->getValues();
        $invoiceItem = $data->item;
        unset($data->item);

        if($this->action === 'edit') {
            $invoiceId = $this->getParameter('id');

            $this->invoiceRepository->updateInvoice($invoiceId, $data);
            $this->invoiceItemsRepository->updateItems($invoiceId, $invoiceItem);

            $this->flashMessage('Faktúra úspešne upravená.', 'success');
            $this->redirect(':Account:invoice:');
        } else {
            $data->user_id = $this->user->getId();

            $data->maturity_date = $this->getRightFormat($data->maturity_date);
            $data->tax_duty_date = $this->getRightFormat($data->tax_duty_date);
            $data->delivery_date = $this->getRightFormat($data->delivery_date);
            $data->date_of_issue = $this->getRightFormat($data->date_of_issue);

            $invoiceId = $this->invoiceRepository->insertInvoice($data);
            $this->invoiceItemsRepository->insertItems($invoiceId, $invoiceItem);

            $this->flashMessage('Faktúra úspešne vytvorená.', 'success');
            $this->redirect(':Account:invoice:');
        }
    }

    protected function getRightFormat($date)
    {
        return date('Y-m-d', strtotime($date));
    }
} 