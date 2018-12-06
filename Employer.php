<?php

	const PAGE_TYPE = "Employer";
	const NO_BACK = true;
	
	require "Head.php";
?>
<h1>Employer</h1>
<div class='form-group'>
	<a class='btn btn-primary mb-3' href='EmployeeList.php' role='button'>List of Employees</a>
	<a class='btn btn-primary mb-3' href='BusinessHours.php' role='button'>Define Business Hours</a>
	<a class='btn btn-primary mb-3' href=' DefineShiftHours.php' role='button'>Define Shift Hours</a>
	<a class='btn btn-primary mb-3' href='CompletedSchedule.php' role='button'>View Completed Schedule</a>
	<a class='btn btn-primary mb-3' href='AllAvailability.php' role='button'>View All Employee's Availability</a>
</div><!-- /.form-group -->

<?php require "Foot.php"; ?>