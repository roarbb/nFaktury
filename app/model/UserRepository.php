<?php

use Nette\Latte\Engine;
use Nette\Mail\Message;
use Nette\Templating\FileTemplate;
use Nette\Utils\Strings;

class UserRepository extends Repository
{
    public function getHash() {
        $hash = Strings::random(20);
        $count = $this->findBy(array('hash' => $hash))->count();

        if($count > 0) {
            return $this->getHash();
        } else {
            return $hash;
        }
    }

    public function insertNew($data) {
        try {
            $this->getTable()->insert($data);
        } catch (PDOException $e) {
            throw new Exception('Email alebo nick uz v databaze existuje.');
        }
    }

    public function activate($hash) {
        $this->findBy(array('hash' => $hash))->update(array('active' => 1));
    }

    public function sendActivationEmail($email, $activationLink)
    {
        $template = new FileTemplate(__DIR__ . '/../FrontModule/templates/' . THEME_FOLDER . '/EmailTemplates/activate.latte');
        $template->registerFilter(new Engine);
        $template->link = $activationLink;

        $this->sendMail($email, $template);
    }

    public function sendForgetPassEmail($email, $repassLink)
    {
        $template = new FileTemplate(__DIR__ . '/../FrontModule/templates/' . THEME_FOLDER . '/EmailTemplates/forgetPassLinkSend.latte');
        $template->registerFilter(new \Nette\Latte\Engine);
        $template->link = $repassLink;

        $this->sendMail($email, $template);
    }

    protected function sendMail($email, FileTemplate $template)
    {
        $mail = new Message;
        $mail->setFrom('Altamira <neodpovedaj@altamira.sk>')
            ->addTo($email)
            ->setHTMLBody($template)
            ->send();
    }

    public function update($userId, $data)
    {
        $this->getTable()->where('id', $userId)->update($data);
    }
}