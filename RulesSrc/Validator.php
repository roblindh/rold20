<?php

namespace RulesSrc;

class ValidationException extends \Exception
{
    private string $fieldName = '';
    private mixed $value = null;

    public function __construct(string $message, string $fieldName = '', mixed $value = null)
    {
        $this->fieldName = $fieldName;
        $this->value = $value;

        parent::__construct($message);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}

class Validator
{
    /**
     * Validate an integer value with optional min/max constraints
     */
    public static function integer(mixed $value, ?int $min = null, ?int $max = null, string $fieldName = ''): int
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (is_string($value)) {
            if (!is_numeric($value)) {
                throw new ValidationException(
                    'Value must be a valid number',
                    $fieldName,
                    $value
                );
            }
            $value = (int) $value;
        } elseif (is_float($value)) {
            $value = (int) $value;
        } elseif (!is_int($value)) {
            throw new ValidationException(
                'Value must be an integer',
                $fieldName,
                $value
            );
        }

        if ($min !== null && $value < $min) {
            throw new ValidationException(
                "Value must be at least {$min}",
                $fieldName,
                $value
            );
        }

        if ($max !== null && $value > $max) {
            throw new ValidationException(
                "Value must be no more than {$max}",
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate a string value with optional length constraints
     */
    public static function string(mixed $value, ?int $minLen = null, ?int $maxLen = null, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            if (is_numeric($value)) {
                $value = (string) $value;
            } else {
                throw new ValidationException(
                    'Value must be a string',
                    $fieldName,
                    $value
                );
            }
        }

        // Sanitize: trim whitespace and remove null bytes
        $value = trim($value);
        $value = str_replace("\0", '', $value);

        // Ensure proper UTF-8 encoding
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        $length = mb_strlen($value, 'UTF-8');

        if ($minLen !== null && $length < $minLen) {
            throw new ValidationException(
                "String must be at least {$minLen} characters",
                $fieldName,
                $value
            );
        }

        if ($maxLen !== null && $length > $maxLen) {
            throw new ValidationException(
                "String must be no more than {$maxLen} characters",
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate an email address
     */
    public static function email(mixed $value, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'Email must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            throw new ValidationException(
                'Email must be a string',
                $fieldName,
                $value
            );
        }

        // Sanitize: lowercase and trim
        $value = strtolower(trim($value));

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(
                'Invalid email format',
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate a boolean value
     */
    public static function boolean(mixed $value, string $fieldName = ''): bool
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $lowered = strtolower(trim($value));
            if (in_array($lowered, ['true', '1', 'yes', 'on'], true)) {
                return true;
            }
            if (in_array($lowered, ['false', '0', 'no', 'off'], true)) {
                return false;
            }
        }

        throw new ValidationException(
            'Value must be a boolean',
            $fieldName,
            $value
        );
    }

    /**
     * Validate a float value with optional min/max constraints
     */
    public static function float(mixed $value, ?float $min = null, ?float $max = null, string $fieldName = ''): float
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (is_string($value)) {
            if (!is_numeric($value)) {
                throw new ValidationException(
                    'Value must be a valid number',
                    $fieldName,
                    $value
                );
            }
            $value = (float) $value;
        } elseif (is_int($value)) {
            $value = (float) $value;
        } elseif (!is_float($value)) {
            throw new ValidationException(
                'Value must be a float',
                $fieldName,
                $value
            );
        }

        if ($min !== null && $value < $min) {
            throw new ValidationException(
                "Value must be at least {$min}",
                $fieldName,
                $value
            );
        }

        if ($max !== null && $value > $max) {
            throw new ValidationException(
                "Value must be no more than {$max}",
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate an array of integers
     */
    public static function arrayOfInts(mixed $value, ?int $minCount = null, string $fieldName = ''): array
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_array($value)) {
            throw new ValidationException(
                'Value must be an array',
                $fieldName,
                $value
            );
        }

        if ($minCount !== null && count($value) < $minCount) {
            throw new ValidationException(
                "Array must contain at least {$minCount} elements",
                $fieldName,
                $value
            );
        }

        $result = [];
        foreach ($value as $item) {
            try {
                $result[] = self::integer($item, fieldName: $fieldName);
            } catch (ValidationException $e) {
                throw new ValidationException(
                    "All array elements must be valid integers",
                    $fieldName,
                    $value
                );
            }
        }

        return $result;
    }

    /**
     * Validate an enum value (must be one of allowed values)
     */
    public static function enum(mixed $value, array $allowedValues, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            throw new ValidationException(
                'Value must be a string',
                $fieldName,
                $value
            );
        }

        $value = trim($value);

        if (!in_array($value, $allowedValues, true)) {
            throw new ValidationException(
                'Value must be one of the allowed options',
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate a URL
     */
    public static function url(mixed $value, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'URL must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            throw new ValidationException(
                'URL must be a string',
                $fieldName,
                $value
            );
        }

        $value = trim($value);

        // Only allow http and https
        if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $value)) {
            throw new ValidationException(
                'Invalid URL format. Only http and https protocols are allowed',
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Validate alphanumeric value, optionally allowing spaces
     */
    public static function alphanumeric(mixed $value, bool $allowSpaces = false, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            if (is_numeric($value)) {
                $value = (string) $value;
            } else {
                throw new ValidationException(
                    'Value must be a string',
                    $fieldName,
                    $value
                );
            }
        }

        $value = trim($value);

        if ($allowSpaces) {
            if (!preg_match('/^[a-zA-Z0-9\s]*$/', $value)) {
                throw new ValidationException(
                    'Value must contain only letters, numbers, and spaces',
                    $fieldName,
                    $value
                );
            }
        } else {
            if (!preg_match('/^[a-zA-Z0-9]*$/', $value)) {
                throw new ValidationException(
                    'Value must contain only letters and numbers',
                    $fieldName,
                    $value
                );
            }
        }

        return $value;
    }

    /**
     * Validate a slug (lowercase alphanumeric with hyphens and underscores)
     */
    public static function slug(mixed $value, string $fieldName = ''): string
    {
        if ($value === null) {
            throw new ValidationException(
                'Value must not be null',
                $fieldName,
                $value
            );
        }

        if (!is_string($value)) {
            throw new ValidationException(
                'Value must be a string',
                $fieldName,
                $value
            );
        }

        $value = trim($value);
        $value = strtolower($value);

        if (!preg_match('/^[a-z0-9_-]*$/', $value)) {
            throw new ValidationException(
                'Value must contain only lowercase letters, numbers, hyphens, and underscores',
                $fieldName,
                $value
            );
        }

        return $value;
    }

    /**
     * Check if a value is required (not null or empty string)
     */
    public static function isRequired(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }

    /**
     * Validate a value from $_GET array
     */
    public static function fromGET(string $key, string $type, array $options = []): mixed
    {
        if (!isset($_GET[$key])) {
            throw new ValidationException(
                "Required parameter '{$key}' not provided",
                $key
            );
        }

        return self::validateByType($_GET[$key], $type, $key, $options);
    }

    /**
     * Validate a value from $_POST array
     */
    public static function fromPOST(string $key, string $type, array $options = []): mixed
    {
        if (!isset($_POST[$key])) {
            throw new ValidationException(
                "Required parameter '{$key}' not provided",
                $key
            );
        }

        return self::validateByType($_POST[$key], $type, $key, $options);
    }

    /**
     * Validate a value from an arbitrary array
     */
    public static function fromArray(array $data, string $key, string $type, array $options = []): mixed
    {
        if (!isset($data[$key])) {
            throw new ValidationException(
                "Required parameter '{$key}' not provided",
                $key
            );
        }

        return self::validateByType($data[$key], $type, $key, $options);
    }

    /**
     * Internal helper to validate by type string
     */
    private static function validateByType(mixed $value, string $type, string $fieldName = '', array $options = []): mixed
    {
        return match ($type) {
            'integer', 'int' => self::integer(
                $value,
                $options['min'] ?? null,
                $options['max'] ?? null,
                $fieldName
            ),
            'string' => self::string(
                $value,
                $options['minLen'] ?? null,
                $options['maxLen'] ?? null,
                $fieldName
            ),
            'email' => self::email($value, $fieldName),
            'boolean', 'bool' => self::boolean($value, $fieldName),
            'float', 'double' => self::float(
                $value,
                $options['min'] ?? null,
                $options['max'] ?? null,
                $fieldName
            ),
            'arrayOfInts', 'array_of_ints' => self::arrayOfInts(
                $value,
                $options['minCount'] ?? null,
                $fieldName
            ),
            'enum' => self::enum($value, $options['allowedValues'] ?? [], $fieldName),
            'url' => self::url($value, $fieldName),
            'alphanumeric' => self::alphanumeric(
                $value,
                $options['allowSpaces'] ?? false,
                $fieldName
            ),
            'slug' => self::slug($value, $fieldName),
            default => throw new ValidationException(
                "Unknown validation type: {$type}",
                $fieldName
            ),
        };
    }
}
