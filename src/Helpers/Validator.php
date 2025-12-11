<?php

class Validator {
    
    /**
     * Validate numeric value
     * @param mixed $value The value to validate
     * @param string $fieldName Field name for error messages
     * @return float The validated numeric value
     * @throws Exception if validation fails
     */
    public static function validateNumeric($value, $fieldName) {
        if (!is_numeric($value)) {
            throw new Exception("$fieldName harus berupa angka.");
        }
        return floatval($value);
    }
    
    /**
     * Validate required field
     * @param mixed $value The value to validate
     * @param string $fieldName Field name for error messages
     * @return mixed The validated value
     * @throws Exception if validation fails
     */
    public static function validateRequired($value, $fieldName) {
        if (empty($value) && $value !== '0') {
            throw new Exception("$fieldName wajib diisi.");
        }
        return $value;
    }
    
    /**
     * Validate integer value
     * @param mixed $value The value to validate
     * @param string $fieldName Field name for error messages
     * @return int The validated integer value
     * @throws Exception if validation fails
     */
    public static function validateInteger($value, $fieldName) {
        if (!filter_var($value, FILTER_VALIDATE_INT) && $value !== 0 && $value !== '0') {
            throw new Exception("$fieldName harus berupa bilangan bulat.");
        }
        return intval($value);
    }
    
    /**
     * Sanitize HTML output to prevent XSS
     * @param string $text The text to sanitize
     * @return string Sanitized text
     */
    public static function sanitizeOutput($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize string input
     * @param string $text The text to sanitize
     * @return string Sanitized text
     */
    public static function sanitizeString($text) {
        return trim(strip_tags($text));
    }
    
    /**
     * Validate field name for dynamic queries (whitelist approach)
     * @param string $fieldName The field name to validate
     * @param array $allowedFields Array of allowed field names
     * @return string The validated field name
     * @throws Exception if field is not in whitelist
     */
    public static function validateFieldName($fieldName, $allowedFields) {
        if (!in_array($fieldName, $allowedFields)) {
            throw new Exception("Invalid field name.");
        }
        return $fieldName;
    }
    
    /**
     * Validate array of required POST fields
     * @param array $fields Array of field names to validate
     * @throws Exception if any required field is missing
     */
    public static function validateRequiredFields($fields) {
        foreach ($fields as $field) {
            if (!isset($_POST[$field]) || (empty($_POST[$field]) && $_POST[$field] !== '0')) {
                throw new Exception("Field '$field' wajib diisi.");
            }
        }
    }
}
