<?php
App::uses('NotificationAppModel', 'Notification.Model');
/**
 * Notification Model
 *
 * @property User $User
 * @property Subject $Subject
 */
class Notification extends NotificationAppModel {

	public $validate = array(
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'read' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	public $order = array('Notification.created DESC');

	public $belongsTo = array(
		'Subject' => array(
			'className' => 'Notification.Subject',
			'foreignKey' => 'subject_id',
		)
	);

	public $subject_id = null;

	public function get($id = null)
	{

	}


	public function init()
	{
		if(!$this->Subject->id)
			throw new CakeException("Subject id non impostato");
			
		$this->create();
		$this->set('subject_id', $this->Subject->id);
		$this->set('shared', false);
		$this->set('read', false);
		return $this;
	}

	public function setSubjectId($id)
	{
		$this->subject_id = $id;
	}

	public function setUser($id)
	{
		$this->set('user_id', $id);
		return $this;
	}

	public function setParams($array)
	{
		$this->set('params', $array);
		return $this;
	}

	public function beforeSave($options = array()) 
	{
		$this->data['Notification']['params'] = json_encode($this->data['Notification']['params']);
	}

	public function afterFind($results, $primary = false)
	{
		if($results)
		{
			foreach($results as &$r)
			{
				$r['Notification']['params'] = json_decode($r['Notification']['params'], true);
				if(is_array($r['Notification']['params']))
				{
					uksort($r['Notification']['params'], "self::cmp");

					$r['Notification']['text'] = $r['Subject']['text'];
					foreach($r['Notification']['params'] as $k => $v)
					{
					    $r['Notification']['text'] = str_replace('%' . $k, $v, $r['Notification']['text']);
					}
					unset($r['Notification']['params']);

					
				}
			}
		}
		return $results;
	}

	public function getUnread($user_id, $limit = false){
		return $this->find('all', array(
			'conditions' => array(
				'Notification.read'		=> false,
				'Notification.user_id'	=> $user_id,
			),
			'limit' => $limit,
		));
	}

	public function getLast($user_id, $limit = 5){
		return $this->find('all', array(
			'conditions' => array(
				'Notification.user_id' => $user_id,
			),
			'limit' => $limit,
			'order' => array('Notification.id' => 'DESC')
		));
	}

	public function markAllAsRead($user_id){
		return $this->updateAll(
			array('Notification.read' => true),
			array('Notification.user_id' => $user_id)
		);
	}

	private function cmp($a, $b)
	{
	    return strlen($b) - strlen($a);
	}

}
