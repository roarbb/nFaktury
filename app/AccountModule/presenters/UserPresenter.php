<?php

namespace AccountModule;

use Exception;
use Nette\Application\UI\Form;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;

class UserPresenter extends BasePresenter
{
    /**
     * @var \UserRepository
     */
    private $userRepository;

    public function inject(\UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function renderDefault()
    {
    }


    protected function createComponentUserEditForm()
    {
        $userInfo = $this->userRepository->fetchById($this->user->getId());

        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());
        $form->addText('fa_supplier_name', 'Dodávateľ');
        $form->addText('fa_supplier_address', 'Adresa');
        $form->addText('fa_supplier_zip', 'PSČ');
        $form->addText('fa_supplier_city', 'Mesto');
        $form->addText('fa_supplier_tel', 'Telefón');
        $form->addText('fa_bank_account_no', 'Číslo účtu');
        $form->addText('fa_suplier_ico', 'IČO');
        $form->addText('fa_dic', 'DIČ');
        $form->addText('fa_ic_dph', 'IČ DPH');

        $form->addSubmit('create', 'Uložiť');

        $form->setDefaults($userInfo);


        $form->onSuccess[] = $this->taskUserEditFormSubmitted;

        return $form;
    }


    /**
     * @param Form $form
     * @return Form if error
     */
    public function taskUserEditFormSubmitted(Form $form)
    {
        $values = $form->getValues(true);

        $this->userRepository->update($this->user->getId(), $this->unsetEmpty($values));
        $this->flashMessage('Údaje úspešne uložené.', 'success');
        $this->redirect(':Account:default:');
    }

    /**
     * Odstrani prazdne stringy z pola a nahradi ich NULLom
     *
     * @param array $values
     * @return array
     */
    public function unsetEmpty(array $values)
    {
        $out = array();

        foreach ($values as $key => $val) {
            if($val === '') {
                $out[$key] = NULL;
            } else {
                $out[$key] = $val;
            }
        }

        return $out;
    }
}