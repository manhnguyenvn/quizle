<?php

class DatabaseOperations
{
	private $dbh;

	private $result;

	private $show_db_error;

	public function __construct($show_db_error = true) {
		$this->show_db_error = $show_db_error;
		$this->dbh = @new mysqli(SERVER_NAME, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DBNAME);
		if($this->dbh->connect_errno)
			throw new Exception($this->show_db_error ? 'Server failed. It might be possible that your DB credentials are wrong' : 'Server Error (101)', 2);
	}

	public function GetDatabaseHandle() {
		return $this->dbh;
	}

	public function GetResultSet() {
		return $this->result;
	}

	public function GetNumRows() {
		return $this->result->num_rows;
	}

	public function GetNextResultRow() {
		return $this->result->fetch_assoc();
	}

	public function StartTransaction() {
		$this->dbh->autocommit(FALSE);
	}

	public function EndTransaction() {
		$this->dbh->commit();
	}

	public function ExecuteSQuery($query) {
		$this->result = $this->dbh->query($query);
		if(!$this->result)
			throw new Exception($this->show_db_error ? $this->dbh->error : 'Server Error (102)', 2);
	}

	public function ExecuteUIDQuery($query, $confirmation = 1) {
		$this->result = $this->dbh->query($query);
		if(!$this->result)
			throw new Exception($this->show_db_error ? $this->dbh->error : 'Server Error (103)', 2);
		if($confirmation == 1)
			if($this->dbh->affected_rows == 0)
				throw new Exception($this->show_db_error ? $this->dbh->error : 'Server Error (104)', 2);
	}
}

?>