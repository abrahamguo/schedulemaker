<?php
/**
 * Created by PhpStorm.
 * User: amoghnagalla
 * Date: 10/27/18
 * Time: 1:47 PM
 */

class ScheduleCreator {

	public static function makeAutomatic (bool $thisWeek = false) {
		$weekStartDate = new DateTime("Monday " . ($thisWeek ? "this" : "next") . " week");
		$employees = Employee::getAll();
		$unassignedShiftsContainer = Week::getUnassignedShiftsContainer($weekStartDate);
		self::make(
			$weekStartDate,
			$employees,
			Business::getBusiness()->getShiftHours(),
			$unassignedShiftsContainer
		);
		foreach ($employees as $employee) $employee->sync();
		if ($unassignedShiftsContainer->getTimeBlocks()) $unassignedShiftsContainer->sync();
	}

	public static function make (
		DateTime $weekStartDate,
		array $employees,
		Week $shiftHours,
		Week $unassignedShiftsContainer
	): bool {

		// 3. Get all shifts
		foreach ($shiftHours->getTimeBlocks() as $shift) {

			// Prioritize all employees first by increasing order of currently scheduled time, then by increasing availability
			usort($employees, function (Employee $e1, Employee $e2) use ($weekStartDate): int {
				if ($result =
					$e1->getScheduleFor($weekStartDate)->sumTimeBlocks() -
					$e2->getScheduleFor($weekStartDate)->sumTimeBlocks()
				) return $result;
				return
					$e1->getWeekAvailability($weekStartDate)->sumTimeBlocks() -
					$e2->getWeekAvailability($weekStartDate)->sumTimeBlocks();
			});
			Util::log(
				"Sorted employees: [" .
				implode(", ", array_map(function (Employee $e): ?string { return $e->getFirstName(); }, $employees)) . "]"
			);

			$assigned = false;
			$clonedShift = clone $shift;
			Util::log("Attempting to assign $shift.");
			foreach($employees as $employee) {
				$name = "{$employee->getFirstName()} {$employee->getLastName()}";
				if ($employee->getWeekAvailability($weekStartDate)->fitsInside($shift)
					&& $employee->getScheduleFor($weekStartDate)->fitsOutside($shift)) {
					Util::log("Assigning $shift to $name");
					$assigned = true;
					$employee->getScheduleFor($weekStartDate)->addTimeBlock($clonedShift);
					break;
				}
				else
					Util::log("<span class='d-inline-block' style='width:50px'></span>$name can't take $shift");
			}

			if (!$assigned) {
				Util::log("$shift not assigned.");
				$unassignedShiftsContainer->addTimeBlock($clonedShift);
			}

		}

		return true;

	}

}