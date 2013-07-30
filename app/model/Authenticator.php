<?php

use Nette\Database\SelectionFactory;
use Nette\Security,
	Nette\Utils\Strings;


/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator
{
    private $salt;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }



	public function __construct($salt, UserRepository $userRepository)
	{
        $this->salt = $salt;
        $this->userRepository = $userRepository;
	}

	/**
	 * Performs an authentication.
	 * @param  array
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;
		$row = $this->userRepository->findBy(array('email' => $email))->fetch();

		if (!$row) {
			throw new Security\AuthenticationException('The email is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->generateHash($password, $this->salt)) {
			throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

        if(!$row->active) {
            throw new Security\AuthenticationException('Your email is not verified.', self::NOT_APPROVED);
        }

        $userData = $row->toArray();
        unset($userData['password']);
        unset($userData['hash']);

        return new Security\Identity($row->id, $row->role, $userData);
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function generateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(23));
	}

    /**
     * @param  int $id
     * @param  string $password
     */
    public function setPassword($id, $password)
    {
        $this->userRepository->findBy(array('id' => $id))->update(array(
            'password' => $this->generateHash($password, $this->salt),
        ));
    }

}
