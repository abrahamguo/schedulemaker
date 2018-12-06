<?php

	const PAGE_TYPE = "Employer";
	require "Head.php";

	$monday = new DateTime("Monday next week");
	echo "<h1>Employee Availability: {$monday->format("m/d/Y")}</h1>";
	foreach(Employee::getAll() as $employee) {
		$OverideAvailabilityWeek = $employee->getWeekAvailability($monday);
		$timeBlocks = $OverideAvailabilityWeek->getTimeBlocks();
		echo "<h4>Availability for: {$employee->getFirstName()} {$employee->getLastName()}</h4>";
		if ($timeBlocks) {
			echo "<ul>";
			foreach ($timeBlocks as $tb)
				echo "<li>{$tb->display()}</li>";
			echo "</ul>";
		}
		else
			echo "<p class='text-muted'><i>No availability for this employee for this week</i></p>";
	}

	require "Foot.php";