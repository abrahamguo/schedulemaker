<?php

	spl_autoload_register(function (string $class): void {
		if (file_exists($filename = "Classes/$class.php")) require $filename;
	});