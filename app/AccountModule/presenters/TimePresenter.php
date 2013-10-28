<?php
/**
 * Projet: faktury
 * Author: Matej Sajgal
 * Date: 27.10.2013
 */

namespace AccountModule;


use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;

class TimePresenter extends BasePresenter
{
    /**
     * @var \TimesheetRepository
     */
    private $timesheetRepository;

    /**
     * @var \ProjectRepository
     */
    private $projectRepository;
    private $year;
    private $month;
    private $projects;

    public function injectDefault(
        \TimesheetRepository $timesheetRepository,
        \ProjectRepository $projectRepository
    )
    {
        $this->timesheetRepository = $timesheetRepository;
        $this->projectRepository = $projectRepository;
    }

    public function startup()
    {
        parent::startup();
        $this->month = date('m');
        $this->year = date('Y');
        $this->projects = $this->projectRepository->getProjectsForUser($this->user->getId());
    }

    protected function createComponentInsertEditTimeForm()
    {
        $projects = $this->projects;

        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $form->addSelect('project_id', 'Projekt', $projects)
            ->setPrompt('- vyber -')
            ->setRequired('Zadajte projekt prosím');
        $form->addTextArea('description', 'Popis');
        $form->addText('hours', 'Trvanie (hodiny)');
        $form->addText('minutes', 'Trvanie (minuty)');

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

    public function projectFormSubmitted() {

    }
}