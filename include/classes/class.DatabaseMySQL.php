<?php

	class DatabaseMySQL
	{
		var $sqlLink;
		var $sqlConnected = false;
		var $sqlResult;
		var $sqlQueryLog = array();

		var $serverHost;
		var $serverDatabase;
		var $serverUser;
		var $serverPassword;

		var $cursorArray = array();
		var $cursorBindingsArray = array();
		var $cursorBindingsRawArray = array();
		var $cursorResultArray = array();

		var $replaceCursor;
		var $replaceError = false;

		var $totalTime;
		var $connectTime;

		var $_lastId = 0;

		function __construct($serverName, $databaseName = '', $username = '', $password = '')
		{
			if (is_array($serverName) === true)
			{
				$this->serverHost = $serverName['host'];
				$this->serverDatabase = $serverName['database'];
				$this->serverUser = $serverName['username'];
				$this->serverPassword = $serverName['password'];
			}
			else 
			{
				$this->serverHost = $serverName;
				$this->serverDatabase = $databaseName;
				$this->serverUser = $username;
				$this->serverPassword = $password;
			}
		}

		function __checkConnected()
		{
			global $isDebug;

			if ($this->sqlConnected == false)
			{
				$microTime = microtime();
				$microTime = explode(" ",$microTime);
				$microTime = $microTime[1] + $microTime[0];
				$startTime = $microTime;

				error_reporting(E_ALL);

				$this->sqlLink = @mysql_connect($this->serverHost, $this->serverUser,  $this->serverPassword, true);

				$microTime = microtime();
				$microTime = explode(" ",$microTime);
				$microTime = $microTime[1] + $microTime[0];
				$endTime = $microTime;

				$this->connectTime = ($endTime - $startTime);

				if ($this->sqlLink == false)
				{
					//if ($isDebug === true)
						echo 'Database error: could not connect to database<br />'.mysql_error();

					$this->sqlConnected = false;
				}
				else
				{
					$selectDbResult = mysql_select_db($this->serverDatabase, $this->sqlLink);

					$this->sqlConnected = true;
				}
			}
		}

		function __closeConnection()
		{
			$this->sqlConnected = false;
		}

		function prepareQuery($queryString)
		{
			$newCursor = 'sqlCursor'.($this->_lastId + 1);
			$this->_lastId++;

			$this->cursorArray[$newCursor] = $queryString;
			$this->cursorBindingsArray[$newCursor] = array();
			return $newCursor;
		}

		function bindInput($cursor, $varName, $varValue)
		{
			$this->cursorBindingsArray[$cursor][$varName] = $varValue;
			$this->cursorBindingsRawArray[$cursor][$varName] = false;
		}

		function bindInputRaw($cursor, $varName, $varValue)
		{
			$this->cursorBindingsArray[$cursor][$varName] = $varValue;
			$this->cursorBindingsRawArray[$cursor][$varName] = true;
		}

		function executeQueryRaw($cursor)
		{
			global $isDebug;

			$sqlQuery = $this->cursorArray[$cursor];

			$this->__checkConnected();

			$microTime = microtime();
			$microTime = explode(" ",$microTime);
			$microTime = $microTime[1] + $microTime[0];
			$startTime = $microTime;

			$this->cursorResultArray[$cursor] = mysql_query($sqlQuery, $this->sqlLink);

			$microTime = microtime();
			$microTime = explode(" ",$microTime);
			$microTime = $microTime[1] + $microTime[0];
			$endTime = $microTime;

			$sqlQueryLogItem = array($sqlQuery, $endTime - $startTime);
			$this->sqlQueryLog[] = $sqlQueryLogItem;
			
			if ($isDebug === true)
			{
				
				

				if ($this->cursorResultArray[$cursor] == false)
				{
					//echo '\r\nMySQL Error: '.mysql_error($this->sqlLink)."<br />\r\n";
				}
			}

			$this->totalTime += ($endTime - $startTime);
		}

		function executeQuery($cursor)
		{
			$sqlQuery = $this->__parseQuery($cursor);
			
			if ($sqlQuery == false)
				return false;

			$this->__checkConnected();

			$microTime = microtime();
			$microTime = explode(" ",$microTime);
			$microTime = $microTime[1] + $microTime[0];
			$startTime = $microTime;

			$this->cursorResultArray[$cursor] = mysql_query($sqlQuery, $this->sqlLink);

			$microTime = microtime();
			$microTime = explode(" ",$microTime);
			$microTime = $microTime[1] + $microTime[0];
			$endTime = $microTime;
			
			//$requestEndTime = time_precise();
			$requestTotalTime = number_format($endTime - $startTime, 6, '.', '');
		
			$sqlQueryLogItem = array($sqlQuery, $endTime - $startTime, $requestTotalTime);
			$this->sqlQueryLog[] = $sqlQueryLogItem;
			
			if(isset($GLOBALS['debug']) && $GLOBALS['debug'] === true)
			{
				
				//$this->sqlQueryLog[] = $sqlQueryLogItem;
			}

			if (($this->cursorResultArray[$cursor] == false) && ($GLOBALS['debug'] === true))
			{
				echo mysql_error($this->sqlLink)."<br />\r\n";
				//throw new Exception('[MySQL Error] '.mysql_error($this->sqlLink).': '.$sqlQuery);
			}

			$this->totalTime += ($endTime - $startTime);
		}

		function executeQueryAffectedRows($cursor)
		{
			$this->executeQuery($cursor);

			return mysql_affected_rows($this->sqlLink);
		}

		function executeQueryRowCount($cursor)
		{
			$this->executeQuery($cursor);

			return mysql_num_rows($this->cursorResultArray[$cursor]);
		}

		function executeQueryInsertId($cursor)
		{
			$this->executeQuery($cursor);
			
			return mysql_insert_id($this->sqlLink);
		}

		function executeQueryItem($cursor)
		{
			$this->executeQuery($cursor);

			$numRows = intval(@mysql_num_rows($this->cursorResultArray[$cursor]));

			if ($numRows > 0)
			{
				$rowData = $this->fetchRowIndex($cursor, 0);
				return $rowData[0];
			}
			else
				return false;
		}

		function executeQueryRow($cursor)
		{
			$this->executeQuery($cursor);

			$numRows = intval(@mysql_num_rows($this->cursorResultArray[$cursor]));

			if ($numRows > 0)
			{
				$rowData = $this->fetchRow($cursor, 0);
				return $rowData;
			}
			else
				return false;
		}

		function executeQueryArray($cursor)
		{
			$this->executeQuery($cursor);

			$returnArray = array();

			$rows = mysql_num_rows($this->cursorResultArray[$cursor]);

			if ($rows == 0)
				return $returnArray;

			for ($i = 0; $i < $rows; $i++)
			{
				mysql_data_seek($this->cursorResultArray[$cursor], $i);
				$returnArray[] = mysql_fetch_assoc($this->cursorResultArray[$cursor]);
			}

			return $returnArray;
		}

		function executeQueryArray_noAssoc($cursor)
		{
			$this->executeQuery($cursor);

			$returnArray = array();

			$rows = mysql_num_rows($this->cursorResultArray[$cursor]);

			if ($rows == 0)
				return $returnArray;

			for ($i = 0; $i < $rows; $i++)
			{
				mysql_data_seek($this->cursorResultArray[$cursor], $i);
				$returnArray[] = mysql_fetch_row($this->cursorResultArray[$cursor]);
			}

			return $returnArray;
		}

		function fetchRowIndex($cursor, $rowNumber)
		{
			mysql_data_seek($this->cursorResultArray[$cursor], $rowNumber);
			return mysql_fetch_row($this->cursorResultArray[$cursor]);
		}

		function fetchRow($cursor, $rowNumber)
		{
			mysql_data_seek($this->cursorResultArray[$cursor], $rowNumber);
			return mysql_fetch_assoc($this->cursorResultArray[$cursor]);
		}
		
		function lastError()
		{
			return mysql_error($this->sqlLink);
		}

		function lastErrorCode()
		{
			return mysql_errno($this->sqlLink);
		}
		
		function getParsedCursor($cursor)
		{
			return $this->__parseQuery($cursor);
		}

		function __parseQuery($cursor)
		{
			$this->replaceCursor = $cursor;
			$this->replaceError = false;

			$sqlQuery = preg_replace_callback("~\:([a-zA-z]{1}[a-zA-z0-9]*)~si", array(&$this, '__parseQueryRegex'), ' '.$this->cursorArray[$cursor].' ');

			if ($this->replaceError == true)
			{
				echo 'Not all values are bound.';
				return false;
			}

			return $sqlQuery;
		}

		function __parseQueryRegex($matches)
		{
			global $isDebug;

			if (array_key_exists($matches[1], $this->cursorBindingsArray[$this->replaceCursor]) == true)
			{
				if ($this->cursorBindingsRawArray[$this->replaceCursor][$matches[1]] === true)
					return $this->cursorBindingsArray[$this->replaceCursor][$matches[1]];
				else
					return @mysql_escape_string($this->cursorBindingsArray[$this->replaceCursor][$matches[1]]);
			}
			else
			{
				echo 'Missing value for: '.$matches[1].' ';
				$this->replaceError = true;
			}
		}

		function freeCursor($cursor)
		{
			unset($this->cursorResultArray[$cursor]);
			unset($this->cursorBindingsArray[$cursor]);
			unset($this->cursorBindingsRawArray[$cursor]);
			unset($this->cursorArray[$cursor]);
		}

		function getTableInfo()
		{
			$returnArray = array();

			$tableString = '';
			$tableCount = 0;

			foreach ($this->getTables() as $tableName)
			{
				if (strlen($tableString) == 0)
					$tableString = '"'.$tableName.'"';
				else
					$tableString = $tableString . ',"'.$tableName.'"';

				$tableFields = $this->getTableFields($tableName);

				$fieldString = '';
				foreach ($tableFields as $fieldName)
				{
					if (strlen($fieldString) == 0)
						$fieldString = '"'.$fieldName.'"';
					else
						$fieldString = $fieldString . ',"'.$fieldName.'"';
				}

				$returnArray['tables'][$tableCount]['fields'] = $tableFields;
				$returnArray['tables'][$tableCount]['fieldstring'] = $fieldString;

				$tableCount++;
			}

			$returnArray['tablestring'] = $tableString;

			return $returnArray;
		}

		function getTables()
		{
			$cursor = $this->prepareQuery('SHOW TABLES');
			$databaseTables = $this->executeQueryArray_noAssoc($cursor);
			$this->freeCursor($cursor);

			$newDatabaseTables = array();

			foreach ($databaseTables as $databaseTable)
			{
				if (!in_array($databaseTable[0], $GLOBALS['reports_application']['settings']['skip_tables']))
					$newDatabaseTables[] = $databaseTable[0];
			}

			return $newDatabaseTables;
		}

		function getTableFields($tableName)
		{
			$cursor = $this->prepareQuery('DESCRIBE :tableName');
			$this->bindInput($cursor, 'tableName', $tableName);
			$tableFields = $this->executeQueryArray_noAssoc($cursor);
			$this->freeCursor($cursor);

			$newTableFields = array();

			foreach ($tableFields as $tableField)
			{
				$newTableFields[] = $tableField[0];
			}

			return $newTableFields;
		}

		function autoCommit($enable)
		{
			if ($enable === true)
			{
				$cursor = $this->prepareQuery('SET autocommit=1;');
				$this->executeQuery($cursor);
				$this->freeCursor($cursor);
			}
			else
			{
				$cursor = $this->prepareQuery('SET autocommit=0;');
				$this->executeQuery($cursor);
				$this->freeCursor($cursor);
			}
		}

		function startTransaction()
		{
			$cursor = $this->prepareQuery('START TRANSACTION');
			$this->executeQuery($cursor);
			$this->freeCursor($cursor);
		}

		function commit()
		{
			$cursor = $this->prepareQuery('COMMIT');
			$this->executeQuery($cursor);
			$this->freeCursor($cursor);
		}

		function rollback()
		{
			$cursor = $this->prepareQuery('ROLLBACK');
			$this->executeQuery($cursor);
			$this->freeCursor($cursor);
		}

		function resetClass()
		{
			$this->cursorArray = array();
			$this->cursorBindingsArray = array();
			$this->cursorBindingsRawArray = array();
			$this->cursorResultArray = array();

			$this->replaceCursor = null;
			$this->replaceError = false;

			$this->totalTime = 0;
			$this->connectTime = 0;
			$this->_lastId = 0;
		}

		function ping()
		{
			$this->__checkConnected();
			return mysql_ping($this->sqlLink);
		}
	}

?>