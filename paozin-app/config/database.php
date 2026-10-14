<?php

class Database {
    private static ?PDO $instance = null;
    private static string $driverUsed = '';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::loadEnv();

            $driversDisponiveis = PDO::getAvailableDrivers();
            $temMysql = in_array('mysql', $driversDisponiveis, true);

            // Tenta MySQL se o driver estiver presente no PHP
            if ($temMysql) {
                try {
                    $host = getenv('DB_HOST') ?: '127.0.0.1';
                    $port = getenv('DB_PORT') ?: '3306';
                    $dbname = getenv('DB_NAME') ?: 'paozin_db';
                    $user = getenv('DB_USER') ?: 'paozin_user';
                    $pass = getenv('DB_PASS') ?: 'paozin_password_local';

                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_TIMEOUT            => 2,
                    ]);
                    self::$driverUsed = 'mysql';
                    return self::$instance;
                } catch (PDOException $e) {
                    // Se o MySQL falhar (ex: Docker desligado), cai para o fallback do SQLite
                }
            }

            // Fallback transparente: SQLite
            if (in_array('sqlite', $driversDisponiveis, true)) {
                $sqliteDir = __DIR__ . '/../../database';
                if (!is_dir($sqliteDir)) {
                    mkdir($sqliteDir, 0777, true);
                }

                $sqlitePath = $sqliteDir . '/paozin.sqlite';
                $novoBanco = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;

                self::$instance = new PDO("sqlite:" . $sqlitePath, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$driverUsed = 'sqlite';

                if ($novoBanco) {
                    self::inicializarSqlite(self::$instance);
                }

                return self::$instance;
            }

            die("❌ Nenhum driver compatível (MySQL ou SQLite) encontrado no PHP.");
        }

        return self::$instance;
    }

    public static function getDriver(): string {
        return self::$driverUsed;
    }

    private static function inicializarSqlite(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS padarias (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                endereco TEXT NOT NULL,
                latitude REAL NULL,
                longitude REAL NULL,
                preco_pao_quilo REAL NOT NULL DEFAULT 14.90,
                nota_media REAL DEFAULT 5.00,
                status_assinatura TEXT DEFAULT 'ativo',
                ultima_fornada DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS clientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                whatsapp TEXT NOT NULL UNIQUE,
                nome TEXT NOT NULL,
                ativo INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS padaria_cliente (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                padaria_id INTEGER NOT NULL,
                cliente_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(padaria_id, cliente_id)
            );

            CREATE TABLE IF NOT EXISTS fornadas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                padaria_id INTEGER NOT NULL,
                preco_quilo_momento REAL NOT NULL,
                total_avisados INTEGER DEFAULT 0,
                disparado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS avaliacoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                padaria_id INTEGER NOT NULL,
                cliente_id INTEGER NULL,
                nota INTEGER,
                comentario TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            -- Seeds de Demonstração
            INSERT INTO padarias (id, nome, endereco, latitude, longitude, preco_pao_quilo, nota_media, status_assinatura) VALUES
            (1, 'Padaria Fornada de Ouro', 'Rua das Flores, 120 - Bairro Central', -23.55052, -46.63330, 15.90, 4.95, 'ativo'),
            (2, 'Panificadora Pão da Vila', 'Av. das Acácias, 840 - Vila Nova', -23.56141, -46.65588, 14.50, 4.80, 'ativo');

            INSERT INTO clientes (id, whatsapp, nome, ativo) VALUES
            (1, '5511999990001', 'Gabriel Silva', 1),
            (2, '5511999990002', 'Mariana Oliveira', 1),
            (3, '5511999990003', 'Carlos Eduardo', 1);

            INSERT INTO padaria_cliente (padaria_id, cliente_id) VALUES
            (1, 1),
            (1, 2),
            (2, 2),
            (2, 3);

            INSERT INTO avaliacoes (padaria_id, cliente_id, nota, comentario) VALUES
            (1, 1, 5, 'Pão crocante e quentinho no meio da tarde! Notificação perfeita.'),
            (1, 2, 5, 'O melhor pão francês da região.');
        ");
    }

    private static function loadEnv(): void {
        $envPaths = [
            __DIR__ . '/../../.env',
            __DIR__ . '/../.env'
        ];

        foreach ($envPaths as $path) {
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    if (str_contains($line, '=')) {
                        [$name, $value] = explode('=', $line, 2);
                        $name = trim($name);
                        $value = trim($value);
                        if (!getenv($name)) {
                            putenv("{$name}={$value}");
                            $_ENV[$name] = $value;
                        }
                    }
                }
                break;
            }
        }
    }
}
