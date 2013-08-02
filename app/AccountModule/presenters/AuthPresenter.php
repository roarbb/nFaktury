<?php

namespace AccountModule;

use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class AuthPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink;


    /**
     * Login form factory
     * @return Form
     */
    protected function createComponentLoginForm()
    {
        $form = new Form;
        $form->addText('email', 'Email:')
            ->addRule(Form::FILLED, 'Enter email');
        $form->addPassword('password', 'Password:')
            ->addRule(Form::FILLED, 'Enter password');
        $form->addSubmit('send', 'Prihlásiť');

        $form->onSuccess[] = $this->processLogin;
        return $form;
    }


    /**
     * Process login form and login user
     * @param Form $form
     */
    public function processLogin(Form $form)
    {
        $values = $form->getValues(TRUE);
        try {
            $this->user->login($values['email'], $values['password']);
            $this->restoreRequest($this->backlink);
            $this->redirect('Default:default');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}