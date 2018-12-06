<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 10/14/2018
 * Time: 2:28 PM
 */

class Staff extends Persistence {

	/**
	 * @type int
	 */
	protected $StaffID;

	/**
	 * @type string
	 */
	protected $FirstName;

	/**
	 * @type string
	 */
	protected $LastName;

	/**
	 * @type string
	 */
	protected $StaffType;

	/**
	 * @type string
	 */

	protected $Username;

	/**
	 * @type string
	 */
	protected $Password;

	public function __construct (array $assoc) {

		parent::__construct($assoc);

		$this->StaffID = $assoc["StaffID"];
		$this->FirstName = $assoc["FirstName"];
		$this->LastName = $assoc["LastName"];
		$this->Username = $assoc["UserName"];
		$this->Password = $assoc["Password"];

		if (!$this->Username)
			$this->Username = strtolower(substr($this->FirstName,0, 1) . substr($this->LastName, 0,5));
		if (!$this-> Password) $this->Password = "password";


	}

	/**
	 * @return int
	 */
	public function getStaffID(): int
	{
		return $this->StaffID;
	}

	/**
	 * @param $row
	 * @return Employee|Employer
	 */
	protected static function create($row): Persistence {
		if ($row["StaffType"] == "Employer")
			return new Employer($row);
		else if ($row["StaffType"] == "Employee")
			return new Employee($row);
	}

	/**
	 * @return string
	 */
	public function getFirstName(): ?string
	{
		return $this->FirstName;
	}

	/**
	 * @return string
	 */
	public function getLastName(): ?string
	{
		return $this->LastName;
	}

	/**
	 * @return string
	 */
	public function getStaffType(): string
	{
		return $this->StaffType;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->Username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->Password;
	}

}