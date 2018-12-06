<?php
/**
 * Created by PhpStorm.
 * User: amoghnagalla
 * Date: 10/14/18
 * Time: 3:11 PM
 */

class Employee extends Staff {

	/**
	 * @type Week[]
	 */
	protected $Schedules = [];

	public function getSchedules (): array {
		return $this->Schedules;
	}


	/**
	 * @type Week
	 */
	protected $DefaultAvailability;

	/**
	 * @type Week[]
	 */
	protected $OverrideAvailabilities;

	/**
	 * @return Employee[]
	 */
	public static function getAll (): array {

		$staff = [];
		foreach (DB::query("
			SELECT *
			FROM Staff
			WHERE StaffType = '" . StaffType::EMPLOYEE . "'
		")->fetch_all(MYSQLI_ASSOC) as $row) {
			$obj = self::create($row);
			$staff[$obj->getStaffID()] = $obj;
		}
		return $staff;

	}

	public function __construct(array $assoc = []) {
		parent::__construct($assoc);
		$this->StaffType = StaffType::EMPLOYEE;
		$this->Schedules = Week::getWhere(["ScheduledEmployeeID" => $this->StaffID]);

		// Default availabilities
		if (!$this->DefaultAvailability)
			$this->DefaultAvailability = ($defaultAvailabilityID = $assoc["DefaultAvailabilityID"])
				? Week::getByID($defaultAvailabilityID)
				: new Week;
		$this->DefaultAvailability->setAvailabilityEmployee($this);

		// Override availabilities
		$this->OverrideAvailabilities = Week::getWhere(["OverrideAvailabilityEmployeeID" => $this->StaffID]);
		foreach ($this->OverrideAvailabilities as $week)
			$week->setAvailabilityEmployee($this);

	}

	/**
	 * @return Week (The employee's default availability)
	 */
	public function getDefaultAvailability (): ?Week { return $this->DefaultAvailability; }

	public function addOverrideAvailability (Week $week): void{
		$this->OverrideAvailabilities[] = $week;
		//Util::dump($OverrideAvailabilities[0]);
	}

	/**
	 * @param DateTime $monday
	 * @return Week
	 */
	public function getScheduleFor (DateTime $monday): Week {
		//Util::log("Looking for a schedule for the week beginning on {$monday->format("m/d/Y")}.");
		//Util::log("This employee has " . count($this->Schedules) . " existing schedules.");
		foreach($this->Schedules as $week) {
			$isMatch = $week->getStartDate() == $monday;
			//Util::log("Found schedule: $week (" . ($isMatch ? "matched" : "not a match") . ").");
			if ($isMatch) return $week;
		}
		$newWeek = (new Week)
			->setStartDate(clone $monday)
			->setEndDate(Week::add6($monday))
			->setScheduledEmployee($this);
		$this->Schedules[] = $newWeek;
		//Util::log("No match found; creating new $newWeek");
		return $newWeek;
	}

	/**
	 * @param DateTime $monday (The first day of a given week)
	 * @return Week (If the employee has a temporary override for that week, it returns it. If not, it returns the default availability)
	 */
	public function getWeekAvailability (DateTime $monday): ?Week {
		$week = $this->getWeekOverride($monday);
		if ($week){
			return $week;
		}
		return $this->DefaultAvailability;
	}

	/**
	 * @param DateTime $monday (The first day of a given week)
	 * @return Week (If the employee has a temporary override for that week, it returns it. If not, it returns null)
	 */
	public function getWeekOverride (DateTime $monday): ?Week {
		foreach($this->OverrideAvailabilities as $week){
			if ($week->getStartDate() == $monday){
				return $week;
			}
		}
		return null;
	}
}