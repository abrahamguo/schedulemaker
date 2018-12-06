<?php
	const PAGE_TYPE = "Employee";
	const NO_BACK = true;

	require "Head.php";
	$staff = Session::getLoggedInUser();

	if ($_GET["action"] == "edit")
		(new Employee($_GET))->sync();


	echo "
		<h1>Employee</h1>
		<a class='btn btn-primary mb-3' href='PersonalSchedule.php'>View My Schedule</a>
		<a class='btn btn-primary mb-3' href='AvailabilityForm.php'>Submit Availability</a>
		<a class='btn btn-primary mb-3' href='TemporaryAvailabilityForm.php'>Submit Temporary Availability</a>
		<a class='btn btn-primary mb-3' href='CompletedSchedule.php'>View Completed Schedule</a>
	";

	(new Modal)
		->title("Edit Information")
		->contents("
			<div class='form-group'>
				<label for='FirstName'>First Name</label>
				<input name='FirstName' type='text' class='form-control' id='FirstName' value='{$staff->getFirstName()}'>
			</div>
			<div class='form-group'>
				<label for='LastName'>Last Name</label>
				<input name='LastName' type='text' class='form-control' id='LastName' value='{$staff->getLastName()}'>
			</div>
				 <div class='form-group'>
				<label for='Password'>Password</label>
				<input name='Password' type='password' class='form-control' id='Password' value='{$staff->getPassword()}'>
			</div>
			<input type='hidden' name='action' value='edit'>
			<input type='hidden' name=StaffID value='{$staff->getStaffID()}'>
		")
		->buttonClass("primary")
		->buttonOtherClass("mb-3")
		->buttonText("Edit Information")
		->echo();

 require "Foot.php";