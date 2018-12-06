<?php

	class ScheduleView {

		private const HOUR_HEIGHT = 30;

		/**
		 * Array of days of the week, each of which has multiple time ranges, each of which has multiple time blocks
		 * @type TimeBlock[][][]
		 */
		private $days = [];

		/**
		 * @var bool
		 */
		private $showNames = true;

		/**
		 * @var bool
		 */
		private $compact = false;

		public function __construct ($weekOrWeeks) {

			foreach (TimeBlock::DAYS as $day)
				$this->days[$day] = [];

			$timeBlocks = [];

			foreach (is_array($weekOrWeeks) ? $weekOrWeeks : [$weekOrWeeks] as $week) {
				$thisWeekTimeBlocks = $week->getTimeBlocks();
				if ($thisWeekTimeBlocks) array_push($timeBlocks, ...$thisWeekTimeBlocks);
			}

			$timeBlocks = TimeBlock::sort($timeBlocks);

			foreach ($timeBlocks as $block)
				$this->days[$block->getDayOfWeek()][$block->getTimeCode()][] = $block;

		}

		public function echo (bool $editable = false): void { echo $this->render($editable); }

		public function noNames (): ScheduleView {
			$this->showNames = false;
			return $this;
		}

		public function compact (): ScheduleView {
			$this->compact = true;
			return $this;
		}

		public function render (bool $editable = false): string {
			$hours = range(0, 24);
			$first = true;
			$headingTag = $this->compact ? "p" : "h3";
			$o = "
				<div class='row no-gutters'>
				
					<!-- Generate the hour indicators -->
					<div class='col-1 pt-5 text-right'>
						"; foreach ($hours as $hour) { $o .= "
							<div
								class='
									d-flex
									align-items-center
									justify-content-end
									pr-2
									text-" . (($pm = intdiv($hour, 12) == 1) ? "dark" : "muted") . "
								'
								style='height: " . self::HOUR_HEIGHT . "px; transform: translateY(-" . self::HOUR_HEIGHT / 2 . "px)'
							>
								" . (($mod = $hour % 12) ? $mod : 12) . ($pm ? "p" : "a") . "m
							</div>
						"; } $o .= "
					</div><!-- /.col-1 -->
					
					
					<!-- Generate a column for each day -->
					"; foreach ($this->days as $dayName => $day) { $o .= "
						<div class='col'>
							<$headingTag class='text-center px-3' style='height: " . ($this->compact ? 1 : 2) . ".5rem'>
								$dayName
							</$headingTag>
							<div
								class='time-cells position-relative'
								style='
									border: solid rgba(0, 123, 255, .25);
									border-width: 0 1px 0 " . ($first ? "1px" : "0") . "; 
									height: " . self::HOUR_HEIGHT * 24 . "px
								'
							>
								"; foreach ($hours as $hour) $o .= "
									<div
										class='position-absolute w-100'
										style='
											background: rgba(0, 123, 255, .25);
											top: " . self::HOUR_HEIGHT * $hour . "px;
											height: 1px
									'></div>
								"; foreach ($day as $period) {
									if ($editable) { $editForm = "
										<input type='hidden' name='action' value='editEmployeesSchedule'>
										"; foreach ($period as $shift) {
										$scheduledEmployee = $shift->getWeek()->getScheduledEmployee();$editForm .= "
										<div class='form-group'>
											<label for='Employee'>
												Employee:
												<select name='employeeID[]'>
													<option value" . ($scheduledEmployee ? "" : " selected") . ">None</option>
													"; foreach (Employee::getAll() as $e) { $editForm .= "
														<option
															value='{$e->id()}'
															" . ($scheduledEmployee && $e->id() == $scheduledEmployee->id() ? "selected" : "") . "
														>
															{$e->getFirstName()} {$e->getLastName()}
														</option>
													"; } $editForm .=	"
												</select>
											</label>
										</div><!-- /.form-group -->
										<input type='hidden' name='shiftID[]' value='{$shift->id()}'>"; } $editForm .= "
										";
										$modal = (new Modal)->large()->title("Edit Shift")->contents($editForm);
									}

									$o .= "
									
									<div
										class='position-absolute w-100 py-1 px-2 border-white border-bottom'
										style='
											background: rgba(0, 123, 255, .5);
											font-size: .8rem;
											top: " . self::HOUR_HEIGHT * $period[0]->getStartHours() . "px;
											height: " . self::HOUR_HEIGHT * $period[0]->getDurationHours() . "px;
											cursor: pointer;
										'
										" . ($editable ? $modal->renderModalAttrs() : "") . "
									>
										";
										if ($editable) echo $modal->renderModal();
										if ($this->showNames) foreach ($period as $timeBlock) { $e = $timeBlock->getWeek()->getEmployee(); $o .=
											($e ? "{$e->getFirstName()} {$e->getLastName()}" : "Unassigned") . "<br>
										"; } $o .= "
										{$period[0]->formatStartTime("g:ia")} - {$period[0]->formatEndTime("g:ia")}<br>
									</div>
								"; } /* End loop through each period in the given day */ $o .= "
							</div><!-- /.time-cells -->
						</div>";
						$first = false; } $o .= "
				</div><!-- /.row -->
			";
			return $o;
		}

	}