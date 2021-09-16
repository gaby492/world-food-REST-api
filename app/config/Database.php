<?php
	class Database {
		// DB params  
		private $host = 'database'; 
		private $db_name = 'WorldFood';
		private $username = 'root';
		private $password = 'root';
		private $port = '3306';
		private $conn;

		// DB Connection
		public function connect() { 
			try {
				$this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';port=' . $this->port, $this->username, $this->password);
    		$this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			} catch(PDOException $e) {  
				echo 'Connection Error: ' . $e->getMessage();
			}

			return $this->conn;
		}
	}