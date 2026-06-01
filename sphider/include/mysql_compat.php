<?php

/**
 * Compatibility layer for old mysql_* functions
 * Maps old function calls to PDO-based equivalents
 */

$_global_db_result = null;

/**
 * Replacement for mysql_query
 */
function mysql_query($query) {
    global $_global_db_result, $db, $pdo;
    try {
        // Try to use the SphiderDatabase wrapper if available
        if (class_exists('SphiderDatabase')) {
            $db = SphiderDatabase::getInstance();
            $_global_db_result = $db->mysql_query($query);
            return $_global_db_result;
        }
        // Fallback to PDO directly
        if ($pdo !== null) {
            $_global_db_result = $pdo->query($query);
            return $_global_db_result;
        }
    } catch (Exception $e) {
        // Silently return false for compatibility
    }
    return false;
}

/**
 * Replacement for mysql_fetch_array
 */
function mysql_fetch_array($result) {
    if ($result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_BOTH);
    }
    if (is_object($result) && method_exists($result, 'fetch_array')) {
        return $result->fetch_array();
    }
    return false;
}

/**
 * Replacement for mysql_fetch_row
 */
function mysql_fetch_row($result) {
    if ($result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_NUM);
    }
    if (is_object($result) && method_exists($result, 'fetch_row')) {
        return $result->fetch_row();
    }
    return false;
}

/**
 * Replacement for mysql_fetch_assoc
 */
function mysql_fetch_assoc($result) {
    if ($result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    if (is_object($result) && method_exists($result, 'fetch_assoc')) {
        return $result->fetch_assoc();
    }
    return false;
}

/**
 * Replacement for mysql_num_rows
 */
function mysql_num_rows($result) {
    if ($result instanceof PDOStatement) {
        return $result->rowCount();
    }
    if (is_object($result) && method_exists($result, 'rowCount')) {
        return $result->rowCount();
    }
    return 0;
}

/**
 * Replacement for mysql_numrows (alias)
 */
function mysql_numrows($result) {
    return mysql_num_rows($result);
}

/**
 * Replacement for mysql_error
 */
function mysql_error() {
    return ""; // Return empty string for compatibility
}

/**
 * Replacement for mysql_errno
 */
function mysql_errno() {
    return 0; // Return 0 for compatibility
}

/**
 * Replacement for mysql_insert_id
 */
function mysql_insert_id($conn = null) {
    global $pdo;
    if ($pdo !== null) {
        return $pdo->lastInsertId();
    }
    return 0;
}

/**
 * Replacement for mysql_db_query (deprecated)
 */
function mysql_db_query($database_name, $query, $link_identifier = null) {
    return mysql_query($query);
}

?>
