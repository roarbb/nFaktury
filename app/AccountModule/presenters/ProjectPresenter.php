<?php
/**
 * User: roarbb
 * Date: 12.10.2013
 * Time: 11:40
 */

namespace AccountModule;

use Grido\Grid;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Database\SelectionFactory;

class ProjectPresenter extends BasePresenter
{
    /**
     * @var SelectionFactory
     */
    private $selectionFactory;

    /**
     * @var ProjectPresenter
     */
    private $projectRepository;

    public function injectDefault(SelectionFactory $selectionFactory, \ProjectRepository $projectRepository)
    {
        $this->selectionFactory = $selectionFactory;
        $this->projectRepository = $projectRepository;
    }

    protected function createComponentGrid($name)
    {
        $table = 'project';
        $grid = new Grid($this, $name);

        $grid->setModel($this->selectionFactory->table($table)->where('user_id', $this->user->getId()));

        $grid->addColumn('id', '#');
        $grid->addFilter('id', 'id');

        $grid->addColumn('name', 'Projekt');
        $grid->addFilter('name', 'name');

        $grid->addColumn('description', 'Popis');
        $grid->addFilter('description', 'description');

        $grid->addAction('edit', 'Upraviť')->setIcon('pencil');
        $grid->addAction('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať tento projekt?');

        $grid->setExporting($table);
    }

    /**
     * Uprava / vkladanie klienta
     *
     * @return Form
     */
    protected function createComponentInsertEditProjectForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $form->addText('name', 'Meno projektu');
        $form->addText('description', 'Popis');

        $form->onSuccess[] = $this->projectFormSubmitted;

        if ($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť');
            $projectId = $this->getParameter('id');
            $projectData = $this->projectRepository->fetchById($projectId);
            $form->setDefaults($projectData);
        } else {
            $form->addSubmit('submit', 'Vložiť');
        }

        return $form;
    }

    public function projectFormSubmitted(Form $form)
    {
        $data = $form->getValues();

        if ($this->action === 'edit') {
            $projectId = $this->getParameter('id');
            $this->projectRepository->updateClient($projectId, $data);

            $this->flashMessage('Projekt úspešne upravený.', 'success');
            $this->redirect(':Account:project:');
        } else {
            $data->user_id = $this->user->getId();
            $data->created = new \DateTime();
            $this->clientRepository->insertClient($data);

            $this->flashMessage('Klient úspešne vytvorený.', 'success');
            $this->redirect(':Account:client:');
        }
    }
} 