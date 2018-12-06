<?php

	const PAGE_TYPE = "Employer";
	require "Head.php";

	$allEmployees = Employee::getAll();

	if ($_GET["deleteID"]) {
		// delete the employee...
			$employee = $allEmployees[$_GET["deleteID"]];
			if ($employee) {
				$employee->remove();
				unset($allEmployees[$_GET["deleteID"]]);
				Util::redir("EmployeeList.php");

			}
	}
	if ($_GET["action"] == "edit"){
		Employee::getByID($_GET["StaffID"])->applyVals($_GET)->sync();
		header("Location: EmployeeList.php");
	}
	if ($_GET["action"]=="create") {
		($_GET["StaffType"] == "Employer" ? new Employer($_GET) : new Employee($_GET))->sync();
		Util::redir("EmployeeList.php");
	}

	echo "
		<div class='row'>
			<div class='col'>
				<h1>All Employees</h1>
			</div><!-- /.col -->
			<div class='col'>
				<div class='form-group d-flex flex-column align-items-end mb-5'>
	";

	(new Modal)
		->buttonClass("primary")
		->buttonText("<span class='fas fa-plus'></span> Add Employee")
		->title("Add Employee")
		->contents("
			<div class='form-group'>
				<label for='FirstName'>First Name</label>
				<input name='FirstName' type='text' class='form-control' id='FirstName' required>
			</div>
			<div class='form-group'>
				<label for='LastName'>Last Name</label>
				<input name='LastName' type='text' class='form-control' id='LastName' required>
			</div>
			<div class='form-group'>
				<label for='UserName'>Username</label>
				<input name='UserName' type='text' class='form-control' id='UserName'>
			</div>
			<input type='hidden' name='action' value='create'>
		")
		->echo();
	echo "
				</div><!-- /.form-group -->
			</div><!-- /.col -->
		</div><!-- /.row -->
		<table class='table'>
			<thead>
				<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Username</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
	";
	foreach (Employee::getAll() as $employee) {

		/*
		 * $dyn = " "; clears out entries from previous employees
		 */
			$dyn = "";
			foreach ($employee->getSchedules() as $schedule)
				$dyn .=  " 
					<tr>
						<td>{$schedule->getStartDate()->format("m/d/Y")}-{$schedule->getEndDate()->format("m/d/Y")}</td>
						<td>{$schedule->sumTimeBlocks()}</td> 
					</tr>
				";


		$pastHourModal = (new Modal)
			->title("View Past Hours")
			->buttonText("View Past Hours")
			->buttonClass("dark")
			->contents(" 
				<table class='table'>
					<thead>
						<tr>
							<th>Week</th>
							<th>Hours</th>
						</tr>
					</thead>
					<tbody>
						$dyn
					</tbody>
				</table>
			");




		echo "
			<tr>
				<td>" . $employee->getFirstName() . "</td>
				<td>" . $employee->getLastName() . "</td>
				<td>" . $employee->getUsername() . "</td>
				<td>";
				(new Modal)
					->title("Availability for {$employee->getFirstName()} {$employee->getLastName()}")
					->buttonText("View Availability")
					->buttonClass("info")
					->large()
					->noSave()
					->contents(
						(new ScheduleView($employee->getWeekAvailability(new DateTime("Monday this week"))))
							->noNames()
							->compact()
							->render()
					)
					->echo();
				(new Modal)
					->title("Edit Employee")
					->buttonText("Edit")
					->buttonClass("warning")
					->contents("
						<div class='form-group'>
							<label for='FirstName'>First Name</label>
							<input name='FirstName' type='text' class='form-control' id='FirstName' value='{$employee->getFirstName()}'>
						</div>
						<div class='form-group'>
							<label for='LastName'>Last Name</label>
							<input name='LastName' type='text' class='form-control' id='LastName' value='{$employee->getLastName()}'>
						</div>

						<input type='hidden' name='action' value='edit'>
						<input type='hidden' name='StaffID' value='{$employee->getStaffID()}'>
					")
					->echo();
					$pastHourModal->echo();
				echo "
							<a href='?deleteID={$employee->getStaffID()}' class='btn btn-danger'>Delete</a>
						</td>
					</tr>
				";
	}
	echo "
			</tbody>
		</table>
	";
	require "Foot.php";