<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 10/22/2018
 * Time: 4:23 PM
 */

class Business extends Persistence
{

	/**
	 * @type Business
	 */
	private static $Business;

	/**
	 * @type int
	 */
	protected $BusinessID;

	/**
	 * @type Week
	 */
	protected $BusinessHours;

	/**
	 * @type Week
	 */
	protected $ShiftHours;

	private function __construct(?array $assoc = []) {
		parent::__construct($assoc);
		$this->ShiftHours = Week::getByID($assoc["ShiftHoursID"]);
		$this->BusinessHours = Week::getByID($assoc["BusinessHoursID"]);
	}

	/**
	 * @return Week
	 */
	public function getShiftHours():Week{
		return $this->ShiftHours;
	}

	/**
	 * @return Week
	 */
	public function getBusinessHours(): Week
	{
		return $this->BusinessHours;
	}

	/**
	 * @param Week $BusinessHours
	 */
	public function setBusinessHours(Week $BusinessHours): Business {
		$this->BusinessHours = $BusinessHours;
		return $this;
	}

	public static function getBusiness(): Business {
		if (!self::$Business) {
			$row = DB::query("
				SELECT *
				FROM Business
			")->fetch_assoc();
			self::$Business = new Business($row);
		}
		return self::$Business;
	}
}