<?php

/**
 * Centralized Logger for the RoL d20 Application
 * 
 * Provides production-ready error logging and handling with:
 * - Singleton pattern for global access
 * - Development and production modes
 * - Full error context capture (timestamp, file, line, function, URL)
 * - Thread-safe file logging with daily rotation
 * - Fallback to error_log() if file write fails
 * 
 * Usage:
 *   Logger::info('User logged in', ['userId' => 123]);
 *   Logger::error('Database connection failed', ['host' => 'localhost']);
 *   Logger::setProductionMode(true);
 */
class Logger
{
    private static $instance = null;
    private static $productionMode = false;
    private $logDir = null;
    private $handle = null;

    /**
     * Private constructor - use getInstance() instead
     */
    private function __construct()
    {
        $this->logDir = $this->getLogDirectory();
        $this->ensureLogDirectory();
    }

    /**
     * Get or create singleton instance
     * 
     * @return Logger
     */
    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set production mode (hides detailed errors from users)
     * 
     * @param bool $production
     * @return void
     */
    public static function setProductionMode(bool $production = true): void
    {
        self::$productionMode = $production;
    }

    /**
     * Log info level message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->write('INFO', $message, $context);
    }

    /**
     * Log warning level message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->write('WARNING', $message, $context);
    }

    /**
     * Log error level message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->write('ERROR', $message, $context);
    }

    /**
     * Log critical level message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical(string $message, array $context = []): void
    {
        self::getInstance()->write('CRITICAL', $message, $context);
    }

    /**
     * Display error to user (production-safe)
     * 
     * In production mode, shows generic message and logs full details.
     * In development mode, shows full error details.
     * 
     * @param string $message Full error message (always logged)
     * @param string|null $userMessage Custom user-facing message (optional)
     * @return void
     */
    public static function displayError(string $message, ?string $userMessage = null): void
    {
        self::getInstance()->write('ERROR', $message, ['type' => 'user_error']);

        if (self::$productionMode) {
            $display = $userMessage ?? "An error occurred. Please try again or contact support.";
        } else {
            $display = $message;
        }

        echo "<div style='color: #d32f2f; padding: 20px; margin: 20px 0; border: 1px solid #d32f2f; border-radius: 4px;'>";
        echo htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
        echo "</div>";
    }

    /**
     * Internal method to write log entry
     * 
     * @param string $level Log level (INFO, WARNING, ERROR, CRITICAL)
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    private function write(string $level, string $message, array $context = []): void
    {
        try {
            $entry = $this->formatLogEntry($level, $message, $context);
            $this->writeToFile($entry);
        } catch (Throwable $e) {
            // Fallback to error_log if file write fails
            error_log("[Logger Fallback] {$level}: {$message}");
        }
    }

    /**
     * Format log entry with context
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    private function formatLogEntry(string $level, string $message, array $context = []): string
    {
        $timestamp = $this->getFormattedTimestamp();
        $file = $this->getCallerFile();
        $line = $this->getCallerLine();
        $function = $this->getCallerFunction();
        $page = $this->getCurrentPage();

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' | context: ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return sprintf(
            "[%s] %s [%s:%d] [%s] - %s%s",
            $timestamp,
            $level,
            $file,
            $line,
            $function,
            $message,
            $contextStr
        );
    }

    /**
     * Write entry to log file
     * 
     * @param string $entry
     * @return void
     */
    private function writeToFile(string $entry): void
    {
        $logFile = $this->getLogFilePath();

        $handle = @fopen($logFile, 'a');
        if ($handle === false) {
            error_log("[Logger] Failed to open log file: {$logFile}");
            return;
        }

        // Attempt file lock for thread safety
        $locked = @flock($handle, LOCK_EX | LOCK_NB);
        if (!$locked) {
            // If non-blocking lock fails, try blocking lock
            @flock($handle, LOCK_EX);
        }

        @fwrite($handle, $entry . PHP_EOL);
        @flock($handle, LOCK_UN);
        @fclose($handle);
    }

    /**
     * Get formatted ISO 8601 timestamp with timezone
     * 
     * @return string
     */
    private function getFormattedTimestamp(): string
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        return $dt->format('Y-m-d\TH:i:sP');
    }

    /**
     * Get the caller's filename from debug backtrace
     * 
     * @return string
     */
    private function getCallerFile(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        // Skip Logger methods to find actual caller
        foreach ($trace as $frame) {
            if (isset($frame['file']) && 
                strpos($frame['file'], 'Logger.php') === false) {
                return basename($frame['file']);
            }
        }
        
        return 'unknown';
    }

    /**
     * Get the caller's line number from debug backtrace
     * 
     * @return int
     */
    private function getCallerLine(): int
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        foreach ($trace as $frame) {
            if (isset($frame['file']) && 
                strpos($frame['file'], 'Logger.php') === false) {
                return $frame['line'] ?? 0;
            }
        }
        
        return 0;
    }

    /**
     * Get the caller's function/method name from debug backtrace
     * 
     * @return string
     */
    private function getCallerFunction(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        // Find first non-Logger frame
        for ($i = 0; $i < count($trace); $i++) {
            if (!isset($trace[$i]['file']) || strpos($trace[$i]['file'], 'Logger.php') === false) {
                // Look at next frame for function name
                if (isset($trace[$i + 1])) {
                    $func = $trace[$i + 1]['function'] ?? 'unknown';
                    if (isset($trace[$i + 1]['class'])) {
                        return $trace[$i + 1]['class'] . '::' . $func;
                    }
                    return $func;
                }
            }
        }
        
        return 'unknown';
    }

    /**
     * Get current page/URL being accessed
     * 
     * @return string
     */
    private function getCurrentPage(): string
    {
        if (php_sapi_name() === 'cli') {
            return 'CLI';
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? $_SERVER['REQUEST_URI'];
        }

        if (isset($_SERVER['PHP_SELF'])) {
            return $_SERVER['PHP_SELF'];
        }

        return 'unknown';
    }

    /**
     * Get the logs directory path (project root/logs/)
     * 
     * @return string
     */
    private function getLogDirectory(): string
    {
        // Determine project root - go up from RulesSrc
        $rulesDir = dirname(__FILE__);
        $projectRoot = dirname($rulesDir);
        return $projectRoot . DIRECTORY_SEPARATOR . 'logs';
    }

    /**
     * Ensure logs directory exists
     * 
     * @return void
     */
    private function ensureLogDirectory(): void
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
    }

    /**
     * Get today's log file path
     * 
     * @return string
     */
    private function getLogFilePath(): string
    {
        $today = date('Y-m-d');
        return $this->logDir . DIRECTORY_SEPARATOR . $today . '.log';
    }

    /**
     * Get log directory path
     * 
     * @return string
     */
    public static function getLogsDirectory(): string
    {
        return self::getInstance()->logDir;
    }

    /**
     * Check if production mode is enabled
     * 
     * @return bool
     */
    public static function isProductionMode(): bool
    {
        return self::$productionMode;
    }
}

/**
 * Global error handler function
 * 
 * Registers with set_error_handler() to catch all PHP errors.
 * Routes them to Logger for centralized handling.
 * 
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return bool
 */
function handleApplicationError(int $errno, string $errstr, string $errfile, int $errline): bool
{
    // Map PHP error codes to log levels
    $levelMap = [
        E_ERROR => 'CRITICAL',
        E_WARNING => 'WARNING',
        E_PARSE => 'CRITICAL',
        E_NOTICE => 'WARNING',
        E_CORE_ERROR => 'CRITICAL',
        E_CORE_WARNING => 'WARNING',
        E_COMPILE_ERROR => 'CRITICAL',
        E_COMPILE_WARNING => 'WARNING',
        E_USER_ERROR => 'ERROR',
        E_USER_WARNING => 'WARNING',
        E_USER_NOTICE => 'WARNING',
        2048 => 'WARNING', // E_STRICT
        E_RECOVERABLE_ERROR => 'ERROR',
        E_DEPRECATED => 'WARNING',
        E_USER_DEPRECATED => 'WARNING',
    ];

    $level = $levelMap[$errno] ?? 'WARNING';
    $errorType = getErrorName($errno);

    $methodName = 'log' . ucfirst(strtolower($level));
    $methodMap = [
        'CRITICAL' => 'critical',
        'ERROR' => 'error',
        'WARNING' => 'warning',
        'INFO' => 'info',
    ];
    
    $method = $methodMap[$level] ?? 'error';
    
    Logger::$method(
        "{$errorType}: {$errstr}",
        [
            'file' => basename($errfile),
            'line' => $errline,
            'errno' => $errno
        ]
    );

    // Return false to let PHP's default error handler also run
    return false;
}

/**
 * Get human-readable error type name
 * 
 * @param int $errno
 * @return string
 */
function getErrorName(int $errno): string
{
    $errors = [
        E_ERROR => 'Fatal Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        2048 => 'Strict Standards', // E_STRICT
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated',
    ];

    return $errors[$errno] ?? 'Unknown Error';
}
