<?php
	class Conexion
	{
		protected $host;
		protected $user;
		protected $pass;
		protected $db;

		public function localhost()
		{
			try
			{
				$this->host	= 'localhost';
				$this->user	= 'root';
				$this->pass	= 'root';
				$this->db	= '';

				return new MySQLI($this->host, $this->user, $this->pass, $this->db);
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		public function cobranza()
		{
			try
			{
				$this->host	= 'localhost';
				$this->user	= 'root';
				$this->pass	= 'root';
				$this->db	= '';

				return new MySQLI($this->host, $this->user, $this->pass, $this->db);
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		public function padron()
		{
			try
			{
				$this->host	= '';
				$this->user	= 'root';
				$this->pass	= '';
				$this->db	= '';

				return new MySQLI($this->host, $this->user, $this->pass, $this->db);
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		public function nodum()
		{
			try
			{
				$this->host	= '';
				$this->user	= '';
				$this->pass	= '';
				$this->db	= '';

				if(sqlsrv_connect($this->host, ['Database' => $this->db, 'UID' => $this->user, 'PWD' => $this->pass, 'CharacterSet' => 'UTF-8']))
				{
					return sqlsrv_connect($this->host, array('Database' => $this->db, 'UID' => $this->user, 'PWD' => $this->pass, 'CharacterSet' => 'UTF-8'));
				}
				else
					die( print_r( sqlsrv_errors(), true));
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}