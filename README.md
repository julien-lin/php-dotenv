# PHP Dotenv

[🇫🇷 Read in French](README.fr.md) | [🇬🇧 Read in English](README.md)

---

A simple and modern PHP library for loading environment variables from a `.env` file.

## 🚀 Installation

```bash
composer require julienlinard/php-dotenv
```

**Requirements**: PHP 8.0 or higher

## ⚡ Quick Start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Load the .env file from the root directory
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Access variables
echo $_ENV['DB_HOST'];
echo $_ENV['DB_NAME'];
```

## 📋 Features

- ✅ `.env` file loading
- ✅ Comment support (lines starting with `#`)
- ✅ Support for single and double quoted values
- ✅ Multi-line value support
- ✅ Variable expansion (`${VAR}` or `$VAR`)
- ✅ Immutable mode (does not replace existing variables)
- ✅ Required variable validation
- ✅ Boolean and null value support

## 📖 Usage

### Basic Loading

```php
use JulienLinard\Dotenv\Dotenv;

// Create an immutable instance (does not replace existing variables)
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

### Mutable Loading

```php
// Create a mutable instance (replaces existing variables)
$dotenv = Dotenv::createMutable(__DIR__);
$dotenv->load();
```

### Required Variable Validation

```php
use JulienLinard\Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Validate that certain variables exist
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
```

### Validation with Default Values

```php
// Validate with default values
$dotenv->required(['DB_PORT'])->notEmpty()->defaultTo('3306');
```

### Direct Variable Retrieval

```php
// Get a variable with default value
$dbHost = Dotenv::get('DB_HOST', 'localhost');
```

## 📝 .env File Format

```env
# Comment
DB_HOST=localhost
DB_NAME=mydatabase
DB_USER=root
DB_PASS=password123

# Quoted values
APP_NAME="My Application"
APP_URL='https://example.com'

# Boolean values
DEBUG=true
MAINTENANCE=false

# Null value
CACHE_DRIVER=null

# Variable expansion
APP_URL=https://example.com
API_URL=${APP_URL}/api

# Multi-line values (with quotes)
DESCRIPTION="This is a description
on multiple lines"
```

## 🔒 Security

- Variables are loaded into `$_ENV` and `$_SERVER`
- Immutable mode by default (does not replace existing system variables)
- Variable name validation (alphanumeric characters and underscores only)

## 🔗 Integration with Other Packages

### Integration with core-php

`core-php` automatically includes `php-dotenv`. Use `loadEnv()` to load variables.

```php
<?php

use JulienLinard\Core\Application;

$app = Application::create(__DIR__);

// Load the .env file
$app->loadEnv();

// Variables are now available in $_ENV
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
```

### Standalone Usage

`php-dotenv` can be used independently of all other packages.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Load the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Access variables
echo $_ENV['DB_HOST'];
echo $_ENV['DB_NAME'];
```

### Usage with Other Frameworks

```php
<?php

// Laravel, Symfony, or any PHP framework
use JulienLinard\Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__)->load();

// Variables are now available
$config = [
    'database' => [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS']
    ]
];
```

## 📚 API Reference

### `Dotenv::createImmutable(string $path, string $file = '.env'): Dotenv`

Creates an immutable instance that does not replace existing variables.

```php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv = Dotenv::createImmutable(__DIR__, '.env.local');
```

### `Dotenv::createMutable(string $path, string $file = '.env'): Dotenv`

Creates a mutable instance that replaces existing variables.

```php
$dotenv = Dotenv::createMutable(__DIR__);
```

### `load(): void`

Loads the `.env` file and sets environment variables in `$_ENV` and `$_SERVER`.

```php
$dotenv->load();
```

### `required(array $variables): Validator`

Validates that the specified variables exist. Throws an exception if a variable is missing.

```php
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
```

### `get(string $key, mixed $default = null): mixed`

Retrieves a variable with an optional default value.

```php
$dbHost = Dotenv::get('DB_HOST', 'localhost');
$dbPort = Dotenv::get('DB_PORT', 3306);
```

## 💡 Advanced Usage Examples

### Validation with Default Values

```php
use JulienLinard\Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Validate with default value
$dotenv->required(['DB_PORT'])->notEmpty()->defaultTo('3306');
```

### Conditional Loading

```php
// Load .env.local if available, otherwise .env
$envFile = file_exists(__DIR__ . '/.env.local') ? '.env.local' : '.env';
$dotenv = Dotenv::createImmutable(__DIR__, $envFile);
$dotenv->load();
```

### Usage in a CLI Script

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Load environment variables
Dotenv::createImmutable(__DIR__)->load();

// Use variables
echo "Database connection: " . $_ENV['DB_HOST'] . "\n";
```

## 🧪 Tests

```bash
composer test
```

## 📝 License

MIT License - See the LICENSE file for more details.

## 🤝 Contributing

Contributions are welcome! Feel free to open an issue or a pull request.

## 💝 Support the project

If this bundle is useful to you, consider [becoming a sponsor](https://github.com/sponsors/julien-lin) to support the development and maintenance of this open source project.

---

**Developed with ❤️ by Julien Linard**
