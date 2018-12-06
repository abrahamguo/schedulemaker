<?php

	const PAGE_TYPE = "Employee";

	require "Head.php";

	$loggedInEmployee = Session::getLoggedInUser();
	if ($_POST["action"] == "SaveAvailability") {
		if (TimeBlockForm::checkNoDeletion()) {
			$defaultSchedule = $loggedInEmployee->getDefaultAvailability();
			foreach ($_POST["TimeBlocks"] as $blockData) $defaultSchedule->addTimeBlock(new TimeBlock($blockData));
			$loggedInEmployee->sync();
		}
		Util::redir("AvailabilityForm.php");
	}

?>
<h1>Default Availability Form</h1>
<form method='post'>
	<input type='hidden' name='action' value='SaveAvailability'>
	<div class='form-group'>
		<button class='btn btn-primary'>Save</button>
	</div><!-- /.form-group -->
	<?php
		foreach ($loggedInEmployee->getDefaultAvailability()->getTimeBlocks() as $block)
			(new TimeBlockForm)->timeBlock($block)->echo();
		(new TimeBlockForm)->echo();
	?>
</form>

<?php require "Foot.php"; ?>
