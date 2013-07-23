<?php
/**
 * User: roarbb
 * Date: 23.7.2013
 * Time: 21:26
 */

namespace AccountModule;


use Grido\Components\Filters\Filter;
use Grido\Grid;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

class ClientPresenter extends BasePresenter
{
    /**
     * @var \ClientRepository
     */
    private $clientRepository;

    /**
     * @var Connection
     */
    private $connection;

    public function inject(Connection $connection, \ClientRepository $clientRepository){
        $this->connection = $connection;
        $this->clientRepository = $clientRepository;
    }

    public function actionEdit($id) {
        $isMineClient = $this->clientRepository->isMineClient($id, $this->user->getId());
        if(!$isMineClient) {
            $this->flashMessage('Tento klient Vám nepatrí.', 'error');
            $this->redirect(':Account:client:');
        }
    }

    public function actionDelete($id)
    {
        $isMineClient = $this->clientRepository->isMineClient($id, $this->user->getId());
        if(!$isMineClient) {
            $this->flashMessage('Tento klient Vám nepatrí.', 'error');
            $this->redirect(':Account:client:');
        } else {
            $this->clientRepository->deleteClient($id);

            $this->flashMessage('Klient úspešne odstránený.', 'success');
            $this->redirect(':Account:client:');
        }
    }

    protected function createComponentGrid($name) {

        $table = 'client';
        $grid = new Grid($this, $name);
        $grid->setModel($this->connection->table($table)->where('user_id', $this->user->getId()));

        $grid->addColumn('id', '#');
        $grid->addFilter('id', 'id');

        $grid->addColumn('name', 'Klient');
        $grid->addFilter('name', 'name');

        $grid->addColumn('street', 'Adresa');
        $grid->addFilter('street', 'street');

        $grid->addColumn('zip', 'PSČ');
        $grid->addFilter('zip', 'zip');

        $grid->addColumn('city', 'Mesto');
        $grid->addFilter('city', 'city');

        $grid->addColumn('tel', 'Telefón');
        $grid->addFilter('tel', 'tel');

        $grid->addColumn('ico', 'IČO');
        $grid->addFilter('ico', 'ico');

        $grid->addAction('edit', 'Upraviť')->setIcon('pencil');
        $grid->addAction('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať tohto klienta?');

        $grid->setExporting($table);
    }

    /**
     * Uprava / vkladanie klienta
     *
     * @return Form
     */
    protected function createComponentInsertEditClientForm()
    {

        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $form->addText('name', 'Meno klienta');
        $form->addText('street', 'Ulica');
        $form->addText('zip', 'PSČ');
        $form->addText('city', 'Mesto');
        $form->addText('tel', 'Telefón');
        $form->addText('ico', 'IČO');
        $form->addText('dic', 'DIČ');
        $form->addText('ic_dph', 'IČ DPH');

        $form->onSuccess[] = $this->clientFormSubmitted;

        if($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť');
            $clientId = $this->getParameter('id');
            $clientData = $this->clientRepository->fetchById($clientId);
            $form->setDefaults($clientData);
        } else {
            $form->addSubmit('submit', 'Vložiť');
        }

        return $form;
    }

    public function clientFormSubmitted(Form $form)
    {
        $data = $form->getValues();

        if($this->action === 'edit') {
            $clientId = $this->getParameter('id');
            $this->clientRepository->updateClient($clientId, $data);

            $this->flashMessage('Klient úspešne upravený.', 'success');
            $this->redirect(':Account:client:');
        } else {
            $data->user_id = $this->user->getId();
            $data->created = new \DateTime();
            $this->clientRepository->insertClient($data);

            $this->flashMessage('Klient úspešne vytvorený.', 'success');
            $this->redirect(':Account:client:');
        }
    }
} 