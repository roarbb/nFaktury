<?php

/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository extends Nette\Object
{
	/** @var Nette\Database\Connection */
	protected $connection;



	public function __construct(Nette\Database\Connection $db)
	{
		$this->connection = $db;
	}



	/**
	 * Vrací objekt reprezentující databázovou tabulku.
	 * @return Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		// název tabulky odvodíme z názvu třídy
		preg_match('#(\w+)Repository$#', get_class($this), $m);
		return $this->connection->table(lcfirst($m[1]));
	}



	/**
	 * Vrací všechny řádky z tabulky.
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}

    /**
     * Vrati vsetky aktivne riadky z tabulky.
     * @return \Nette\Database\Table\Selection
     */
    public function findAllActive()
    {
        return $this->findAll()->where('active', 1);
    }

    /**
     * Vrací řádky podle filtru, např. array('name' => 'John').
     * @param array $by
     * @return \Nette\Database\Table\Selection
     */
    public function findBy(array $by)
	{
		return $this->getTable()->where($by);
	}

    /**
     * Vrati riadok z tabulky podla PRIMARY KEY
     *
     * @param $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function fetchById($id) {
        return $this->getTable()->get($id);
    }

}
