<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 11/4/18
 * Time: 4:52 PM
 */

const PAGE_TYPE = "Employee";
require "Head.php";

echo " <h1>Schedule for {$staff->getFirstName()} {$staff->getLastName()}</h1>";
if ($staff instanceof Employee) {
	$Schedule = $staff->getScheduleFor(new DateTime("monday this week"));
	(new ScheduleView($Schedule))->echo();
}
































require "Foot.php";