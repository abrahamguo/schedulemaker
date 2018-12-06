<?php

	require "autoload.php";

	session_start();
	$bodyClass = defined("LOGIN_PAGE") ? "bg-dark" : "";

	$staff = Session::getLoggedInUser();
	if ($_GET["back"]) Util::redir("{$staff->getStaffType()}.php");

?><!doctype html>
<html>
	<head>
		<title>ScheduleMe</title>
		<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
		<link rel='shortcut icon' href='Images/favicon.png'>
		<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.5.0/css/all.css' integrity='sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU' crossorigin='anonymous'>
		<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
	</head>

	<body class='pt-3 <?=$bodyClass?>'>
		<div class='container'>
			<div class='form-group'>
				<?php
					if (!defined("LOGIN_PAGE"))
						echo "
							<a href='index.php?logout=1' class='btn btn-secondary float-right'>Logout</a>
							" . (defined("NO_BACK") ? "" : "<a href='?back=1'><i class='fas fa-arrow-left fa-3x'></i></a>");
				?>
			</div><!-- /.form-group -->
			<div class='clearfix form-group'></div><!-- /.form-group -->
			<?php

				if (PAGE_TYPE != "index") {
					if (!$staff) Util::redir("index.php?redirect=nologin");

					if (PAGE_TYPE != $staff->getStaffType())
						Util::redir("{$staff->getStaffType()}.php?redirect=wrongtype");
				}

				if ($_GET["redirect"] == "wrongtype")
					echo "<div class='alert alert-danger'>You do not have access to this page.</div>";

				if ($_GET["error"]) echo "<div class='alert alert-primary'>Invalid login, please try again.</div>";

				if ($_GET["logon"]) echo "<div class='alert alert-primary'>Hi, {$staff->getFirstName()}!</div>";