-- Criação da tabela de PIs (Planos Internos)
CREATE TABLE `pis` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `descricao` VARCHAR(255) NOT NULL,
    `limite_anual_aprovado` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `saldo_disponivel` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Empenhos
CREATE TABLE `empenhos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pi_id` INT UNSIGNED NOT NULL,
    `numero_processo` VARCHAR(100) NOT NULL,
    `objeto` TEXT NOT NULL,
    `valor_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `valor_consumido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('Ativo', 'Liquidado', 'Cancelado') NOT NULL DEFAULT 'Ativo',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_empenhos_pi` FOREIGN KEY (`pi_id`) 
        REFERENCES `pis`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Ações (Vinculadas aos Empenhos)
CREATE TABLE `acoes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `empenho_id` INT UNSIGNED NOT NULL,
    `identificador` VARCHAR(100) NOT NULL COMMENT 'Pode ser número de NF, Portaria, Recibo, etc.',
    `descricao` TEXT NOT NULL,
    `beneficiario` VARCHAR(255) NOT NULL,
    `valor` DECIMAL(15,2) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    
    CONSTRAINT `fk_acoes_empenho` FOREIGN KEY (`empenho_id`) 
        REFERENCES `empenhos`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da tabela de Movimentações (Ledger/Razão)
CREATE TABLE `movimentacoes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pi_origem_id` INT UNSIGNED NULL COMMENT 'Nulo se for aporte externo da Adm Superior',
    `pi_destino_id` INT UNSIGNED NULL COMMENT 'Nulo se for saída/empenho',
    `empenho_id` INT UNSIGNED NULL COMMENT 'Preenchido quando a movimentação for vinculada a um empenho',
    `tipo_operacao` ENUM(
        'Aporte_Inicial', 
        'Transferencia', 
        'Novo_Empenho', 
        'Reforco_Empenho', 
        'Cancelamento_Empenho',
        'Estorno_Acao'
    ) NOT NULL,
    `valor` DECIMAL(15,2) NOT NULL,
    `justificativa_historico` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_movimentacoes_pi_origem` FOREIGN KEY (`pi_origem_id`) 
        REFERENCES `pis`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_movimentacoes_pi_destino` FOREIGN KEY (`pi_destino_id`) 
        REFERENCES `pis`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_movimentacoes_empenho` FOREIGN KEY (`empenho_id`) 
        REFERENCES `empenhos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
