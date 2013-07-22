<?php

namespace AdminModule;

use DependentSelectBox\JsonDependentSelectBox;
use Nette\Security\IUserStorage;

abstract class BasePresenter extends \BasePresenter
{
    public function startup()
    {
        parent::startup();
        JsonDependentSelectBox::register('addJSelect');

        if ($this->name != 'Admin:Auth') {
            if (!$this->user->isLoggedIn()) {
                if ($this->user->getLogoutReason() === IUserStorage::INACTIVITY ) {
                    $this->flashMessage('Session timeout, you have been logged out');
                }

                $this->redirect('Auth:login', array(
                    'backlink' => $this->storeRequest()
                ));

            } else {
                if (!$this->user->isAllowed($this->name, $this->action)) {
                    $this->flashMessage('Access denied', 'error');
                    $this->redirect('Default:');
                }
            }
        }
    }

    public function beforeRender() {
        parent::beforeRender();
        JsonDependentSelectBox::tryJsonResponse($this /*(presenter)*/);
    }

    /**
     * Logout user
     */
    public function handleLogout()
    {
        $this->user->logOut();
        $this->flashMessage('You were logged off.');
        $this->redirect('this');
    }

}