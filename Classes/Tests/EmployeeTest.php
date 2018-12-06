<?php

	use PHPUnit\Framework\TestCase;

	require "autoload.php";

	class EmployeeTest extends TestCase {

		public function testCreateEmployee (): void {
			$e = new Employee([
				"FirstName" => "Creation",
				"LastName" => "Test"
			]);
			$e->sync();

            $e = Employee::getByID($e->id());

			$this->assertNotEmpty(DB::getByID("Staff", "StaffID", $e->id()));
			DB::delete("Staff", "StaffID", $e->id());
		}

		public function testCreateEmployeeWithUsername (): void {
			$e = new Employee([
				"FirstName" => "Creation",
				"LastName" => "Test",
				"Username" => "foo"
			]);
			$e->sync();

            $e = Employee::getByID($e->id());

			$this->assertNotEmpty(DB::getByID("Staff", "StaffID", $e->id()));
			DB::delete("Staff", "StaffID", $e->id());
		}

		public function testRemoveEmployee(): void {
			$temp = new Employee([
				"FirstName" => "First", "LastName" => "Last"
			]);
			$temp->sync();

            $temp = Employee::getByID($temp->id());

			$temp->remove();
			$this->assertEmpty(DB::getByID("Staff","StaffID", $temp->id()));
		}

		public function testEditEmployee (): void {
            $e = new Employee([
                "FirstName" => "Creation",
                "LastName" => "Test"
            ]);
            $e->sync();

            $e = Employee::getByID($e->id());
            $this->assertSame($e->getFirstName(), "Creation");
            $this->assertSame($e->getLastName(), "Test");

            $e->applyVals([
                "FirstName" => "New",
                "LastName" => "Name"
            ]);
            $e->sync();

            $e = Employee::getByID($e->id());

            $this->assertSame($e->getFirstName(), "New");
            $this->assertSame($e->getLastName(), "Name");

            $this->assertNotEmpty(DB::getByID("Staff", "StaffID", $e->id()));
            DB::delete("Staff", "StaffID", $e->id());
		}

        public function testSubmitDefaultAvailability (): void {
            $e = new Employee([
                "FirstName" => "First", "LastName" => "Last"
            ]);

            $defaultSchedule = $e->getDefaultAvailability();
            $defaultSchedule->addTimeBlock(new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"]));

            $e->sync();

            $e = Employee::getByID($e->id());

            $this->assertNotEmpty(DB::getByID("TimeBlock","WeekID", $e->getDefaultAvailability()->id()));

            $e->remove();
            $this->assertEmpty(DB::getByID("Staff","StaffID", $e->id()));
        }

        public function testEditDefaultAvailability (): void {
            $e = new Employee([
                "FirstName" => "First", "LastName" => "Last"
            ]);

            $defaultSchedule = $e->getDefaultAvailability();
            $defaultSchedule->addTimeBlock(new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"]));

            $e->sync();

            $e = Employee::getByID($e->id());


            $timeblock = $e->getDefaultAvailability()->getTimeBlocks()[0];
            $this->assertEquals($timeblock->getStartTime()->format("g:i A"), "8:00 PM");
            $this->assertEquals($timeblock->getEndTime()->format("g:i A"), "9:00 PM");

            $timeblock->applyVals([
                "StartTime" => "7:00 PM",
                "EndTime" => "10:00 PM"
            ]);
            $e->sync();

            $e = Employee::getByID($e->id());

            $timeblock = $e->getDefaultAvailability()->getTimeBlocks()[0];
            $this->assertEquals($timeblock->getStartTime()->format("g:i A"), "7:00 PM");
            $this->assertEquals($timeblock->getEndTime()->format("g:i A"), "10:00 PM");

            $e->remove();
            $this->assertEmpty(DB::getByID("Staff","StaffID", $e->id()));
        }

	}