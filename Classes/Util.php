<?php
	/**
	 * Created by PhpStorm.
	 * User: abraham
	 * Date: 10/16/18
	 * Time: 9:32 AM
	 */

	class Util {

		public static function log (string $msg, bool $printOnCLI = false): void {
			if (PHP_SAPI == "cli") {
				if (defined("DEBUG") || $printOnCLI) fprintf(STDERR, strip_tags($msg) . "\n");
			}
			else echo "<p class='m-0'><code><b>$msg</b></code></p>";
		}

		public static function dump (...$vars): void {
			echo "<pre>";
			foreach ($vars as $var) var_dump($var);
			debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			echo "</pre>";
		}

		public static function redir (string $to) {
			header("Location: $to");
			die;
		}

	}