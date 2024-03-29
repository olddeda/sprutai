<?php
namespace common\modules\telegram\commands\telegram;

abstract class AdminCommand extends Command
{
	/**
	 * @var bool
	 */
	protected $private_only = true;
	
	public function preExecute() {
		if (!$this->isAdmin()) {
			die;
		}
		
		return parent::preExecute(); // TODO: Change the autogenerated stub
	}
	
}