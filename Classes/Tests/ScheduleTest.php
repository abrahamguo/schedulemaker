<?php

    use PHPUnit\Framework\TestCase;

    require "autoload.php";

    class ScheduleTest extends TestCase {





    	/* PRIVATE HELPER METHODS */

    	private static function getNextMonday (): DateTime { return new DateTime("Monday next week"); }

    	private static function createWeek (array $times): Week {
    		$week = new Week;
				foreach ($times as $time)
					$week->addTimeBlock(new TimeBlock([
						"DayOfWeek" => "Monday",
						"StartTime" => $time[0],
						"EndTime" => $time[1]
					]));
    		return $week;
			}

			private static function createEmployeeWithAvailability (array $availability = [["8AM", "9AM"]]): Employee {
				return new Employee([ "DefaultAvailability" => self::createWeek($availability) ]);
			}

			private static function getScheduleFor (Employee $e): Week { return $e->getScheduleFor(self::getNextMonday()); }

			private static function createScheduleGenerationTest (
				array $employees,
				?array $shiftHours = [["8AM", "9AM"]]
			): Week {
				ScheduleCreator::make(
					ScheduleTest::getNextMonday(),
					$employees,
					ScheduleTest::createWeek($shiftHours),
					$unassignedShifts = new Week
				);
				return $unassignedShifts;
			}

			private function assertTimeBlockCount (Week $actual, int $expected = 0): void {
    		$this->assertEquals($expected, count($actual->getTimeBlocks()));
			}

			private function assertEmployeeShiftCount (Employee $e, int $expected = 0): void {
				$this->assertTimeBlockCount($this->getScheduleFor($e), $expected);
			}





			/* PUBLIC TEST METHODS */

			/**
			 * Test creating a schedule with someone who has no availability.
			 */
    	public function testNoAvailability (): void {
				$this->assertTimeBlockCount($this->createScheduleGenerationTest([ $e = new Employee ]), 1);
				$this->assertEmployeeShiftCount($e);
			}

			/**
			 * Test creating a schedule when no shifts have been defined.
			 */
			public function testNoShifts (): void {
				$this->assertTimeBlockCount(
					$this->createScheduleGenerationTest([ $e = $this->createEmployeeWithAvailability() ], [])
				);
				$this->assertEmployeeShiftCount($e);
			}

			/**
			 * Test creating a schedule with two employees, one of which has more availability than the other. Ensure that
			 * each employee is assigned one shift.
			 */
			public function testPrioritizingSchedule (): void {
				foreach (range(0, 1) as $i) {
					$employees = array_map(
						[$this, "createEmployeeWithAvailability"],
						[ [["8AM", "10AM"]], [["8AM", "11AM"]] ]
					);
					$this->assertTimeBlockCount(
						$this->createScheduleGenerationTest(
							// The second time around, send the employees in reverse order
							$i ? array_reverse($employees) : $employees,
							$shifts = [ ["8AM", "9AM"], ["9AM", "10AM"] ]
						)
					);
					// Loop through each employee and assert it
					foreach ($employees as $i => $employee) {
						$shift = $this->getScheduleFor($employee)->getTimeBlocks()[0];
						$this->assertEmployeeShiftCount($employee, 1);
						foreach ([$shift->getStartTime(), $shift->getEndTime()] as $j => $time)
							$this->assertEquals(
								$shifts[$i][$j],
								$time->format("gA")
							);
					}
				}
			}

			public function testNoEmployees (): void {
				$this->assertTimeBlockCount($this->createScheduleGenerationTest([]), 1);
			}

			public function testTimeBlockOverlaps (): void {
				$this->assertTimeBlockCount(
					$this->createScheduleGenerationTest(
						[
							$e = $this->createEmployeeWithAvailability([
								["7AM", "8AM"],
								["9AM", "11AM"],
								["12PM", "2PM"],
								["4PM", "7PM"],
								["9PM", "10PM"]
							])
						],
						[["7AM", "8AM"], ["10AM", "12PM"], ["1PM", "3PM"], ["5PM", "6PM"], ["8PM", "11PM"]]
					),
					3
				);
				$this->assertEmployeeShiftCount($e, 2);
			}

    }