<?php

/**
 * Sphider PDO Database Wrapper
 * Provides a simple interface to execute queries using PDO
 * Replaces old mysql_* function calls
 */
class SphiderDatabase {
    private static $instance = null;
    private $pdo = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setPDO($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Execute a query and return PDOStatement
     */
    public function query($sql) {
        if ($this->pdo === null) {
            throw new Exception("Database connection not initialized");
        }
        try {
            return $this->pdo->query($sql);
        } catch (PDOException $e) {
            throw new Exception("Query error: " . $e->getMessage());
        }
    }

    /**
     * Execute a prepared statement
     */
    public function prepare($sql) {
        if ($this->pdo === null) {
            throw new Exception("Database connection not initialized");
        }
        return $this->pdo->prepare($sql);
    }

    /**
     * Execute a statement directly
     */
    public function exec($sql) {
        if ($this->pdo === null) {
            throw new Exception("Database connection not initialized");
        }
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new Exception("Exec error: " . $e->getMessage());
        }
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        if ($this->pdo === null) {
            return 0;
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Legacy mysql_query wrapper
     * For queries like: mysql_query("SELECT ...")
     */
    public function mysql_query($query) {
        try {
            $result = $this->pdo->query($query);
            if ($result === false) {
                return false;
            }
            return new SphiderQueryResult($result);
        } catch (PDOException $e) {
            // Return false for compatibility with old code
            return false;
        }
    }

    /**
     * Legacy mysql_fetch_array wrapper
     */
    public function mysql_fetch_array($result) {
        if ($result instanceof SphiderQueryResult) {
            return $result->fetch_array();
        }
        return false;
    }

    /**
     * Legacy mysql_fetch_row wrapper
     */
    public function mysql_fetch_row($result) {
        if ($result instanceof SphiderQueryResult) {
            return $result->fetch_row();
        }
        return false;
    }

    /**
     * Legacy mysql_num_rows wrapper
     */
    public function mysql_num_rows($result) {
        if ($result instanceof SphiderQueryResult) {
            return $result->rowCount();
        }
        return 0;
    }

    /**
     * Legacy mysql_numrows wrapper (alias)
     */
    public function mysql_numrows($result) {
        return $this->mysql_num_rows($result);
    }

    /**
     * Legacy mysql_insert_id wrapper
     */
    public function mysql_insert_id() {
        return $this->lastInsertId();
    }
}

/**
 * Wrapper for PDOStatement results
 */
class SphiderQueryResult {
    private $statement = null;
    private $results = null;
    private $currentIndex = 0;

    public function __construct($statement) {
        $this->statement = $statement;
    }

    public function fetch_array() {
        return $this->statement->fetch(PDO::FETCH_BOTH);
    }

    public function fetch_row() {
        return $this->statement->fetch(PDO::FETCH_NUM);
    }

    public function fetch_assoc() {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }
}

// Initialize the singleton
$db = SphiderDatabase::getInstance();

// If we have a PDO connection from database.php, use it
if (isset($pdo)) {
    $db->setPDO($pdo);
}

?>
