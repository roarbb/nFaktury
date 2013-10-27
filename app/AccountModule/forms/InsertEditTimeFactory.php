<?php
/**
 * Projet: faktury
 * Author: Matej Sajgal
 * Date: 27.10.2013
 */

use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;

class InsertEditTimeFactory extends \Nette\Object
{
    public function createForm($userId)
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

        if ($this->action === 'edit') {

            $form->addSubmit('submit', 'Uložiť')
                ->onClick[] = $this->process;

//            $projectId = $this->getParameter('id');
            $projectData = $this->projectRepository->fetchById($projectId);
            $form->setDefaults($projectData);
        } else {
            $form->addSubmit('submit', 'Vložiť')
                ->onClick[] = $this->process;
        }

        return $form;
    }

    public function process($button)
    {

    }
}