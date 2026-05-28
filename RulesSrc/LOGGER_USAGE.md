# Logger.php - Centralized Error Logging System

## Overview

Logger.php provides a production-ready centralized logging and error handling system for the RoL d20 PHP application. It implements the singleton pattern and captures full error context including timestamps, file locations, line numbers, and function names.

## Features

- **Singleton Pattern**: Access from anywhere with `Logger::info()`, `Logger::error()`, etc.
- **Production-Safe Error Handling**: Hides detailed errors from users in production mode
- **Full Context Capture**: Automatically captures timestamp, file, line, function, URL/page
- **Daily Log Rotation**: One log file per day (format: `logs/2026-05-25.log`)
- **Thread-Safe**: Uses file locking for concurrent access
- **Fallback Mechanism**: Falls back to `error_log()` if file write fails
- **Four Log Levels**: INFO, WARNING, ERROR, CRITICAL

## Installation

Logger is automatically integrated into the application via `RulesSrc/global.php`:

1. Logger class is required at the top of global.php
2. Global error handler is registered via `set_error_handler('handleApplicationError')`
3. Production mode is detected from environment variables

## Usage

### Basic Logging

```php
// Info level
Logger::info('User logged in', ['userId' => 123]);

// Warning level
Logger::warning('High memory usage detected', ['memoryMB' => 512]);

// Error level
Logger::error('Database query failed', ['query' => $sql, 'errno' => $errno]);

// Critical level
Logger::critical('Application cannot start', ['reason' => 'Database unavailable']);
```

### Production-Safe Error Display

```php
// Logs full error message and shows generic message to user in production
Logger::displayError(
    'Failed to connect to payment gateway: ' . $e->getMessage(),
    'Payment processing is temporarily unavailable. Please try again later.'
);
```

### Setting Production Mode

Production mode is automatically detected from environment variables:
- `ENVIRONMENT=production`
- `PRODUCTION=true`

Manual override:
```php
Logger::setProductionMode(true);  // Hide errors from users
Logger::setProductionMode(false); // Show full errors (development)
```

### Checking Mode

```php
if (Logger::isProductionMode()) {
    // In production
}
```

### Getting Log Directory

```php
$logsDir = Logger::getLogsDirectory();
// Returns: /path/to/project/root/logs
```

## Log File Format

Each log entry follows this format:

```
[2026-05-25T14:26:51+02:00] ERROR [entity.php:1276] [ProcessTraits] - Message here | context: {"key":"value"}
```

Breaking down the format:
- `[2026-05-25T14:26:51+02:00]` - ISO 8601 timestamp with timezone
- `ERROR` - Log level (INFO, WARNING, ERROR, CRITICAL)
- `[entity.php:1276]` - Caller filename and line number
- `[ProcessTraits]` - Function or method name
- `Message here` - Log message
- `| context: {...}` - Optional additional context as JSON

### Example Log Files

```
[2026-05-25T08:30:15+00:00] INFO [index.php:42] [application_start] - Application started
[2026-05-25T08:30:16+00:00] INFO [index.php:43] [application_start] - Database connected | context: {"host":"localhost","database":"rold20"}
[2026-05-25T08:35:20+00:00] WARNING [chargen.php:156] [generateCharacter] - Ability score generation took longer than expected | context: {"milliseconds":2543}
[2026-05-25T08:40:30+00:00] ERROR [Database.php:70] [query] - Query execution failed | context: {"errno":1054,"query":"SELECT * FROM invalid_table"}
[2026-05-25T08:45:45+00:00] CRITICAL [entity.php:1276] [ProcessTraits] - Fatal error in trait processing | context: {"traitId":"abc123","error":"Undefined array key"}
```

## Integration Points

### Database.php Integration

Wrap Database operations in try-catch and log errors:

```php
try {
    $result = $db->query($sql, $params);
    Logger::info('Query executed successfully', ['affectedRows' => $db->numRows()]);
} catch (Exception $e) {
    Logger::error('Database query failed', [
        'query' => substr($sql, 0, 100),
        'error' => $e->getMessage()
    ]);
    Logger::displayError(
        'Database error: ' . $e->getMessage(),
        'Failed to retrieve data. Please try again.'
    );
}
```

### Error Handler Integration

PHP errors are automatically caught by the global error handler registered in global.php:

```php
// These are automatically logged by handleApplicationError():
trigger_error("Custom warning", E_USER_WARNING);
// Logged as: [timestamp] WARNING [...] - User Warning: Custom warning

// Division by zero
$x = 1 / 0;  // Logs to: [timestamp] WARNING [...] - Warning: Division by zero
```

### Custom Exception Logging

```php
try {
    // Some operation
} catch (Exception $e) {
    Logger::error('Operation failed', [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    Logger::displayError($e->getMessage());
}
```

## Environment Configuration

Production vs Development mode is determined by environment variables:

### Production Environment
```bash
export ENVIRONMENT=production
# or
export PRODUCTION=true
```

When in production mode:
- Logger::displayError() shows generic "An error occurred..." message
- Full error details are only in logs (logs/YYYY-MM-DD.log)
- Users never see sensitive information

### Development Environment
- Logger::displayError() shows full error message to aid debugging
- Useful for rapid development and testing

## Log File Location

All log files are stored in the `logs/` directory at the project root:

```
/path/to/project/
├── RulesSrc/
│   ├── Logger.php
│   └── global.php
├── logs/
│   ├── 2026-05-25.log
│   ├── 2026-05-26.log
│   └── 2026-05-27.log
└── .gitignore (includes "logs/")
```

The `logs/` directory is automatically created on first use. Log files are NOT committed to git (see .gitignore).

## Thread Safety

Logger uses file locking (flock) to safely handle concurrent writes:

1. Non-blocking lock attempt first (LOCK_EX | LOCK_NB)
2. Falls back to blocking lock if non-blocking fails
3. Holds lock only for the write operation (minimal duration)
4. Releases lock after write
5. Error log fallback if lock fails

This ensures log integrity even with multiple concurrent requests.

## Error Levels Mapping

PHP error types are automatically mapped to Logger levels:

```
E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR  → CRITICAL
E_USER_ERROR, E_RECOVERABLE_ERROR                → ERROR
E_WARNING, E_NOTICE, E_CORE_WARNING, E_COMPILE_WARNING, 
E_USER_WARNING, E_USER_NOTICE, E_STRICT, 
E_DEPRECATED, E_USER_DEPRECATED                  → WARNING
```

## API Reference

### Static Methods

#### getInstance(): Logger
Get or create the singleton instance.

#### info(string $message, array $context = []): void
Log an info-level message.

#### warning(string $message, array $context = []): void
Log a warning-level message.

#### error(string $message, array $context = []): void
Log an error-level message.

#### critical(string $message, array $context = []): void
Log a critical-level message.

#### setProductionMode(bool $production = true): void
Set production mode (hides errors from users).

#### displayError(string $message, ?string $userMessage = null): void
Display error to user with production-safe message.

#### getLogsDirectory(): string
Get the logs directory path.

#### isProductionMode(): bool
Check if production mode is enabled.

## Fallback Mechanism

If Logger cannot write to a file:
1. Logs "[Logger] Failed to open log file" to error_log()
2. Does not throw exception (prevents cascading failures)
3. Continues application execution
4. Uses error_log() for temporary storage

## Performance Considerations

- **Minimal Overhead**: Uses debug_backtrace() once per log entry
- **File I/O**: One file write per log entry (append mode)
- **Lock Duration**: Lock held only for write, minimal impact
- **No Buffering**: Synchronous writes ensure no log loss
- **Daily Rotation**: Automatic via date-based filename (no maintenance needed)

## Security Considerations

- **No Secrets**: Be careful not to log sensitive data (passwords, API keys)
- **HTML Escaping**: Error display uses htmlspecialchars() for XSS protection
- **File Permissions**: Logs directory created with 0755 permissions
- **Production Safety**: Full errors never shown to users in production mode

## Testing

To verify Logger functionality:

1. Check logs directory exists:
   ```bash
   ls -la logs/
   ```

2. Verify today's log file:
   ```bash
   ls -la logs/$(date +%Y-%m-%d).log
   ```

3. View recent log entries:
   ```bash
   tail -20 logs/$(date +%Y-%m-%d).log
   ```

## Troubleshooting

### Logs not being created
- Check if logs/ directory exists and is writable
- Verify file permissions: `chmod 755 logs/`
- Check error_log() output for write failures

### Missing function name in logs
- Some contexts may have limited backtrace information
- Check that error handler is properly registered

### Logs directory in git
- Ensure logs/ is added to .gitignore
- Use `git rm --cached logs/` if accidentally committed

## Future Enhancements

Potential improvements for future versions:
- Log rotation by size (not just date)
- Log filtering and searching tools
- Structured logging (JSON format option)
- Email alerts for critical errors
- Remote logging support
- Compression of old log files
