-- ==============================================================================
-- Pãozin — Estrutura de Banco de Dados e Seeds Iniciais (MySQL 8.0+)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS paozin_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE paozin_db;

-- ------------------------------------------------------------------------------
-- 1. Tabela de Estabelecimentos (B2B)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS avaliacoes;
DROP TABLE IF EXISTS fornadas;
DROP TABLE IF EXISTS padaria_cliente;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS padarias;

CREATE TABLE padarias (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    preco_pao_quilo DECIMAL(5, 2) NOT NULL DEFAULT 14.90,
    nota_media DECIMAL(3, 2) DEFAULT 5.00,
    status_assinatura ENUM('ativo', 'inadimplente', 'cancelado') DEFAULT 'ativo',
    ultima_fornada TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status_assinatura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Tabela de Consumidores (B2C)
-- ------------------------------------------------------------------------------
CREATE TABLE clientes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    whatsapp VARCHAR(20) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_whatsapp (whatsapp),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Tabela Pivot (N:N - Clientes vinculados a cada Padaria)
-- ------------------------------------------------------------------------------
CREATE TABLE padaria_cliente (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    padaria_id BIGINT NOT NULL,
    cliente_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_padaria_cliente (padaria_id, cliente_id),
    FOREIGN KEY (padaria_id) REFERENCES padarias(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Tabela de Histórico e Auditoria de Fornadas
-- ------------------------------------------------------------------------------
CREATE TABLE fornadas (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    padaria_id BIGINT NOT NULL,
    preco_quilo_momento DECIMAL(5, 2) NOT NULL,
    disparado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_avisados INT DEFAULT 0,
    FOREIGN KEY (padaria_id) REFERENCES padarias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Tabela de Avaliações
-- ------------------------------------------------------------------------------
CREATE TABLE avaliacoes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    padaria_id BIGINT NOT NULL,
    cliente_id BIGINT NULL,
    nota INT CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (padaria_id) REFERENCES padarias(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- SEEDS (DADOS INICIAIS PARA TESTES DO MVP)
-- ==============================================================================

-- Padarias Piloto
INSERT INTO padarias (id, nome, endereco, latitude, longitude, preco_pao_quilo, nota_media, status_assinatura) VALUES
(1, 'Padaria Fornada de Ouro', 'Rua das Flores, 120 - Bairro Central', -23.55052000, -46.63330800, 15.90, 4.95, 'ativo'),
(2, 'Panificadora Pão da Vila', 'Av. das Acácias, 840 - Vila Nova', -23.56141400, -46.65588100, 14.50, 4.80, 'ativo');

-- Clientes de Teste
INSERT INTO clientes (id, whatsapp, nome, ativo) VALUES
(1, '5511999990001', 'Gabriel Silva', 1),
(2, '5511999990002', 'Mariana Oliveira', 1),
(3, '5511999990003', 'Carlos Eduardo', 1);

-- Associações (Quem segue qual padaria)
-- Gabriel e Mariana seguem a Padaria 1; Mariana e Carlos seguem a Padaria 2
INSERT INTO padaria_cliente (padaria_id, cliente_id) VALUES
(1, 1),
(1, 2),
(2, 2),
(2, 3);

-- Avaliações Iniciais
INSERT INTO avaliacoes (padaria_id, cliente_id, nota, comentario) VALUES
(1, 1, 5, 'Pão francês com crosta estalando e miolo macio! Notificação chegou no momento certo.'),
(1, 2, 5, 'Melhor pão do bairro, quentinho às 17h em ponto.');
