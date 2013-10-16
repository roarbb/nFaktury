<?php
/**
 * User: Matej
 * Date: 12.10.2013
 * Time: 16:35
 */

namespace AccountModule;

use Grido\Components\Filters\Filter;
use Grido\Grid;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Database\SelectionFactory;

class TaskPresenter extends BasePresenter
{
    /**
     * @var SelectionFactory
     */
    private $selectionFactory;

    /**
     * @var \TaskRepository
     */
    private $taskRepository;

    /**
     * @var \Task_statusRepository
     */
    private $taskStatusRepository;

    /**
     * @var \Nette\Database\Table\Selection Vrati zoznam vsetkych moznych statusov pre Tasky
     */
    private $statuses;

    /**
     * @var \ProjectRepository
     */
    private $projectRepository;

    /**
     * @var \Nette\Database\Table\Selection Vrati zoznam vsetkych moznych projektov pre Tasky uzivatela
     */
    private $projects;

    public function inject(\Task_statusRepository $taskStatusRepository)
    {
        $this->taskStatusRepository = $taskStatusRepository;
    }

    public function injectNew(\TaskRepository $taskRepository, \ProjectRepository $projectRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function injectEdit(\TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function injectDefault(SelectionFactory $selectionFactory)
    {
        $this->selectionFactory = $selectionFactory;
    }

    public function startup()
    {
        parent::startup();
        $this->statuses = $this->taskStatusRepository->findAllActive()->fetchPairs('id', 'name');
        $this->projects = $this->projectRepository->getProjectsForUser($this->user->getId());
    }

    public function actionEdit($id)
    {
        $isMineClient = $this->taskRepository->isMineTask($id, $this->user->getId());
        if (!$isMineClient) {
            $this->flashMessage('Tento task Vám nepatrí.', 'error');
            $this->redirect(':Account:task:');
        }

        $this->setView('new');
    }

    public function renderDelete($id) {
        $result = $this->taskRepository->deleteTask($id, $this->user->getId());
        if($result) {
            $this->presenter->flashMessage('Task vymazaný', 'success');
        } else {
            $this->presenter->flashMessage('Task nebol vymazaný', 'danger');
        }
        $this->presenter->redirect(':account:task:');
    }

    protected function createComponentGrid($name)
    {
        $table = 'task';
        $grid = new Grid($this, $name);

        $grid->setModel($this->selectionFactory->table($table)->where('user_id', $this->user->getId()));

//        $grid->getRowPrototype()

        $grid->addColumn('id', '#');
        $grid->addFilter('id', 'id');

        $grid->addColumn('name', 'Task');
        $grid->addFilter('name', 'name');

//        $descriptionClassName = 'grid-description';
        $grid->addColumn('description', 'Popis')->setTruncate(100);
//        $grid->getColumn('description')->getCellPrototype()->addAttributes(array('class' => $descriptionClassName));
//        $grid->getColumn('description')->getHeaderPrototype()->addAttributes(array('class' => $descriptionClassName));

        $grid->addFilter('description', 'description');
//        $grid->getFilter('description')->getWrapperPrototype()->addAttributes(array('class' => $descriptionClassName));

        $grid->addColumn('status_id', 'Status')->setReplacement($this->statuses);
        $grid->addFilter('status_id', 'status_id', Filter::TYPE_SELECT, $this->statuses);

        $grid->addColumn('project_id', 'Projekt')->setReplacement($this->projects);
        $grid->addFilter('project_id', 'project_id', Filter::TYPE_SELECT, $this->projects);

        $grid->addAction('edit', 'Upraviť')->setIcon('pencil');
        $grid->addAction('delete', 'Vymazať')
            ->setIcon('trash')
            ->setConfirm('Naozaj chcete vymazať tento projekt?');

        $grid->setExporting($table);
    }

    /**
     * Uprava / vkladanie tasku
     *
     * @return Form
     */
    protected function createComponentInsertEditTaskForm()
    {
        $statuses = $this->statuses;
        $projects = $this->projects;

        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $form->addText('name', 'Názov tasku')->setAttribute('class', 'input-xlarge');
        $form->addSelect('project_id', 'Projekt', $projects);
        $form->addSelect('status_id', 'Status', $statuses);
        $form->addTextArea('description', 'Popis')->setAttribute('class', 'editor input-block-level');

        $form->onSuccess[] = $this->taskFormSubmitted;

        if ($this->action === 'edit') {
            $form->addSubmit('submit', 'Uložiť')->setAttribute('class', 'btn btn-primary');
            $taskId = $this->getParameter('id');
            $taskData = $this->taskRepository->fetchById($taskId);
            $form->setDefaults($taskData);
        } else {
            $form->addSubmit('submit', 'Vložiť')->setAttribute('class', 'btn btn-primary');
        }

        return $form;
    }

    public function taskFormSubmitted(Form $form)
    {
        $data = $form->getValues();

        if ($this->action === 'edit') {
            $taskId = $this->getParameter('id');
            $this->taskRepository->updateTask($taskId, $data);

            $this->flashMessage('Task úspešne upravený.', 'success');
            $this->redirect(':Account:task:');
        } else {
            $data->user_id = $this->user->getId();
            $data->created = new \DateTime();
            $this->taskRepository->insertTask($data);

            $this->flashMessage('Task úspešne vytvorený.', 'success');
            $this->redirect(':Account:task:');
        }
    }
} 