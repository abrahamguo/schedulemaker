<?php

	const PAGE_TYPE = "Employer";

	require "Head.php";

	if ($_POST["action"] == "SaveBusinessHours") {
		if (TimeBlockForm::checkNoDeletion()) {
			$week = Business::getBusiness()->getBusinessHours();
			foreach ($_POST["TimeBlocks"] as $blockData)
				$week->addTimeBlock(new TimeBlock($blockData));
			Business::getBusiness()->sync();
		}
		Util::redir("BusinessHours.php");
	}
?>

<h1>Business Hours</h1>
<form method='post'>
	<input type='hidden' name='action' value='SaveBusinessHours'>
	<div class='form-group'>
		<button class='btn btn-primary'>Save</button>
	</div>
	<?php
		$businessHours = Business::getBusiness()->getBusinessHours();
		$formCount = 0;
		$timeBlocks = $businessHours->getTimeBlocks();
		foreach ($timeBlocks as $block) {
			(new TimeBlockForm)
				->dayOfWeek($block->getDayOfWeek())
				->label("we're open from")
				->timeBlock($block)
				->echo();
			$formCount++;
		}
		for(; $formCount < 7; $formCount++)
			(new TimeBlockForm)->dayOfWeek(TimeBlock::DAYS[$formCount])->label("we're open from")->echo();
	?>
</form>

<?php require "Foot.php";