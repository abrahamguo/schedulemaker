<?php

class Session {

	private static $loggedInUser;

	public static function getLoggedInUser (): ?Staff {
		if (self::$loggedInUser) return self::$loggedInUser;
		return $_SESSION["loggedInUserID"] ? self::$loggedInUser = Staff::getByID($_SESSION["loggedInUserID"]) : null;
	}

	public static function logout(): void { $_SESSION = []; }

	public static function login (string $username, string $password): void {

		$loggedInUser = Staff::getWhere([
			"UserName" => $username,
			"Password" => $password
		])[0];

		if ($loggedInUser){
			$_SESSION["loggedInUserID"] = $loggedInUser->id();
			Util::redir("{$loggedInUser->getStaffType()}.php?logon=1");
		}
		else Util::redir("?error=1");

	}
}

