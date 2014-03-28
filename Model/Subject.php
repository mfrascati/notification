<?php
App::uses('NotificationAppModel', 'Notification.Model');


/*

Impostazione e salvataggio del Subject:

$ret = $this->Notification->Subject
	->init()
	->setShared(false)
	->setType(5)
	->setText('Prova')
	->save();


Tipologie notifiche:
	1 - Aggiornamento importante
	2 - News generica
	3 - Offerta commerciale
	4 - 
	5 - Notifica gestionale


*/

class Subject extends NotificationAppModel {

	public $recursive = 1;

	public $hasMany = array(
		'Notification' => array(
			'className'  => 'Notification.Notification',
			'foreignKey' => 'subject_id',
			'dependent'  => true,
		)
	);

	public $validate = array(
		'type'		=> array('numeric' => array( 'rule' => array('numeric'), ), ),
		'text'		=> array('notempty' => array( 'rule' => array('notempty'), ), ),
	);

	public function init()
	{
		$this->create();
		$this->set('shared', false);
		return $this;
	}

	public function setType($value)
	{
		$this->set('type', $value);
		return $this;
	}

	public function setShared($value)
	{
		$this->set('shared', $value);
		return $this;
	}

	public function setText($value)
	{
		$this->set('text', $value);
		return $this;
	}

	public function afterSave($created, $options = array()) 
	{
		if($created == true) {
			$this->Notification->setSubjectId($this->id);
		}
	}


}
