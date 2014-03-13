<?php
/**
 * User: roarbb
 * Date: 23.7.2013
 * Time: 21:26
 */

namespace AccountModule;


use Grido\Grid;
use Nette\Application\UI\Form;
use Nette\Database\SelectionFactory;

class ClientPresenter extends BasePresenter
{
    /**
     * @inject
     * @var \ClientRepository
     */
    public $clientRepository;

    /**
     * @inject
     * @var \Nette\Database\Context
     */
    public $selectionFactory;


    public function actionEdit($id)
    {
        $isMineClient = $this->clientRepository->isMineClient($id, $this->user->getId());
        if (!$isMineClient) {
            $this->flashMessage('Tento klient Vám nepatrí.', 'error');
            $this->redirect(':Account:client:');
        }
    }

    public function actionDelete($id)
    {
        $isMineClient = $this->clientRepository->isMineClient($id, $this->user->getId());
        if (!$isMineClient) {
            $this->flashMessage('Tento klient Vám nepatrí.', 'error');
            $this->redirect(':Account:client:');
        } else {
            $this->clientRepository->deleteClient($id);

            $this->flashMessage('Klient úspešne odstránený.', 'success');
            $this->redirect(':Account:client:');
        }
    }

    public function clientFormSubmitted(Form $form)
    {
        $data = $form->getValues();

        if ($this->action === 'edit') {
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

    protected function createComponentGrid($name)
    {

        $table = 'client';
        $grid = new Grid($this, $name);

        $grid->setModel($this->selectionFactory->table($table)->where('user_id', $this->user->getId()));

        $grid->addColumnText('id', '#');
        $grid->addFilterText('id', 'id');

        $grid->addColumnText('name', 'Klient');
        $grid->addFilterText('name', 'name');

        $grid->addColumnText('street', 'Adresa');
        $grid->addFilterText('street', 'street');

        $grid->addColumnText('zip', 'PSČ');
        $grid->addFilterText('zip', 'zip');

        $grid->addColumnText('city', 'Mesto');
        $grid->addFilterText('city', 'city');

        $grid->addColumnText('tel', 'Telefón');
        $grid->addFilterText('tel', 'tel');

        $grid->addColumnText('ico', 'IČO');
        $grid->addFilterText('ico', 'ico');

        $grid->addActionHref('edit', 'Upraviť')->setIcon('pencil')->getElementPrototype()->addAttributes(array('class' => 'no-ajax'));
        $grid->addActionHref('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať tohto klienta?');

        $grid->setExport($table);
    }

    /**
     * Uprava / vkladanie klienta
     *
     * @return Form
     */
    protected function createComponentInsertEditClientForm()
    {
        $form = new Form();

        $form->addText('name', 'Meno klienta');
        $form->addText('street', 'Ulica');
        $form->addText('zip', 'PSČ');
        $form->addText('city', 'Mesto');
        $form->addText('tel', 'Telefón');
        $form->addText('ico', 'IČO');
        $form->addText('dic', 'DIČ');
        $form->addText('ic_dph', 'IČ DPH');

        $form->onSuccess[] = $this->clientFormSubmitted;

        if ($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť');
            $clientId = $this->getParameter('id');
            $clientData = $this->clientRepository->fetchById($clientId);
            $form->setDefaults($clientData);
        } else {
            $form->addSubmit('submit', 'Vložiť');
        }

        return $form;
    }
} 