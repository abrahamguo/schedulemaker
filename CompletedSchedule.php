<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11/4/18
 * Time: 1:32 PM
 */
	const PAGE_TYPE = "index";
	require "Head.php";

	$isNextWeek = $_GET["Next"];
	$monday = new DateTime("Monday " . ($isNextWeek ? "next" : "this") . " week");

	echo "
		<div class='mb-5'>
			<h1>" . ($isNextWeek ? "Next Week's" : "Current") . " Schedule</h1>
			<p>You are viewing the schedule for the week beginning <b>{$monday->format("F j")}</b>.</p>
			<a href='?" . ($isNextWeek ? "" : "Next=1") . "' class='btn btn-primary'>
				" . ($isNextWeek ? "Current" : "Next Week's") . " Schedule
			</a>
		</div><!-- /.text-right -->
	";

	if ($_GET["action"] === "editEmployeesSchedule") {
		$shifts = $_GET["shiftID"];
		$employees = $_GET["employeeID"];

		for($i = 0; $i < count($shifts); $i++){
			$timeBlock = TimeBlock::getByID($shifts[$i]);
			$week = $timeBlock->getWeek();
			$startDate = $week->getStartDate();
			$week->removeTimeBlock($timeBlock);

			$employeeID = $employees[$i];
			$week = $employeeID
				? Employee::getByID($employeeID)->getScheduleFor($startDate)
				: Week::getUnassignedShiftsContainer($startDate);
			$week->addTimeBlock($timeBlock);

			$week->sync();
		}
		Util::redir("CompletedSchedule.php");
	}

	(new ScheduleView(array_merge(
		array_map(
			function (Employee $e): Week {
				global $monday;
				return $e->getScheduleFor($monday);
			},
			Employee::getAll()
		),
		[ Week::getUnassignedShiftsContainer($monday) ]
	)))
		->echo(Session::getLoggedInUser()->getStaffType() == StaffType::EMPLOYER);
?>


<?php require "Foot.php"; ?>