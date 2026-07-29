<?php
namespace Controller\Config;

use Exception;

class Database
{
    private $connection = null;
    
    
    private function getConnection()
    {
        if($this->connection === null) {
            $this->connection = 
            sasql_connect(
                "HOST={$_ENV["HOST"]}:{$_ENV["PORT"]};" .
                "DBN={$_ENV["DBN"]};" .
                "UID={$_ENV["UID"]};" .
                "PWD={$_ENV["PWD"]};" .
                "ServerName={$_ENV["SERVER"]};" .
                "CharSet={$_ENV["CHARSET"]};" .
                "ConnectionPool=NO;" .
                "IDLE=1"
            );
        }

        return $this->connection;
    }

    private function closeConnection()
    {
        if($this->connection !== null) {
            sasql_close($this->connection);
            $this->connection = null;
        }
    }

    public function fetchAssoc(string $query): array
    {
        $queryResult = sasql_query($this->getConnection(), $query);
        $resultSet = [];

        while($row = sasql_fetch_assoc($queryResult)) {
            $resultSet[] =  $row;
        }

        return $resultSet;
    }

    /**
     * @param string $query the sql query to use.
     * @param array $bindParams use associative array with type and value ["type" => "x", "value" => "y"].
     * @return array
    **/
    public function fetchPreparedAssoc(string $query, array $bindParams): array
    {
        $resultSet = [];
        $preparedStatement = $this->prepareStatement($query, $bindParams);
        sasql_stmt_execute($preparedStatement);
        $resultData = sasql_stmt_result_metadata($preparedStatement);

        while($row = sasql_fetch_assoc($resultData)) {
            $resultSet[] = $row;
        }

        $this->closeConnection();
        return $resultSet;
    }

     /**
     * @param string $query the sql query to use.
     * @param array $bindParams use associative array with type and value ["type" => "x", "value" => "y"].
     * @return bool
    **/
    public function updateOrInsertPrepared(string $query, array $bindParams): bool
    {
        $preparedStatement = $this->prepareStatement($query, $bindParams);
        sasql_stmt_execute($preparedStatement);

        $success = sasql_stmt_affected_rows($preparedStatement) > 0;

        $this->closeConnection();
        
        return $success;
    }

    private function prepareStatement(string $query, array $bindParams)
    {
        $paramTypes = [];
        $paramValues = [];

        foreach($bindParams as $param) {
            if(isset($param["value"]) && isset($param["type"])) {
                $paramValues[] = $param["value"];
                $paramTypes[] = $param["type"];
                continue;
            }

            throw new Exception("Supply value and type parameters for prepared statement");
        }

        $preparedStatement = sasql_prepare($this->getConnection(), $query);

        sasql_stmt_bind_param($preparedStatement, join("", $paramTypes), ...$paramValues);

        return $preparedStatement;
    }
}