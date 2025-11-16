<?php

namespace JulienLinard\Dotenv\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Dotenv\Dotenv;
use JulienLinard\Dotenv\Exception\InvalidFileException;
use JulienLinard\Dotenv\Exception\ValidationException;

class DotenvTest extends TestCase
{
    private string $testPath;

    protected function setUp(): void
    {
        $this->testPath = __DIR__ . '/fixtures';
        
        // Nettoyer les variables d'environnement avant chaque test
        unset($_ENV['TEST_VAR'], $_SERVER['TEST_VAR']);
    }

    protected function tearDown(): void
    {
        // Nettoyer après chaque test
        unset($_ENV['TEST_VAR'], $_SERVER['TEST_VAR']);
    }

    public function testLoadEnvFile(): void
    {
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.test');
        $dotenv->load();

        $this->assertEquals('test_value', $_ENV['TEST_VAR']);
        $this->assertEquals('test_value', $_SERVER['TEST_VAR']);
    }

    public function testLoadNonExistentFile(): void
    {
        $this->expectException(InvalidFileException::class);
        
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.nonexistent');
        $dotenv->load();
    }

    public function testImmutableMode(): void
    {
        // Définir une variable avant le chargement
        $_ENV['EXISTING_VAR'] = 'existing_value';
        
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.test');
        $dotenv->load();

        // La variable existante ne doit pas être remplacée
        $this->assertEquals('existing_value', $_ENV['EXISTING_VAR']);
    }

    public function testMutableMode(): void
    {
        // Définir une variable avant le chargement
        $_ENV['TEST_VAR'] = 'old_value';
        
        $dotenv = Dotenv::createMutable($this->testPath, '.env.test');
        $dotenv->load();

        // La variable doit être remplacée
        $this->assertEquals('test_value', $_ENV['TEST_VAR']);
    }

    public function testRequiredVariables(): void
    {
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.test');
        $dotenv->load();
        
        $dotenv->required(['TEST_VAR'])->validate();
        
        $this->assertTrue(true); // Si on arrive ici, la validation a réussi
    }

    public function testRequiredVariablesMissing(): void
    {
        $this->expectException(ValidationException::class);
        
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.test');
        $dotenv->load();
        
        $dotenv->required(['MISSING_VAR'])->validate();
    }

    public function testGetMethod(): void
    {
        $dotenv = Dotenv::createImmutable($this->testPath, '.env.test');
        $dotenv->load();
        
        $value = Dotenv::get('TEST_VAR');
        $this->assertEquals('test_value', $value);
        
        $default = Dotenv::get('NON_EXISTENT', 'default');
        $this->assertEquals('default', $default);
    }
}

