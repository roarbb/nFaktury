<?php
/**
 * User: roarbb
 * Date: 24.7.2013
 * Time: 21:22
 */

namespace AccountModule;


use Grido\Grid;
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
    private $selectionFactory;
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
                           \Nette\Database\Context $selectionFactory,
                           \UserRepository $userRepository,
                           \ClientRepository $clientRepository)
    {
        $this->selectionFactory = $selectionFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemsRepository = $invoiceItemsRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function actionEdit($id)
    {
        $this->setView('new');
        $invoiceData = $this->invoiceItemsRepository->findBy(array('invoice_id' => $id))->fetch()->toArray();
        $this->template->invoiceData = $invoiceData;
    }

    public function actionShow($id)
    {
        $isMineClient = $this->invoiceRepository->isMine($id, $this->user->getId());
        if (!$isMineClient) {
            $this->flashMessage('Táto faktúra Vám nepatrí.', 'error');
            $this->redirect(':Account:invoice:');
        }

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
        if (!$isMineClient) {
            $this->flashMessage('Táto faktúra Vám nepatrí.', 'error');
            $this->redirect(':Account:invoice:');
        } else {
            $this->invoiceRepository->delete($id);

            $this->flashMessage('Faktúra úspešne odstránená.', 'success');
            $this->redirect(':Account:invoice:');
        }
    }

    public function invoiceFormSubmitted(Form $form)
    {
        $data = $form->getValues();
        $invoiceItem = $data->item;
        unset($data->item);

        if ($this->action === 'edit') {
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

    protected function createComponentGrid($name)
    {

        $table = 'invoice';
        $grid = new Grid($this, $name);
        $grid->setModel($this->selectionFactory->table($table)->where('user_id', $this->user->getId()));

        $grid->addColumnText('invoice_number', 'Číslo faktúry');
        $grid->addFilterText('invoice_number', 'invoice_number');

//        $grid->addColumnText('client_id', 'ID Klienta');
//        $grid->addFilterText('client_id', 'client_id');

        $clients = $this->getClients();
        $grid->addColumnText('client_id', 'Klient')->setReplacement($clients);
        $grid->addFilterSelect('client_id', 'Klient', $clients);

        $grid->addColumnText('variable_sign', 'Variabilný symbol');
        $grid->addFilterText('variable_sign', 'variable_sign');

        $grid->addColumnText('date_of_issue', 'Dátum vyhotovenia');
        $grid->addFilterText('date_of_issue', 'date_of_issue');

        $grid->addColumnText('maturity_date', 'Dátum splatnosti');
        $grid->addFilterText('maturity_date', 'maturity_date');

        $grid->addActionHref('edit', 'Upraviť')->setIcon('pencil')->getElementPrototype()->addAttributes(array('class' => 'no-ajax'));
        $grid->addActionHref('show', 'PDF')->setIcon('list-alt');
        $grid->addActionHref('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať túto faktúru?');

        $grid->setExport($table);
    }

    private function getClients()
    {
        return $this->clientRepository->getClientsForUser($this->user->getId());
    }

    protected function createComponentInsertEditInvoiceForm()
    {
        $form = new Form();
        // $form->setRenderer(new BootstrapRenderer());

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
        $form['item']->addTextarea('text', 'Popis');
        $form['item']->addSelect('unit', 'Jednotka fakturácie', $units);
        $form['item']->addText('unit_count', 'Počet jednotiek')->setAttribute('ng-model', 'unitCount');
        $form['item']->addText('unit_price', 'Cena jednotky')->setAttribute('ng-model', 'unitPrice');
        $form['item']->addText('vat', 'Daň v %');
        $form['item']->addText('discount_percentage', 'Zľava v %');

        $form->onSuccess[] = $this->invoiceFormSubmitted;

        if ($this->action === 'edit') {
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
} 