<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11/4/2018
 * Time: 1:58 PM
 */

	const PAGE_TYPE = "Employee";

	require "Head.php";

	$loggedInEmployee = Session::getLoggedInUser();
	if ($_GET["weekSelection"] == "Week") {
		$selectedWeek = $_GET["TemporaryWeek"];
		$selectedMonday = new DateTime($selectedWeek);
	}


	if ($_POST["action"] == "SaveTemporaryAvailability") {
		if (TimeBlockForm::checkNoDeletion()) {
			$overrideWeek = $loggedInEmployee->getWeekOverride($selectedMonday);
			if (!$overrideWeek) {
				$overrideWeek = new Week([
					"OverrideAvailabilityEmployee" => $loggedInEmployee,
					"StartDate" => $selectedMonday,
					"EndDate" => TimeBlock::add6($selectedMonday)
				]);
			}
			$loggedInEmployee->addOverrideAvailability($overrideWeek);
			if ($_POST["Submit"] == "standard")
				foreach ($_POST["TimeBlocks"] as $blockData) $overrideWeek->addTimeBlock(new TimeBlock($blockData));
			$loggedInEmployee->sync();
		}
		Util::redir("TemporaryAvailabilityForm.php?$_SERVER[QUERY_STRING]");
	}

?>

<h1>Temporary Availability Form</h1>
<form method='get'>
	<input type='hidden' name='weekSelection' value='Week'>
	<p>For the week of <input type="week" name="TemporaryWeek" value="<?php echo $selectedWeek;?>" required> <button class="btn btn-success">Go</button></p>

</form>

<form method="post">
	<input type='hidden' name='action' value='SaveTemporaryAvailability'>
	<?php
	if ($selectedMonday) {
		$week = $loggedInEmployee->getWeekOverride($selectedMonday);
		if ($week)
			foreach ($week->getTimeBlocks() as $block)
				(new TimeBlockForm)->timeBlock($block)->echo();
	}
	(new TimeBlockForm)->echo();
	?>
	<div class='form-group'>
		<button class='btn btn-primary' name='Submit' value='standard'>Save</button>
		<button class='btn btn-danger' name='Submit' value='off'>Request Week Off</button>
	</div><!-- /.form-group -->
</form>

<?php

	require "Foot.php";

?>
