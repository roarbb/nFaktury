<?php

use Nette\Latte\Engine;
use Nette\Mail\Message;
use Nette\Mail\SmtpException;
use Nette\Mail\SmtpMailer;
use Nette\Templating\FileTemplate;
use Nette\Utils\Strings;

class UserRepository extends Repository
{
    /**
     * @var SmtpMailer
     */
    public $mailer;

    public function inject(SmtpMailer $mailer){
        $this->mailer = $mailer;
    }

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
        $message = new Message();
        $message->setFrom('faktury@sajgal.com')
            ->addTo($email)
            ->setHTMLBody($template);

        try
        {
            $this->mailer->send($message);
        }
        catch (SmtpException $e)
        {
            echo $e->getMessage();
        }
    }

    public function update($userId, $data)
    {
        $this->getTable()->where('id', $userId)->update($data);
    }

    public function getUserByMail($email)
    {
        return $this->findBy(array('email' => $email));
    }

    public function getUserById($userId)
    {
        return $this->fetchById($userId);
    }
}