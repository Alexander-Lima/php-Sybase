<?php
namespace Controller\Config;

class Database
{
    private $connection = null;

    private function getConnection() {
        if($this->connection === null) {
            $this->connection = 
            sasql_connect(
                "HOST=" . $_ENV["HOST"] . ":" . $_ENV["PORT"]  . ";" .
                "DBN=" . $_ENV["DBN"]  . ";" .
                "UID=" . $_ENV["UID"]  .  ";" .
                "PWD=" . $_ENV["PWD"]  . ";" .
                "ServerName=" . $_ENV["SERVER"]  . ";" .
                "charset=" . $_ENV["CHARSET"] 
            );
        }

        return $this->connection;
    }

    public function fetchAssoc(string $query): array {
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
     * @return returns an array.
    **/
    public function fetchPreparedAssoc(string $query, array $bindParams): array {
        $resultSet = [];
        $paramTypes = [];
        $paramValues = [];

        foreach($bindParams as $param) {
            $paramValues[] = $param["value"];
            $paramTypes[] = $param["type"];
        }

        $preparedStatement = sasql_prepare($this->getConnection(), $query);

        if(!empty($paramTypes) && !empty($paramValues)) {
            sasql_stmt_bind_param($preparedStatement, join("", $paramTypes), ...$paramValues);
        }

        sasql_stmt_execute($preparedStatement);
        $resultData = sasql_stmt_result_metadata($preparedStatement);

        while($row = sasql_fetch_assoc($resultData)) {
            $resultSet[] = $row;
        }

        return $resultSet;
    }
}