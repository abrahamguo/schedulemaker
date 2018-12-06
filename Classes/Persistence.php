<?php
	/**
	 * Created by PhpStorm.
	 * User: abraham
	 * Date: 10/16/18
	 * Time: 8:40 AM
	 */

	abstract class Persistence {

		private static $cacheByID = [];

		protected static $colsWithIds = [];

		protected static $colsToIgnore = [];

		protected static $colsNoRecursiveSync = [];

		public function applyVals (array $assoc): Persistence {
			foreach ($assoc as $key => $value)
				if (property_exists($this, $key)) {

					// Handle date/time values
					if (
						!($value instanceof DateTime) &&
						(stripos($key, "date") !== false || ($isTime = stripos($key, "time") !== false)) &&
						stripos($key, "id") === false
					)
						/* If this is a time value coming from the database, then remove the trailing seconds (`:00`) from the end
							 of the value */
						$value = new DateTime(
							($isTime && substr_count($value, ":") == 2)
								? substr($value, 0, -3)
								: $value
						);

					// The ternary statement here handles boolean instance variables
					$this->$key = is_bool($this->$key) ? !!$value : $value;
				}
				return $this;
		}

		protected function __construct (?array $assoc = []) {
			if ($assoc) $this->applyVals($assoc);
			$id = $this->id();
			if ($id) self::$cacheByID[$this->table()][$id] = $this;
		}

		/**
		 * Used when taking the data OUT of the object INTO the database
		 * @return array - associative array of the data to put into the DB
		 */
		protected function getDBDataArr (): array {
			$data = [];

			// Loop through the instance variables of this class
			foreach (get_object_vars($this) as $instanceVar => $value) {
				if (in_array($instanceVar, static::$colsToIgnore)) continue;
				$valueIsPersistent = $value instanceof Persistence || in_array($instanceVar, static::$colsWithIds);
				$valueIsArray = is_array($value);
				if ($value && !in_array($instanceVar, static::$colsNoRecursiveSync)) {
					if ($valueIsPersistent) $value->sync();
					if ($valueIsArray && $value[0] instanceof Persistence)
						foreach ($value as $subValue) $subValue->sync();
				}
				/* The second part of this `if` statement ensures that if the primary (ID) column has no value, it is excluded
					 from the query. This allows it to automatically be assigned a value on the database side. */
				if ($valueIsArray || $instanceVar == $this->getIDColName() && !$value) continue;

				// Set the column name and its value
				if ($valueIsPersistent) $dbVal = $value ? $value->id() : null;
				else $dbVal = $value;
				$data[$instanceVar . ($valueIsPersistent ? "ID" : "")] = $dbVal;
			}
			return $data;
		}

		public function sync (): void {
			if ($this->id()) DB::update($this->table(), $this->getDBDataArr(), $this->getIDColName(), $this->id());
			else $this->{$this->getIDColName()} = DB::insert($this->table(), $this->getDBDataArr());
		}

		protected static function table (): string {
			for ($class = get_called_class(); ($nextClass = get_parent_class($class)) != get_class(); $class = $nextClass);
			return $class;
		}

		public function remove () {
			DB::delete($this->table(), $this->getIDColName(), $this->id());
		}

		protected static function getIDColName () { return static::table() . "ID"; }

		public function id () { return $this->{$this->getIDColName()}; }

		public static function getByID (?int $id): Persistence {
			$table = self::table();
			if ($cached = self::$cacheByID[$table][$id]) return $cached;
			return static::create(DB::getByID($table, self::getIDColName(), $id));
		}

		public static function getWhere (array $params): array {
			return array_map(
				function (array $row): Persistence { return static::create($row); },
				DB::getWhere(self::table(), $params)
			);
		}

		protected static function create (array $row): Persistence { return new static($row); }

	}