SET NAMES utf8mb4;
-- 1. Inserir PIs (Planos Internos)
INSERT INTO `pis` (`id`, `codigo`, `descricao`, `limite_anual_aprovado`, `saldo_disponivel`) VALUES
(1, 'PI-2026-MOB', 'Programa de Mobilidade Acadêmica Internacional', 150000.00, 100000.00),
(2, 'PI-2026-IDIOMAS', 'Apoio a Cursos de Idiomas e Proficiência', 50000.00, 50000.00),
(3, 'PI-2026-ADM', 'Custeio Administrativo PROINTER', 20000.00, 20000.00);

-- 2. Registrar o Aporte Inicial nas Movimentações
INSERT INTO `movimentacoes` (`pi_destino_id`, `tipo_operacao`, `valor`, `justificativa_historico`) VALUES
(1, 'Aporte_Inicial', 150000.00, 'Abertura do orçamento 2026 para Mobilidade'),
(2, 'Aporte_Inicial', 50000.00, 'Abertura do orçamento 2026 para Idiomas'),
(3, 'Aporte_Inicial', 20000.00, 'Abertura do orçamento 2026 para Custeio Administrativo');

-- 3. Inserir Empenhos
-- Nota: O PI 1 (Mobilidade) tem limite de 150k. Vamos empenhar 50k para um edital específico.
INSERT INTO `empenhos` (`id`, `pi_id`, `numero_processo`, `objeto`, `valor_total`, `valor_consumido`, `status`) VALUES
(1, 1, '23073.001234/2026-11', 'Edital de Bolsas de Intercâmbio - Europa', 50000.00, 15000.00, 'Ativo'),
(2, 3, '23073.004567/2026-89', 'Aquisição de material de expediente', 5000.00, 0.00, 'Ativo');

-- 4. Registrar as Movimentações dos Empenhos
-- Quando o empenho é criado, ele debita do saldo do PI.
INSERT INTO `movimentacoes` (`pi_origem_id`, `empenho_id`, `tipo_operacao`, `valor`, `justificativa_historico`) VALUES
(1, 1, 'Novo_Empenho', 50000.00, 'Reserva de crédito para Edital de Bolsas Europa'),
(3, 2, 'Novo_Empenho', 5000.00, 'Reserva para material de expediente do semestre');

-- 5. Inserir Ações (Consumindo o saldo do Empenho 1)
-- O empenho 1 tem 50k. Vamos registrar o pagamento de bolsas para dois alunos.
INSERT INTO `acoes` (`empenho_id`, `identificador`, `descricao`, `beneficiario`, `valor`) VALUES
(1, 'Portaria 101/2026', 'Pagamento de Auxílio Instalação - Aluno A', 'João Silva', 7500.00),
(1, 'Portaria 102/2026', 'Pagamento de Auxílio Instalação - Aluna B', 'Maria Souza', 7500.00);
