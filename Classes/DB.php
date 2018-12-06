<?php
	/**
	 * Created by PhpStorm.
	 * User: abraham
	 * Date: 10/4/18
	 * Time: 6:14 PM
	 */

	class DB {

		private const HOST = "localhost";

		private const USER = "root";

		private const PASSWORD = "";

		private const DB_NAME = "ScheduleMe";

		/**
		 * @type mysqli
		 */
		private static $db;

		private static function checkConn () {
			self::$db = new mysqli(self::HOST, self::USER, self::PASSWORD, self::DB_NAME);
		}

		public static function query (string $query) {
			self::checkConn();
			$ret = self::$db->query($query);
			if ($ret) return $ret;
			echo "
				<p><b>MySQL query error:</b></p>
				<p><b>" . self::$db->error . "</b></p>
				<pre>$query</pre>
				<pre>
			";
			debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			echo "</pre>";
			die;
		}

		private static function formatDBVals (array $data): array {
			foreach ($data as $col => $val) {
				if ($val === null) $val = "NULL";
				else if (is_bool($val)) $val = (int)$val;
				else $val = "'" . self::esc($val) . "'";
				$data[$col] = $val;
			}
			return $data;
		}

		public static function insert (string $table, array $data): int {
			$data = self::formatDBVals($data);
			DB::query("
				INSERT INTO $table
				(" . implode(", ", array_keys($data)) . ")
				VALUES (" . implode(", ", array_values($data)) . ")
			");
			return DB::$db->insert_id;
		}

		public static function update (string $table, array $data, string $idCol, int $id): void {
			$SET = [];
			foreach (self::formatDBVals($data) as $col => $val) $SET[] = "$col = $val";
			DB::query("
				UPDATE $table
				SET " . implode(", ", $SET) . "
				WHERE $idCol = $id
			");
		}

		public static function delete (string $table, string $idCol, int $id): void {
			DB::query("
				DELETE FROM $table
				WHERE $idCol = $id
			");
		}

		public static function esc ($value): string {
			self::checkConn();
			return self::$db->real_escape_string($value);
		}

		public static function getByID (string $table, string $idCol, ?int $id): array {
			if (!$id) return [];
			$assoc = DB::query("
				SELECT *
				FROM $table
				WHERE $idCol = $id
			")->fetch_assoc();
			return $assoc ? $assoc : [];
		}

		public static function getWhere (string $table, array $params): array {
			$conditions = [];
			foreach($params as $key => $value)
				$conditions[] = "$key = " . (is_bool($value) ? (int)$value : ("'" . self::esc($value) . "'"));
			return DB::query("
				SELECT *
				FROM $table
				WHERE " . implode(" AND ", $conditions)
			)->fetch_all(MYSQLI_ASSOC);
		}

	}