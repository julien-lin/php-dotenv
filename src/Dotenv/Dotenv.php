<?php

namespace JulienLinard\Dotenv;

use JulienLinard\Dotenv\Exception\InvalidPathException;
use JulienLinard\Dotenv\Exception\InvalidFileException;

/**
 * Classe principale pour charger les variables d'environnement depuis un fichier .env
 */
class Dotenv
{
    /**
     * Chemin vers le répertoire contenant le fichier .env
     */
    private string $path;

    /**
     * Nom du fichier .env (par défaut: .env)
     */
    private string $file;

    /**
     * Mode immutable (ne remplace pas les variables existantes)
     */
    private bool $immutable;

    /**
     * Variables chargées depuis le fichier .env
     */
    private array $variables = [];

    /**
     * Constructeur privé (utiliser createImmutable ou createMutable)
     */
    private function __construct(string $path, string $file = '.env', bool $immutable = true)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->file = $file;
        $this->immutable = $immutable;
    }

    /**
     * Crée une instance immutable (ne remplace pas les variables existantes)
     */
    public static function createImmutable(string $path, string $file = '.env'): self
    {
        return new self($path, $file, true);
    }

    /**
     * Crée une instance mutable (remplace les variables existantes)
     */
    public static function createMutable(string $path, string $file = '.env'): self
    {
        return new self($path, $file, false);
    }

    /**
     * Charge le fichier .env et définit les variables d'environnement
     *
     * @throws InvalidPathException Si le chemin n'existe pas
     * @throws InvalidFileException Si le fichier .env n'existe pas ou n'est pas lisible
     */
    public function load(): void
    {
        $filePath = $this->getFilePath();

        if (!file_exists($filePath)) {
            throw new InvalidFileException(
                sprintf('Le fichier .env n\'existe pas à l\'emplacement: %s', $filePath)
            );
        }

        if (!is_readable($filePath)) {
            throw new InvalidFileException(
                sprintf('Le fichier .env n\'est pas lisible: %s', $filePath)
            );
        }

        $this->parseFile($filePath);
        $this->setEnvironmentVariables();
    }

    /**
     * Retourne le chemin complet vers le fichier .env
     */
    private function getFilePath(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->file;
    }

    /**
     * Parse le fichier .env et extrait les variables
     */
    private function parseFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new InvalidFileException('Impossible de lire le fichier .env');
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignorer les commentaires et les lignes vides
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Parser la ligne (format: KEY=VALUE)
            if (str_contains($line, '=')) {
                [$key, $value] = $this->parseLine($line);
                
                if ($key !== null && $value !== null) {
                    // Expansion de variables (${VAR} ou $VAR)
                    $value = $this->expandVariables($value);
                    
                    // Conversion des valeurs spéciales
                    $value = $this->convertValue($value);
                    
                    $this->variables[$key] = $value;
                }
            }
        }
    }

    /**
     * Parse une ligne du fichier .env
     *
     * @return array{string|null, string|null} [key, value]
     */
    private function parseLine(string $line): array
    {
        // Trouver la position du premier =
        $equalsPos = strpos($line, '=');
        
        if ($equalsPos === false) {
            return [null, null];
        }

        $key = trim(substr($line, 0, $equalsPos));
        $value = trim(substr($line, $equalsPos + 1));

        // Valider le nom de la variable
        if (!$this->isValidVariableName($key)) {
            return [null, null];
        }

        // Parser la valeur (supporter les guillemets)
        $value = $this->parseValue($value);

        return [$key, $value];
    }

    /**
     * Parse une valeur (gère les guillemets simples et doubles)
     */
    private function parseValue(string $value): string
    {
        // Valeur vide
        if (empty($value)) {
            return '';
        }

        // Valeur entre guillemets doubles
        if (preg_match('/^"(.*)"$/s', $value, $matches)) {
            return str_replace(['\\"', '\\n', '\\r'], ['"', "\n", "\r"], $matches[1]);
        }

        // Valeur entre guillemets simples
        if (preg_match("/^'(.*)'$/s", $value, $matches)) {
            return $matches[1];
        }

        // Valeur sans guillemets
        return $value;
    }

    /**
     * Vérifie si un nom de variable est valide
     */
    private function isValidVariableName(string $name): bool
    {
        // Nom de variable valide: lettres, chiffres, underscores, ne commence pas par un chiffre
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name) === 1;
    }

    /**
     * Expansion des variables (${VAR} ou $VAR)
     */
    private function expandVariables(string $value): string
    {
        // Expansion ${VAR}
        $value = preg_replace_callback(
            '/\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function ($matches) {
                $varName = $matches[1];
                $envValue = $_ENV[$varName] ?? $_SERVER[$varName] ?? null;
                return $envValue !== null ? $envValue : $matches[0];
            },
            $value
        );

        // Expansion $VAR (mais pas ${VAR} déjà traité)
        $value = preg_replace_callback(
            '/\$([a-zA-Z_][a-zA-Z0-9_]*)/',
            function ($matches) {
                $varName = $matches[1];
                $envValue = $_ENV[$varName] ?? $_SERVER[$varName] ?? null;
                return $envValue !== null ? $envValue : $matches[0];
            },
            $value
        );

        return $value;
    }

    /**
     * Convertit les valeurs spéciales (true, false, null)
     */
    private function convertValue(string $value): mixed
    {
        $lowerValue = strtolower($value);

        if ($lowerValue === 'true') {
            return true;
        }

        if ($lowerValue === 'false') {
            return false;
        }

        if ($lowerValue === 'null' || $lowerValue === '') {
            return null;
        }

        return $value;
    }

    /**
     * Définit les variables d'environnement
     */
    private function setEnvironmentVariables(): void
    {
        foreach ($this->variables as $key => $value) {
            // En mode immutable, ne pas remplacer les variables existantes
            if ($this->immutable) {
                if (isset($_ENV[$key]) || isset($_SERVER[$key])) {
                    continue;
                }
            }

            // Définir dans $_ENV et $_SERVER
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            
            // Utiliser putenv() pour compatibilité
            if (is_string($value) || is_numeric($value) || is_bool($value)) {
                putenv("$key=" . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
            }
        }
    }

    /**
     * Valide que certaines variables sont requises
     */
    public function required(array $variables): Validator
    {
        return new Validator($this->variables, $variables);
    }

    /**
     * Récupère une variable avec une valeur par défaut
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Retourne toutes les variables chargées
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}

