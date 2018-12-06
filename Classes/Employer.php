<?php
/**
 * Created by PhpStorm.
 * User: amoghnagalla
 * Date: 10/14/18
 * Time: 3:12 PM
 */

class Employer extends Staff {

	public function __construct(array $assoc)
	{
		parent::__construct($assoc);
		$this->StaffType = StaffType::EMPLOYER;
	}

}