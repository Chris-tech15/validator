# Sielatchom\Validator
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Packagist Version](https://img.shields.io/packagist/v/sielatchom/validator)
![Downloads](https://img.shields.io/packagist/dt/sielatchom/validator)

**A simple, extensible, and developer-friendly PHP validation library**  
Validate forms and data easily with Laravel-style rules. Supports strings, numbers, dates, emails, passwords, phone numbers, URLs, JSON, and more.




## Installation

Install via Composer:

```bash
composer require sielatchom/validator
```

## Description

`Sielatchom\Validator` provides a **simple API to validate PHP arrays**, allowing developers to check multiple fields and rules with a **single call**.  
It is **PSR-4 compliant**, **unit-tested**, and **extensible**

## Usage
### Basic Examples

```
<?php
require 'vendor/autoload.php';

use Sielatchom\Validator\Validator;

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'Abc123$%',
    'password_confirmation' => 'Abc123$%',
    'age' => 25,
    'phone' => '237655123456',
    'website' => 'https://example.com',
    'payload' => '{"role":"admin"}',
    'dob' => '1990-01-01'
];

$validator = new Validator($data);

$validator->validate([
    'name'     => ['required', 'string', 'min:3', 'max:50'],
    'email'    => ['required', 'email'],
    'password' => ['required', 'password:8,strong', 'confirmed'],
    'age'      => ['integer', 'between:18,65'],
    'phone'    => ['phone:9,13'],
    'website'  => ['url'],
    'payload'  => ['json'],
    'dob'      => ['dateValidate', 'after:1900-01-01', 'before:today']
]);

if ($validator->fails()) {
    print_r($validator->errors());
} else {
    echo "All data is valid!";
}
```

#
# Validation Rules Reference

| Rule | Description |
| :--- | :--- |
| **required** | Field must exist and not be empty |
| **email** | Must be a valid email address |
| **password:min,strength** | Minimum length and optional strong password check (uppercase, lowercase, number, special) |
| **phone:min,max** | Phone number digits range |
| **date** | Validate date format (default Y-m-d) |
| **after:min** | Date must be after a specific date or today |
| **before:max** | Date must be before a specific date |
| **string** | Field must be a string |
| **numeric** | Field must be numeric |
| **integer** | Field must be an integer |
| **boolean** | Field must be boolean (true/false/1/0) |
| **min:value** | Minimum string length or numeric value |
| **max:value** | Maximum string length or numeric value |
| **between:min,max** | Value must be within a given range |
| **in:val1,val2,...** | Value must be one of the allowed options |
| **confirmed** | Field must match its _confirmation field |
| **regex:/pattern/** | Field must match the given regex pattern |
| **url** | Must be a valid URL |
| **json** | Must be a valid JSON string |

## About the Author

**Sielatchom Jeukeng Chrisaire Daryl**
-   Email: sielatchomchrisaire@gmail.com
-   Location: Yaoundé, Cameroon   
-   GitHub: https://github.com/Chris-tech15

## Lisense
This library is licensed under the **MIT License**.