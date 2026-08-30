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
        if ($this->pdo !== null) {
            return;
        }

        if (class_exists('\Illuminate\Support\Facades\DB')) {
            try {
                $this->pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
                if ($this->pdo !== null) {
                    return;
                }
            } catch (\Throwable $t) {}
        }

        try {
            $realHost = $host ?: (getenv('DB_HOST') ?: '127.0.0.1');
            $realPort = getenv('DB_PORT') ?: 3306;
            if (str_contains((string)$realHost, ':')) {
                [$realHost, $realPort] = explode(':', (string)$realHost, 2);
            }
            $dsn = "mysql:host={$realHost};port={$realPort};dbname={$database};charset=utf8mb4";
            $this->pdo = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 2,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
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
        if ($this->pdo === null && class_exists('\Illuminate\Support\Facades\DB')) {
            try {
                $this->pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            } catch (\Throwable $t) {}
        }

        // Auto map legacy table queries to ref_* tables if ref_* table exists
        $refMappedQuery = preg_replace_callback('/\b(?:FROM|JOIN)\s+([a-zA-Z0-9_]+)\b/i', function ($matches) {
            $table = $matches[1];
            if (!str_starts_with($table, 'ref_') && !in_array($table, ['campaigns', 'characters', 'players', 'search_index', 'migrations'])) {
                return str_replace($table, "ref_$table", $matches[0]);
            }
            return $matches[0];
        }, $query);

        try {
            $stmt = $this->pdo->prepare($refMappedQuery);
            $stmt->execute($params);
            $this->lastStatement = $stmt;
            return new QueryResult($stmt);
        } catch (PDOException $e) {
            try {
                // Retry with original query
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($params);
                $this->lastStatement = $stmt;
                return new QueryResult($stmt);
            } catch (PDOException $e2) {
                throw new Exception("Query error: " . $e2->getMessage() . "\nQuery: " . $query);
            }
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
