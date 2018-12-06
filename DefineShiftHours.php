<?php

	const PAGE_TYPE = "Employer";

	require "Head.php";

	if ($_POST["action"] == "SaveShiftHours") {
		if (TimeBlockForm::checkNoDeletion()) {
			$week = Business::getBusiness()->getShiftHours();
			foreach ($_POST["TimeBlocks"] as $blockData)
				$week->addTimeBlock(new TimeBlock($blockData));
			Business::getBusiness()->sync();
		}
		Util::redir("DefineShiftHours.php");
	}
	echo "
		<h1>Define Shift Hours</h1>
		<form method='post'>
			<input type='hidden' name='action' value='SaveShiftHours'>
	";
	foreach (Business::getBusiness()->getShiftHours()->getTimeBlocks() as $timeBlock)
		(new TimeBlockForm)
			->pretext("<b>Shift:</b>")
			->timeBlock($timeBlock)
			->label("")
			->echo();
	(new TimeBlockForm)
		->pretext("<b>Shift:</b>")
		->label("")
		->echo();
	echo "
			<button type='submit' class='btn btn-primary'>Submit</button>
		</form>
	";


require "Foot.php";