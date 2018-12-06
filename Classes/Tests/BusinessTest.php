<?php

	use PHPUnit\Framework\TestCase;

	require "autoload.php";

	class BusinessTest extends TestCase {
		public function testBusinessHours() {
			$timeblock = new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"] );
			$temp = Business::getBusiness();
			$temp->getBusinessHours()->addTimeBlock($timeblock);
			$timeblock->sync();
			$this->assertNotEmpty(DB::getByID("TimeBlock", "TimeBlockID", $timeblock->id() ));

			$timeblock->applyVals([
				"StartTime" => "7:00 PM",
				"EndTime" => "10:00 PM",
				"DayOfWeek" => "Tuesday"
			]);
			$timeblock->sync();

			$timeblock = TimeBlock::getByID($timeblock->id());

			$this->assertEquals($timeblock->getStartTime()->format("g:i A"), "7:00 PM");
			$this->assertEquals($timeblock->getEndTime()->format("g:i A"), "10:00 PM");
			$this->assertEquals($timeblock->getDayOfWeek(), "Tuesday");

			DB::delete("TimeBlock", "TimeBlockID", $timeblock->id());
		}

		public function testCreateShift() {
			$timeblock = new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"]);
			$temp = Business::getBusiness();
			$temp->getShiftHours()->addTimeBlock($timeblock);
            $timeblock->sync();
			$this->assertNotEmpty(DB::getByID("TimeBlock", "TimeBlockID", $timeblock->id() ));
			DB::delete("TimeBlock", "TimeBlockID", $timeblock->id() );
		}

        public function testEditShift() {
            $timeblock = new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"]);
            $temp = Business::getBusiness();
            $temp->getShiftHours()->addTimeBlock($timeblock);
            $timeblock->sync();

            $timeblock = TimeBlock::getByID($timeblock->id());

            $this->assertEquals($timeblock->getStartTime()->format("g:i A"), "8:00 PM");
            $this->assertEquals($timeblock->getEndTime()->format("g:i A"), "9:00 PM");
            $this->assertEquals($timeblock->getDayOfWeek(), "Monday");

            $timeblock->applyVals([
                "StartTime" => "7:00 PM",
                "EndTime" => "10:00 PM",
                "DayOfWeek" => "Tuesday"
            ]);
            $timeblock->sync();

            $timeblock = TimeBlock::getByID($timeblock->id());

            $this->assertEquals($timeblock->getStartTime()->format("g:i A"), "7:00 PM");
            $this->assertEquals($timeblock->getEndTime()->format("g:i A"), "10:00 PM");
            $this->assertEquals($timeblock->getDayOfWeek(), "Tuesday");

            DB::delete("TimeBlock", "TimeBlockID", $timeblock->id() );
        }

        public function testRemoveShift()
        {
            $timeblock = new TimeBlock(["StartTime" => "8:00 PM","EndTime" => "9:00 PM", "DayOfWeek" => "Monday"]);
            $temp = Business::getBusiness();
            $temp->getShiftHours()->addTimeBlock($timeblock);
            $timeblock->sync();

            $timeblock = TimeBlock::getByID($timeblock->id());

            $timeblock->remove();
            $this->assertEmpty(DB::getByID("TimeBlock", "TimeBlockID", $timeblock->id()));
        }

	}