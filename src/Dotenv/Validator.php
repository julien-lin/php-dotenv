<?php

namespace JulienLinard\Dotenv;

use JulienLinard\Dotenv\Exception\ValidationException;

/**
 * Validateur pour les variables d'environnement requises
 */
class Validator
{
    private array $variables;
    private array $required;

    public function __construct(array $variables, array $required)
    {
        $this->variables = $variables;
        $this->required = $required;
    }

    /**
     * Valide que les variables requises existent
     *
     * @throws ValidationException Si une variable requise est manquante
     */
    public function validate(): void
    {
        $missing = [];

        foreach ($this->required as $variable) {
            if (!isset($this->variables[$variable])) {
                $missing[] = $variable;
            }
        }

        if (!empty($missing)) {
            throw new ValidationException(
                sprintf(
                    'Variables d\'environnement requises manquantes: %s',
                    implode(', ', $missing)
                )
            );
        }
    }

    /**
     * Valide que les variables ne sont pas vides
     *
     * @throws ValidationException Si une variable est vide
     */
    public function notEmpty(): self
    {
        $empty = [];

        foreach ($this->required as $variable) {
            if (isset($this->variables[$variable])) {
                $value = $this->variables[$variable];
                if ($value === null || $value === '' || $value === false) {
                    $empty[] = $variable;
                }
            }
        }

        if (!empty($empty)) {
            throw new ValidationException(
                sprintf(
                    'Variables d\'environnement ne peuvent pas être vides: %s',
                    implode(', ', $empty)
                )
            );
        }

        return $this;
    }

    /**
     * Définit une valeur par défaut pour les variables manquantes
     */
    public function defaultTo(mixed $default): void
    {
        foreach ($this->required as $variable) {
            if (!isset($this->variables[$variable])) {
                $this->variables[$variable] = $default;
                $_ENV[$variable] = $default;
                $_SERVER[$variable] = $default;
            }
        }
    }

    /**
     * Valide que les variables sont des entiers
     */
    public function isInteger(): self
    {
        $invalid = [];

        foreach ($this->required as $variable) {
            if (isset($this->variables[$variable])) {
                if (!is_numeric($this->variables[$variable]) || (int)$this->variables[$variable] != $this->variables[$variable]) {
                    $invalid[] = $variable;
                }
            }
        }

        if (!empty($invalid)) {
            throw new ValidationException(
                sprintf(
                    'Variables d\'environnement doivent être des entiers: %s',
                    implode(', ', $invalid)
                )
            );
        }

        return $this;
    }

    /**
     * Valide que les variables sont des booléens
     */
    public function isBoolean(): self
    {
        $invalid = [];

        foreach ($this->required as $variable) {
            if (isset($this->variables[$variable])) {
                $value = strtolower((string)$this->variables[$variable]);
                if (!in_array($value, ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'], true)) {
                    $invalid[] = $variable;
                }
            }
        }

        if (!empty($invalid)) {
            throw new ValidationException(
                sprintf(
                    'Variables d\'environnement doivent être des booléens: %s',
                    implode(', ', $invalid)
                )
            );
        }

        return $this;
    }
}

