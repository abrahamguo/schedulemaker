<?php
	/**
	 * Created by PhpStorm.
	 * User: abraham
	 * Date: 10/27/18
	 * Time: 12:15 PM
	 */

	class TimeBlockForm {



		private static $formCounter = 0;

		/**
		 * @type TimeBlock
		 */
		private $timeBlock;
		/**
		 * @type string
		 */
		private $dayOfWeek;

		/**
		 * @var string
		 */
		private $pretext = "On";

		/* @type string
		 *
		 */
		private $label = "I'm available from";

		public function label (string $label): TimeBlockForm {
			$this->label = $label;
			return $this;
		}

		public function pretext (string $pretext): TimeBlockForm {
			$this->pretext = $pretext;
			return $this;
		}

		public function timeBlock (TimeBlock $timeBlock): TimeBlockForm {
			$this->timeBlock = $timeBlock;
			return $this;
		}

		public function dayOfWeek (string $dayOfWeek): TimeBlockForm {
			$this->dayOfWeek = $dayOfWeek;
			return $this;
		}



		public function echo () {

			$namePrefix = "TimeBlocks[" . self::$formCounter++ . "]";
			$idPostfix = "-Block" . self::$formCounter;
			echo "
				<div class='form-inline form-group'>
					<input
						name='{$namePrefix}[TimeBlockID]'
						type='hidden'
						value='" . ($this->timeBlock ? $this->timeBlock->id() : 0) . "'
					>
			";
			if (!$this->dayOfWeek) {
				echo "
					<label for='DayOfWeek$idPostfix'>{$this->pretext}</label>
					<select class='form-control mx-2' id='DayOfWeek$idPostfix' name='{$namePrefix}[DayOfWeek]'>
						<option value>select a day...</option>
				";
				foreach (TimeBlock::DAYS as $day)
					echo "
						<option" . ($this->timeBlock && $this->timeBlock->getDayOfWeek() == $day ? " selected" : "") . ">
							$day
						</option>
					";
				echo "</select>";
			}
			echo
					($this->dayOfWeek ? "<input type='hidden' name='{$namePrefix}[DayOfWeek]' value='$this->dayOfWeek'>" : "") . "
					<label for='StartTime$idPostfix'>
						" . ($this->dayOfWeek ? "On $this->dayOfWeek" : "") . ", $this->label
					</label>
					<input 
						class='form-control mx-2' 
						id='StartTime$idPostfix' 
						name='{$namePrefix}[StartTime]' 
						type='time' 
						value='" . ($this->timeBlock ? $this->timeBlock->getStartTime()->format("H:i") : "") . "'
					>
					<label for='EndTime$idPostfix'>to</label>
					<input
						class='form-control mx-2' 
						id='EndTime$idPostfix' 
						name='{$namePrefix}[EndTime]' 
						type='time' 
						value='" . ($this->timeBlock ? $this->timeBlock->getEndTime()->format("H:i") : "") . "'
					>
					.
					" . ($this->timeBlock && !$this->dayOfWeek ? "
						<button
							class='btn btn-danger ml-2' 
							type='submit' 
							title='Delete' 
							name='delete' 
							value='{$this->timeBlock->id()}'
						>
							<span class='far fa-trash-alt'></span>
						</button>
					" : "") . "
				</div><!-- /.form-group -->
			";
		}

		public static function checkNoDeletion (): bool {
			if ($timeBlockID = $_REQUEST["delete"])
				TimeBlock::getByID($timeBlockID)->remove();
			return !$timeBlockID;
		}

	}