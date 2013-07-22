<?php

namespace FrontModule;

use Exception;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Utils\Strings;

class UserPresenter extends \BasePresenter
{
    /**
     * @var \Authenticator
     */
    private $authenticator;

    /**
     * @var \UserRepository
     */
    private $userRepository;

    /**
     * @var \Nette\Database\Table\ActiveRow
     */
    private $repassUser;

    public function inject(\Authenticator $authenticator, \UserRepository $userRepository)
    {
        $this->authenticator = $authenticator;
        $this->userRepository = $userRepository;
    }

    public function actionActivate($id)
    {
        if(!$id) {
            $this->flashMessage('Aktivovanie užívateľa sa nepodarilo.', 'error');
        } else {
            $this->userRepository->activate($id);
            $this->flashMessage('Vaše konto bolo úspešne aktivované. Teraz sa môžete prihlásiť.', 'success');
        }

        $this->redirect(':front:default:default');
    }

    public function actionRepass($id)
    {
        $this->repassUser = $this->userRepository->findBy(array('hash' => $id))->fetch();
        if ($this->repassUser === FALSE) {
            $this->setView('notFound');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());

        $form->addText('nickname', 'Vaše meno:')->setRequired('Zadajte prosím Vaše meno.');
        $form->addText('email', 'Email:')
            ->addRule(Form::EMAIL, 'Prosím zadajte správny formát emailu.')
            ->setRequired('Zadajte email prosím');
        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zvoľte si heslo')
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mať aspoň %d znakov', 8);
        $form->addPassword('password2', 'Heslo pre kontrolu:')
            ->setRequired('Zadajte prosím heslo ešte raz pre kontrolu')
            ->addRule(Form::EQUAL, 'Heslá se nezhodujú', $form['password']);

        $form->addSubmit('send', 'Registrovať');

        $form->onSuccess[] = $this->registerFormSubmitted;

        return $form;
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function registerFormSubmitted(Form $form)
    {
        $v = $form->getValues();
        unset($v->password2);

        $v->password = $this->authenticator->generateHash($v->password, $this->authenticator->getSalt());
        $v->create_date = new \DateTime();
        $v->hash = $this->userRepository->getHash();
        $v->nickname_webalized = Strings::webalize($v->nickname);
        $v->role = 'user';

        try {
            $this->userRepository->insertNew($v);

            $this->absoluteUrls = true;
            $activationLink = $this->link(':front:user:activate', $v->hash);
            $this->userRepository->sendActivationEmail($v->email, $activationLink);

            $this->flashMessage('Boli ste úspešne zaregistrovaný. Pomocou emailu, ktorý sme Vám zaslali aktivujte svoje konto.', 'success');
            $this->redirect(':front:default:');
        } catch (Exception $e) {
            $form->addError($e->getMessage());
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForgetPassForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer);

        $form->addText('email', 'Email:')
            ->addRule(Form::EMAIL, 'Prosím zadajte správny formát emailu.')
            ->setRequired('Zadajte email prosím');

        $form->addSubmit('send', 'Zaslať nové heslo')
            ->setAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = $this->forgetPassFormSubmitted;
        return $form;
    }

    /**
     * @param Form $form
     */
    public function forgetPassFormSubmitted(Form $form)
    {
        $userEmail = $form->values->email;
        $user = $this->userRepository->findBy(array('email' => $userEmail))->fetch();

        if($user === FALSE) {
            $form->addError('Tento email nie je zaregistrovaný.');
        } else {
            $this->absoluteUrls = true;
            $repassLink = $this->link(':front:user:repass', $user->hash);
            $this->userRepository->sendForgetPassEmail($user->email,$repassLink);

            $this->flashMessage('Na Váš email bol odoslaný link na zmenu hesla.', 'success');
            $this->redirect(':front:default:');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRepassForm()
    {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer);

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zvoľte si heslo')
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mať aspoň %d znakov', 8);
        $form->addPassword('password2', 'Heslo pre kontrolu:')
            ->setRequired('Zadajte prosím heslo ešte raz pre kontrolu')
            ->addRule(Form::EQUAL, 'Heslá se nezhodujú', $form['password']);

        $form->addSubmit('send', 'Zmeniť heslo')
            ->setAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = $this->repassFormSubmitted;
        return $form;
    }

    public function repassFormSubmitted(Form $form)
    {
        $this->authenticator->setPassword($this->repassUser->id, $form->values->password);

        $this->presenter->flashMessage('Heslo úspešne zmenené, terz sa môžete prihlásiť', 'success');
        $this->presenter->redirect(':front:default:');
    }
}