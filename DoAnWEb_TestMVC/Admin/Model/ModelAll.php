<?php
class ModelAll
{
	public $connection;
	
	public function __construct()
	{
		
		$this->connection = new PDO('mysql:host='.$GLOBALS['DBHOST'].';dbname='.$GLOBALS['DBNAME'].';charset=utf8', $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->connection->setAttribute(PDO::ATTR_AUTOCOMMIT,0);

	}
	
	//=======HÀM INSERT DỮ LIỆU =======//
	public function insertData($tableName, $columnValue)
	{
		try
		{
			// $this->connection->beginTransaction();
			$sql1 = "INSERT INTO `$tableName` SET ";
			
			foreach($columnValue AS $ca1Column => $ca1Value)
			{
				$ca1ColumnUpper = strtoupper($ca1Column);
				@$sql2 .= "`$ca1Column`=:$ca1ColumnUpper, ";
			}
			$sql2 = rtrim(@$sql2, ", ");
			
			$preSQL = $sql1 . $sql2; //
			$query = $this->connection->prepare($preSQL);
			
			foreach($columnValue AS $ca2Column => $ca2Value)
			{
				$ca2ColumnUpper = strtoupper($ca2Column);
				$postSQL[$ca2ColumnUpper] = $ca2Value;
			}
			$query->execute($postSQL);
			$dataAdded = $query->rowCount();
			$lastInsertId = $this->connection->lastInsertId();
			
			// if($dataAdded> 0)
				// $this->connection->commit();
			return array("NUMBER_OF_ROW_INSERTED"=>$dataAdded, "LAST_INSERT_ID"=>$lastInsertId);
		}
		catch(Exception $e) 
		{
			// $this->connection->rollBack();
			return 0;
		}
	}
	
	//=======UPDATE FUNCTION SET =======//
	public function updateData($tableName, $columnValue, $whereValue = 0)
	{
		try
		{
			$sql1 = "UPDATE `$tableName` SET ";
			
			foreach($columnValue AS $ca1Column => $ca1Value)
			{
				$ca1ColumnUpper = strtoupper($ca1Column);
				@$sql2 .= "`$ca1Column`=:$ca1ColumnUpper, ";
			}
			$sql2 = rtrim(@$sql2, ", ");
			
			if($whereValue == 0)
			{
				$preSQL = $sql1 . $sql2;
			}
			else
			{
				$sql3 = " WHERE ";
				foreach($whereValue AS $wa1Column => $wa1Value)
				{
					$wa1ColumnUpper = strtoupper($wa1Column);
					@$sql4 .= "`$wa1Column`=:$wa1ColumnUpper AND ";
				}
				$sql4 = trim($sql4); $sql4 = rtrim($sql4, "AND"); $sql4 = trim($sql4);
				
				$preSQL = $sql1 . $sql2 . $sql3 . $sql4;
			}
			
			$query = $this->connection->prepare($preSQL);
			
			if($whereValue == 0)
			{
				foreach($columnValue AS $ca2Column => $ca2Value)
				{
					$ca2ColumnUpper = strtoupper($ca2Column);
					$postSQL[$ca2ColumnUpper] = $ca2Value;
				}
			}
			else
			{
				foreach($columnValue AS $ca2Column => $ca2Value)
				{
					$ca2ColumnUpper = strtoupper($ca2Column);
					$postSQL[$ca2ColumnUpper] = $ca2Value;
				}
				
				foreach($whereValue AS $wa2Column => $wa2Value)
				{
					$wa2WhereUpper = strtoupper($wa2Column);
					$postSQL[$wa2WhereUpper] = $wa2Value;
				}
			}
			$query->execute($postSQL);
			$this->connection->commit();
			$dataAdded = $query->rowCount();
			
			return $dataAdded;
		}
		catch(Exception $e) 
		{
			return -1;
		}
	}
	
	//=======DELETE FUNCTION SET=======//
	public function deleteData($tableName, $whereValue = 0)
	{
		try
		{
			$sql1 = "DELETE FROM `$tableName`";
			
			if($whereValue != 0)
			{
				$sql2 = " WHERE ";
				foreach($whereValue AS $wa1Column => $wa1Value)
				{
					$wa1ColumnUpper = strtoupper($wa1Column);
					@$sql2 .= "`$wa1Column`=:$wa1ColumnUpper AND ";
				}
				$sql2 = trim($sql2); $sql2 = rtrim($sql2, "AND"); $sql2 = trim($sql2); 
				
				$preSQL = $sql1 . $sql2;
				
				$query = $this->connection->prepare($preSQL);
				
				foreach($whereValue AS $wa2Column => $wa2Value)
				{
					$wa2WhereUpper = strtoupper($wa2Column);
					$postSQL[$wa2WhereUpper] = $wa2Value;
				}
				
				$query->execute($postSQL);
			}
			else
			{
				$preSQL = $sql1;
				$query = $this->connection->prepare($preSQL);
				$query->execute();
			}
			
			$dataAdded = $query->rowCount();
			if($dataAdded > 0)
			{
				$this->connection->commit();
			}
			return $dataAdded;
		}
		catch(Exception $e) 
		{
			return 0;
		}
	}
	
	//=======SELECT FUNCTION SET=======//
	public function selectData($columnName, $tableName, $whereValue = 0, $whereCondition = "=", $inColumn = 0, $inValue = 0, $formatBy = 0, $paginate = 0)
	{
		try
		{
			if($columnName == "*")
			{
				$sql1 = "SELECT ";
				$sql2 = "*";
			}
			else
			{
				$sql1 = "SELECT ";
				foreach($columnName AS $ca1Column => $ca1Value)
				{
					@$sql2 .= "`$ca1Value`, ";
				}
				$sql2 = rtrim(@$sql2, ", ");
			}
			$sql3 = " FROM `$tableName`";
			
			if(@$formatBy['GROUP'])
				$sql6 = " GROUP BY `" . $formatBy['GROUP'] . "`";
			else
				$sql6 = "";
			
			if(@$formatBy['ASC'])
				$sql7 = " ORDER BY `" . $formatBy['ASC'] . "` ASC";
			else if(@$formatBy['DESC'])
				$sql7 = " ORDER BY `" . $formatBy['DESC'] . "` DESC";
			else
				$sql7 = "";
			
			if($inValue != 0)
			{
				$sql4 = " WHERE `$inColumn` IN (";
				#  ('ONE_VALUE', 'ANOTHER_VALUE');
				foreach($inValue AS $in1Column => $in1Value)
				{
					@$sql5 .= "'$in1Value', ";
				}
				$sql5 = rtrim(@$sql5, ", ");
				$sql5 = $sql5 . ")";
			}
			
			if($paginate != 0)
				$sql8 = " LIMIT " . $paginate['POINT'] . ", " . $paginate['LIMIT'];
			else
				$sql8 = "";
			
			if($whereValue != 0)
			{
				$sql4 = " WHERE ";
				
				foreach($whereValue AS $wa1Column => $wa1Value)
				{
					@$sql5 .= $wa1Column .$whereCondition. "'" . $wa1Value . "' AND ";
				}
				$sql5 = trim($sql5); $sql5 = rtrim($sql5, "AND"); $sql5 = trim($sql5); 
				
				$preSQL = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8;
			}
			else
			{
				if($inValue != 0)
				{
					$preSQL = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8;
				}
				else
				{
					$preSQL = $sql1 . $sql2 . $sql3 . $sql6 . $sql7 . $sql8;
				}
			}
			
			$query = $this->connection->prepare($preSQL);
			$query->execute();
			$dataSelected = $query->fetchAll(PDO::FETCH_ASSOC);
			
			return $dataSelected;
		}
		catch(Exception $e) 
		{
			return 0;
		}
	}
	
	//=======SELECT FUNCTION SET=======//
	public function selectJoinData($columnName, $tableName, $joinType = "INNER", $onCondition, $whereValue = 0, $whereCondition="=", $formatBy = 0,$paginate=0)
	{
		try
		{
			if($columnName == "*")
			{
				$sql1 = "SELECT ";
				$sql2 = "*";
			}
			else
			{
				$sql1 = "SELECT ";
				foreach($columnName AS $ca1Column => $ca1Value)
				{
					@$sql2 .= "$ca1Value, ";
				}
				$sql2 = rtrim(@$sql2, ", ");
			}
			
			$sql3 = " FROM `" . $tableName['MAIN'] . "`";
			
			foreach($onCondition AS $on1Column => $on1Value)
			{
				@$sql4 .= " " . $joinType . " JOIN `" . $tableName[$on1Column] . "` ON " . $on1Value[0] . " = " . $on1Value[1];
			}
			
			if(@$formatBy['ASC'])
				$sql7 = " ORDER BY " . $formatBy['ASC'] . " ASC";
			else if(@$formatBy['DESC'])
				$sql7 = " ORDER BY " . $formatBy['DESC'] . " DESC";
			else
				$sql7 = "";
			
			if($whereValue != 0)
			{
				$sql5 = " WHERE ";
			
				foreach($whereValue AS $wa1Column => $wa1Value)
				{
					@$sql6 .= $wa1Column . $whereCondition . "'" . $wa1Value . "' AND ";
				}
				$sql6 = trim($sql6); $sql6 = rtrim($sql6, "AND"); $sql6 = trim($sql6); 
				
				$preSQL = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7;
			}
			else
			{
				$preSQL = $sql1 . $sql2 . $sql3 . $sql4 . $sql7;
			}
			
			if($paginate != 0)
				$preSQL = $preSQL . " LIMIT " . $paginate['POINT'] . ", " . $paginate['LIMIT'];
			
			$query = $this->connection->prepare($preSQL);
			$query->execute();
			$dataSelected = $query->fetchAll(PDO::FETCH_ASSOC);
			
			return $dataSelected;
		}
		catch(Exception $e) 
		{
			return 0;
		}
	}
	
	
}

?>