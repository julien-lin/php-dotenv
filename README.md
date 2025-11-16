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

## 📚 API Reference

### `Dotenv::createImmutable(string $path): Dotenv`

Crée une instance immutable qui ne remplace pas les variables existantes.

### `Dotenv::createMutable(string $path): Dotenv`

Crée une instance mutable qui remplace les variables existantes.

### `load(): void`

Charge le fichier `.env` et définit les variables d'environnement.

### `required(array $variables): Validator`

Valide que les variables spécifiées existent.

### `get(string $key, mixed $default = null): mixed`

Récupère une variable avec une valeur par défaut optionnelle.

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

