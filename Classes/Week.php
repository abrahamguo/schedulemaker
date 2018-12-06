<?php
/**
 * Created by PhpStorm.
 * User: amoghnagalla
 * Date: 10/22/18
 * Time: 4:24 PM
 */

class Week extends Persistence {



	/**
	 * @type int
	 */
	protected $WeekID;

	/**
	 * @type DateTime
	 */
	protected $StartDate;

	/**
	 * @type DateTime
	 */
	protected $EndDate;

	/**
	 * @type Employee
	 */
	protected $OverrideAvailabilityEmployee;

	/**
	 * @type Employee
	 */
	protected $AvailabilityEmployee;

	/**
	 * @type Employee
	 */
	protected $ScheduledEmployee;

	protected $ContainsUnassignedShifts = false;

	/**
	 * @type TimeBlock[]
	 */
	protected $TimeBlocks = [];

	protected static $colsNoRecursiveSync = ["OverrideAvailabilityEmployee", "ScheduledEmployee"];

	protected static $colsToIgnore = ["AvailabilityEmployee", "TimeBlocks"];

	public function __construct (?array $assoc = []) {
		parent::__construct($assoc);
		if ($weekID = $this->WeekID) $this->TimeBlocks = TimeBlock::getWhere(["WeekID" => $weekID]);
		if ($seID = $assoc["ScheduledEmployeeID"]) $this->ScheduledEmployee = Staff::getByID($seID);
		if ($oaeID = $assoc["OverrideAvailabilityEmployeeID"])
			$this->OverrideAvailabilityEmployee = Staff::getByID($oaeID);
	}

	public static function getUnassignedShiftsContainer(DateTime $monday) {
		return ($week = Week::getWhere([
			"StartDate" => $monday->format("Y-m-d"),
			"ContainsUnassignedShifts" => true
		]))
			? $week[0]
			: new Week([
				"StartDate" => $monday,
				"EndDate" => Week::add6($monday),
				"ContainsUnassignedShifts" => true
			]);
	}

	public function setAvailabilityEmployee (Employee $e): Week {
		$this->AvailabilityEmployee = $e;
		return $this;
	}

	public function getTimeBlocks (): array {
		return $this->TimeBlocks;
	}

	public function getStartDate(): DateTime {
		return $this->StartDate;
	}
	public function getEndDate():DateTime{
		return $this->EndDate;
	}
	public function getScheduledEmployee()
	{
		return $this->ScheduledEmployee;
	}

	public function setStartDate(DateTime $monday): Week {
		$this->StartDate = $monday;
		return $this;
	}

	public function setEndDate(DateTime $sunday): Week {
		$this->EndDate = $sunday;
		return $this;
	}

	public function setScheduledEmployee (Employee $employee): Week {
		$this->ScheduledEmployee = $employee;
		return $this;
	}

	protected static $colsWithIds = ["OverrideAvailabilityEmployee", "ScheduledEmployee"];

	protected function getDBDataArr (): array {
		return array_replace(
			parent::getDBDataArr(),
			[
				"StartDate" => $this->StartDate ? $this->StartDate->format("Y-m-d") : null,
				"EndDate" => $this->EndDate ? $this->EndDate->format("Y-m-d") : null
			]
		);
	}

	public function sumTimeBlocks()
	{
			$total = 0;
	   foreach ($this->TimeBlocks as $block)
	   {
		   $difference = $block->getEndTime()->diff($block->getStartTime());
		   $total += ($difference->format("%i") / 60)+ $difference->format("%h");
	   }
	   return $total;
	}

	public function addTimeBlock (TimeBlock $timeBlock): bool {
		if (!$timeBlock->getDayOfWeek()) return false;
		$this->TimeBlocks[] = $timeBlock;
		$timeBlock->setWeek($this);
		return true;
	}

	public function removeTimeBlock (TimeBlock $timeBlock): void {
		unset($this->TimeBlocks[array_search($timeBlock, $this->TimeBlocks)]);
	}

	public function sync (): void {
		parent::sync();
		foreach ($this->TimeBlocks as $timeBlock) {
			$timeBlock->setWeek($this)->sync();
		}
	}

	/*
	 * Checks if given time block fits in a time block this week
	 */
	public function fitsInside(TimeBlock $potential) : bool {
		foreach ($this->TimeBlocks as $block)
			if (
				$potential->getTime(true) >= $block->getTime(true) &&
				$potential->getTime(false) <= $block->getTime(false)
			)
				return true;
		return false;
	}

	/*
	 * Checks if given time block is completely outside of other time blocks this week
	 */
	public function fitsOutside(TimeBlock $potential) : bool {
		// for any block...
		foreach ($this->TimeBlocks as $block) {
			if (
				$potential->getTime(false) > $block->getTime(true) &&
				$potential->getTime(true) < $block->getTime(false)
			)
				return false;
		}
		return true;
	}

	public function __toString (): string {
		return
			"Week " . ($this->WeekID ? "(ID $this->WeekID) " : "") . " - " .
			($this->StartDate
				? "{$this->StartDate->format("m/d/Y")} to {$this->EndDate->format("m/d/Y")}"
				: "no date range"
			) . " - containing " . count($this->TimeBlocks) . " time block(s)";
	}

	public function getEmployee (): ?Employee {
		foreach ([$this->OverrideAvailabilityEmployee, $this->AvailabilityEmployee, $this->ScheduledEmployee] as $e)
			if ($e) return $e;
		return null;
	}

	public static function add6 (DateTIme $tb): DateTime {
		return (clone $tb)->add(new DateInterval("P6D"));
	}

}