<?php

App::uses('NotificationAppController', 'Notification.Controller');

class NotificationsController extends NotificationAppController {

	/**
	 * Mostra l'elenco delle notifiche
	 * Se la chiamata è ajax mostra la versione ridotta per la dashboard
	 * @return void
	 */
	public function index($limit = 100)
	{
		$this->set('title_for_layout', 'Notifiche');
		$all = $this->Notification->getLast($this->getLoggedUser(), $limit);


		$this->set(compact('all'));
	}

	/**
	 * Segna una notifica come letta o no
	 * @param  int  $id   
	 * @param  integer $read 
	 * @return bool
	 */
	public function mark($id, $read = 1)
	{
		$this->Notification->read(null, $id);
		if(empty($this->data))
			throw new CakeException("Notifica non trovata");
			
		if($this->data['Notification']['user_id'] != $this->getLoggedUser())
			throw new CakeException("Non puoi modificare lo stato della notifica");

		return $this->saveField('read', (bool)$read);

	}

	private function getLoggedUser()
	{
		$user_id = $this->Session->read('Auth.User.id');
		if(!$user_id)
			throw new CakeException("Nessun utente impostato");

		return $user_id;
	}

	public function testAdd()
	{
		$ret = $this->Notification->Subject
			->init()
			->setShared(false)
			->setType(1)
			->setText('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. %nome %cognome')
			->save();

		$ret = $this->Notification
			->init()
			->setUser(3042)
			->setParams(array('nome' => 'Massi', 'cognome' => 'Frascati'))
			->save();

		$ret = $this->Notification
			->init()
			->setUser(2)
			->setParams(array('nome' => 'Massi', 'cognome' => 'Ciccirillo'))
			->save();
		$this->render(false);
	}

	public function testGetLast($user_id)
	{
		debug($this->Notification->getLast($user_id));
		$this->render(false);
	}
}
