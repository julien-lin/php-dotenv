# PHP Dotenv

Une librairie PHP simple et moderne pour charger les variables d'environnement depuis un fichier `.env`.

## 🚀 Installation

```bash
composer require julienlinard/php-dotenv
```

**Requirements** : PHP 8.0 ou supérieur

## ⚡ Démarrage rapide

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Charger le fichier .env depuis le répertoire racine
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Accéder aux variables
echo $_ENV['DB_HOST'];
echo $_ENV['DB_NAME'];
```

## 📋 Fonctionnalités

- ✅ Chargement de fichiers `.env`
- ✅ Support des commentaires (lignes commençant par `#`)
- ✅ Support des valeurs entre guillemets simples et doubles
- ✅ Support des valeurs multi-lignes
- ✅ Expansion de variables (`${VAR}` ou `$VAR`)
- ✅ Mode immutable (ne remplace pas les variables existantes)
- ✅ Validation des variables requises
- ✅ Support des valeurs booléennes et null

## 📖 Utilisation

### Chargement basique

```php
use JulienLinard\Dotenv\Dotenv;

// Créer une instance immutable (ne remplace pas les variables existantes)
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

### Chargement mutable

```php
// Créer une instance mutable (remplace les variables existantes)
$dotenv = Dotenv::createMutable(__DIR__);
$dotenv->load();
```

### Validation des variables requises

```php
use JulienLinard\Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Valider que certaines variables existent
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
```

### Validation avec valeurs par défaut

```php
// Valider avec valeurs par défaut
$dotenv->required(['DB_PORT'])->notEmpty()->defaultTo('3306');
```

### Récupération directe d'une variable

```php
// Récupérer une variable avec valeur par défaut
$dbHost = Dotenv::get('DB_HOST', 'localhost');
```

## 📝 Format du fichier .env

```env
# Commentaire
DB_HOST=localhost
DB_NAME=mydatabase
DB_USER=root
DB_PASS=password123

# Valeurs entre guillemets
APP_NAME="Mon Application"
APP_URL='https://example.com'

# Valeurs booléennes
DEBUG=true
MAINTENANCE=false

# Valeur null
CACHE_DRIVER=null

# Expansion de variables
APP_URL=https://example.com
API_URL=${APP_URL}/api

# Valeurs multi-lignes (avec guillemets)
DESCRIPTION="Ceci est une description
sur plusieurs lignes"
```

## 🔒 Sécurité

- Les variables sont chargées dans `$_ENV` et `$_SERVER`
- Mode immutable par défaut (ne remplace pas les variables système existantes)
- Validation des noms de variables (caractères alphanumériques et underscores uniquement)

## 🔗 Intégration avec les autres packages

### Intégration avec core-php

`core-php` inclut automatiquement `php-dotenv`. Utilisez `loadEnv()` pour charger les variables.

```php
<?php

use JulienLinard\Core\Application;

$app = Application::create(__DIR__);

// Charger le fichier .env
$app->loadEnv();

// Les variables sont maintenant disponibles dans $_ENV
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
```

### Utilisation standalone

`php-dotenv` peut être utilisé indépendamment de tous les autres packages.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Charger le fichier .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Accéder aux variables
echo $_ENV['DB_HOST'];
echo $_ENV['DB_NAME'];
```

### Utilisation avec d'autres frameworks

```php
<?php

// Laravel, Symfony, ou n'importe quel framework PHP
use JulienLinard\Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__)->load();

// Les variables sont maintenant disponibles
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

Crée une instance immutable qui ne remplace pas les variables existantes.

```php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv = Dotenv::createImmutable(__DIR__, '.env.local');
```

### `Dotenv::createMutable(string $path, string $file = '.env'): Dotenv`

Crée une instance mutable qui remplace les variables existantes.

```php
$dotenv = Dotenv::createMutable(__DIR__);
```

### `load(): void`

Charge le fichier `.env` et définit les variables d'environnement dans `$_ENV` et `$_SERVER`.

```php
$dotenv->load();
```

### `required(array $variables): Validator`

Valide que les variables spécifiées existent. Lance une exception si une variable est manquante.

```php
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);
```

### `get(string $key, mixed $default = null): mixed`

Récupère une variable avec une valeur par défaut optionnelle.

```php
$dbHost = Dotenv::get('DB_HOST', 'localhost');
$dbPort = Dotenv::get('DB_PORT', 3306);
```

## 💡 Exemples d'utilisation avancée

### Validation avec valeurs par défaut

```php
use JulienLinard\Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Valider avec valeur par défaut
$dotenv->required(['DB_PORT'])->notEmpty()->defaultTo('3306');
```

### Chargement conditionnel

```php
// Charger .env.local si disponible, sinon .env
$envFile = file_exists(__DIR__ . '/.env.local') ? '.env.local' : '.env';
$dotenv = Dotenv::createImmutable(__DIR__, $envFile);
$dotenv->load();
```

### Utilisation dans un script CLI

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Dotenv\Dotenv;

// Charger les variables d'environnement
Dotenv::createImmutable(__DIR__)->load();

// Utiliser les variables
echo "Connexion à la base de données : " . $_ENV['DB_HOST'] . "\n";
```

## 🧪 Tests

```bash
composer test
```

## 📝 License

MIT License - Voir le fichier LICENSE pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

---

**Développé avec ❤️ par Julien Linard**

