<?php

/**
 * Simple and extensible validation library
 *
 * @license MIT
 * @package Sielatchom\Validator
 * @author  Sielatchom Daryl
 */

namespace Sielatchom\Validator;

use DateTime;
use Exception;

class Validator
{
    protected array $data;
    protected array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Main validation entry point
     * Applies validation rules to each field
     */
    public function validate(array $rules): self
    {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        return $this;
    }

    /**
     * Apply a single validation rule
     */
    protected function applyRule(string $field, string $rule): void
    {
        [$name, $params] = $this->parseRule($rule);

        if (!method_exists($this, $name)) {
            throw new Exception("Validation rule [$name] does not exist.");
        }

        $this->$name($field, ...$params);
    }

    /**
     * Parse rule definitions like "min:3" or "password:8,strong"
     */
    protected function parseRule(string $rule): array
    {
        $parts  = explode(':', $rule, 2);
        $name   = $parts[0];
        $params = $parts[1] ?? '';

        return [$name, $params !== '' ? explode(',', $params) : []];
    }

    /* ==========================================================
     * VALIDATION RULES
     * ========================================================== */

    /** Field must exist and not be empty */
    protected function required(string $field): void
    {
        if (!isset($this->data[$field]) || trim((string)$this->data[$field]) === '') {
            $this->errors[$field][] = "The {$field} field is required.";
        }
    }

    /** Field must be a valid email address */
    protected function email(string $field): void
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Invalid email format.";
        }
    }

    /** Validate password length and strength */
    protected function password(string $field, int $minLength = 8, string $strength = 'weak'): void
    {
        $value = $this->data[$field] ?? '';

        if ($value === '') return;

        if (strlen($value) < $minLength) {
            $this->errors[$field][] = "Password must be at least {$minLength} characters.";
        }

        if ($strength === 'strong') {
            foreach ([
                '/[a-z]/' => 'a lowercase letter',
                '/[A-Z]/' => 'an uppercase letter',
                '/[0-9]/' => 'a number',
                '/[^a-zA-Z0-9]/' => 'a special character',
            ] as $regex => $message) {
                if (!preg_match($regex, $value)) {
                    $this->errors[$field][] = "Password must contain {$message}.";
                }
            }
        }
    }

    /** Validate phone number digit length */
    protected function phone(string $field, int $min, int $max): void
    {
        $value = $this->data[$field] ?? '';

        if ($value === '') return;

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) < $min || strlen($digits) > $max) {
            $this->errors[$field][] = "Phone number must be between {$min} and {$max} digits.";
        }
    }

    /** Validate date format */
    protected function dateValidate(string $field, string $format = 'Y-m-d'): void
    {
        $value = $this->data[$field] ?? '';

        if ($value === '') return;

        $date = DateTime::createFromFormat($format, $value);

        if (!$date || $date->format($format) !== $value) {
            $this->errors[$field][] = "Invalid date format.";
        }
    }

    /** Ensure date is after a given date or today */
    protected function after(string $field, string $min): void
    {
        $value = $this->data[$field] ?? '';
        if ($value === '') return;

        if (new DateTime($value) < new DateTime($min === 'today' ? 'today' : $min)) {
            $this->errors[$field][] = "Date must be after {$min}.";
        }
    }

    /** Ensure date is before a given date */
    protected function before(string $field, string $max): void
    {
        $value = $this->data[$field] ?? '';
        if ($value === '') return;

        if (new DateTime($value) > new DateTime($max)) {
            $this->errors[$field][] = "Date must be before {$max}.";
        }
    }

    /** Field must be a string */
    protected function string(string $field): void
    {
        if (isset($this->data[$field]) && !is_string($this->data[$field])) {
            $this->errors[$field][] = "The {$field} must be a string.";
        }
    }

    /** Field must be numeric */
    protected function numeric(string $field): void
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = "The {$field} must be numeric.";
        }
    }

    /** Field must be an integer */
    protected function integer(string $field): void
    {
        if (isset($this->data[$field]) &&
            filter_var($this->data[$field], FILTER_VALIDATE_INT) === false) {
            $this->errors[$field][] = "The {$field} must be an integer.";
        }
    }

    /** Field must be boolean */
    protected function boolean(string $field): void
    {
        if (isset($this->data[$field]) &&
            !in_array($this->data[$field], [true, false, 1, 0, '1', '0'], true)) {
            $this->errors[$field][] = "The {$field} must be a boolean.";
        }
    }

    /** Minimum string length or numeric value */
    protected function min(string $field, int $min): void
    {
        $value = $this->data[$field] ?? '';
        if ((is_string($value) && strlen($value) < $min) ||
            (is_numeric($value) && $value < $min)) {
            $this->errors[$field][] = "Minimum value is {$min}.";
        }
    }

    /** Maximum string length or numeric value */
    protected function max(string $field, int $max): void
    {
        $value = $this->data[$field] ?? '';
        if ((is_string($value) && strlen($value) > $max) ||
            (is_numeric($value) && $value > $max)) {
            $this->errors[$field][] = "Maximum value is {$max}.";
        }
    }

    /** Value must be within a given range */
    protected function between(string $field, int $min, int $max): void
    {
        $value = $this->data[$field] ?? '';
        if ((is_numeric($value) && ($value < $min || $value > $max)) ||
            (is_string($value) && (strlen($value) < $min || strlen($value) > $max))) {
            $this->errors[$field][] = "Must be between {$min} and {$max}.";
        }
    }

    /** Value must be one of the allowed options */
    protected function in(string $field, ...$values): void
    {
        if (isset($this->data[$field]) &&
            !in_array($this->data[$field], $values, true)) {
            $this->errors[$field][] = "Invalid value selected.";
        }
    }

    /** Field must match confirmation field */
    protected function confirmed(string $field): void
    {
        if (($this->data[$field] ?? null) !== ($this->data[$field . '_confirmation'] ?? null)) {
            $this->errors[$field][] = "Confirmation does not match.";
        }
    }

    /** Validate value using regex pattern */
    protected function regex(string $field, string $pattern): void
    {
        if (isset($this->data[$field]) &&
            !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field][] = "Invalid format.";
        }
    }

    /** Validate URL format */
    protected function url(string $field): void
    {
        if (isset($this->data[$field]) &&
            !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field][] = "Invalid URL.";
        }
    }

    /** Validate JSON string */
    protected function json(string $field): void
    {
        if (isset($this->data[$field])) {
            json_decode($this->data[$field]);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[$field][] = "Invalid JSON string.";
            }
        }
    }

    /* ==========================================================
     * RESULT METHODS
     * ========================================================== */

    /** Check if validation failed */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /** Retrieve validation errors */
    public function errors(): array
    {
        return $this->errors;
    }
}
