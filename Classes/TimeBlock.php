<?php

	class TimeBlock extends Persistence {

		public const DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

		/**
		 * @var Week
		 */
		protected $Week;

		/**
		 * @var int
		 */
		protected $TimeBlockID;

		/**
		 * @var string
		 */
		protected $DayOfWeek;

		/**
		 * @var DateTime
		 */
		protected $StartTime;

		/**
		 * @var DateTime
		 */
		protected $EndTime;

		/**
		 * @return int
		 */
		public function getDayOfWeek (): string {
			return $this->DayOfWeek;
		}

		/**
		 * @return DateTime
		 */
		public function getStartTime (): DateTime {
			return $this->StartTime;
		}

		public function formatStartTime (String $formatStr): String {
			return $this->StartTime->format($formatStr);
		}

		public function formatEndTime (String $formatStr): String {
			return $this->EndTime->format($formatStr);
		}

		private static function getHours (DateTime $dt): float {
			return $dt->format("G") + $dt->format("i") / 60;
		}

		public function getStartHours (): float {
			return $this->getHours($this->StartTime);
		}

		public function getDurationHours (): float {
			return $this->getHours($this->EndTime) - $this->getHours($this->StartTime);
		}

		/**
		 * @return DateTime
		 */
		public function getEndTime(): DateTime {
			return $this->EndTime;
		}

		public function getWeek (): Week { return $this->Week; }

		public function setTimeBlockID(?int $id) : void {
			$this->TimeBlockID = $id;
		}

		protected static $colsNoRecursiveSync = ["Week"];

		public function __construct (array $assoc) {
			parent::__construct($assoc);
			if ($weekID = $assoc["WeekID"]) $this->Week = Week::getByID($weekID);
		}

		public function setWeek (Week $week) {
			$this->Week = $week;
			return $this;
		}

		protected function getDBDataArr (): array {
			return array_replace(
				parent::getDBDataArr(),
				[
					"StartTime" => $this->StartTime->format("H:i:s"),
					"EndTime" => $this->EndTime->format("H:i:s")
				]
			);
		}

		public function __clone () { $this->setTimeBlockID(null); }

		public function setEmployee (Employee $employee) {
			$this->Employee = $employee;
			return $this;
		}

		public function getTime (bool $startTime) : int {
			return (int)(
				array_search($this->DayOfWeek, self::DAYS)
				. $this->{($startTime ? "Start" : "End") . "Time"}->format("Hi")
			);
		}

		public function __toString (): string {
			return
				"TimeBlock " . ($this->TimeBlockID ? "(ID $this->TimeBlockID) " : "") . "- " .
				substr($this->DayOfWeek, 0, 3) .
				" {$this->StartTime->format("g:ia")} - {$this->EndTime->format("g:ia")}";
}

		public function display (): string {
			return substr($this->DayOfWeek, 0, 3) .
				" {$this->StartTime->format("g:ia")} - {$this->EndTime->format("g:ia")}";
		}

		public static function sort (array $timeBlocks): array {
			usort($timeBlocks, function (TimeBlock $a, TimeBlock $b): int {
				return $a->getTimeCode() - $b->getTimeCode();
			});
			return $timeBlocks;
		}

		public function getTimeCode (): int {
			return
				array_search($this->getDayOfWeek(), self::DAYS) .
				$this->formatStartTime("Hi") .
				$this->formatEndTime("Hi");
		}

		public static function getWhere (array $params): array {
			return self::sort(parent::getWhere($params));
		}

	}