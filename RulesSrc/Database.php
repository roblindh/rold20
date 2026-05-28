<?php

/**
 * Database Abstraction Layer
 * 
 * Provides a PDO-based wrapper for database operations.
 * Replaces mysqli with prepared statements for security and consistency.
 */
class Database
{
    private static $instance = null;
    private $pdo = null;
    private $lastStatement = null;

    /**
     * Get database singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database
     * 
     * @param string $host Database host
     * @param string $user Database user
     * @param string $password Database password
     * @param string $database Database name
     * @throws Exception
     */
    public function connect($host, $user, $password, $database)
    {
        try {
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
            $this->pdo = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Execute a query (for SELECT queries)
     * Returns an iterator for fetching results
     * 
     * @param string $query SQL query string
     * @param array $params Optional bound parameters
     * @return QueryResult
     */
    public function query($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $this->lastStatement = $stmt;
            return new QueryResult($stmt);
        } catch (PDOException $e) {
            throw new Exception("Query error: " . $e->getMessage() . "\nQuery: " . $query);
        }
    }

    /**
     * Execute a query and return all results as array
     * 
     * @param string $query SQL query string
     * @param array $params Optional bound parameters
     * @return array Array of result rows
     */
    public function fetchAll($query, $params = [])
    {
        $result = $this->query($query, $params);
        return $result->fetchAll();
    }

    /**
     * Execute a query and return first row
     * 
     * @param string $query SQL query string
     * @param array $params Optional bound parameters
     * @return array|null First row or null
     */
    public function fetchOne($query, $params = [])
    {
        $result = $this->query($query, $params);
        return $result->fetch();
    }

    /**
     * Execute an insert/update/delete query
     * 
     * @param string $query SQL query string
     * @param array $params Optional bound parameters
     * @return int Number of affected rows
     */
    public function execute($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $this->lastStatement = $stmt;
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Execute error: " . $e->getMessage() . "\nQuery: " . $query);
        }
    }

    /**
     * Get last insert ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get number of rows from last query
     * 
     * @return int Number of rows
     */
    public function numRows()
    {
        if ($this->lastStatement === null) {
            return 0;
        }
        return $this->lastStatement->rowCount();
    }

    /**
     * Close database connection
     */
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * Check if connected
     * 
     * @return bool
     */
    public function isConnected()
    {
        return $this->pdo !== null;
    }
}

/**
 * Helper class for query results
 * Provides iterator interface for fetching rows
 */
class QueryResult
{
    private $statement = null;
    private $results = null;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    /**
     * Fetch next row as associative array
     * 
     * @return array|null
     */
    public function fetch()
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all remaining rows
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get row count
     * 
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * Iterator support for while loops
     * Usage: while ($row = $result->fetch())
     * 
     * @return array|null Next row or null when done
     */
    public function __invoke()
    {
        return $this->fetch();
    }
}
