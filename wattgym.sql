-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/10/2026 às 23:25
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `wattgym`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome`, `descricao`) VALUES
(1, 'Suplementos', 'Whey Protein Isolado, Concentrado, Hidrolisado e Proteínas Vegetais'),
(2, 'Acessórios e Equipamentos', 'Coqueteleiras, straps, cinturões, grips e acessórios para treino'),
(3, 'Creatinas', 'Creatina Monohidratada, Micronizada e Creapure em pó ou cápsulas'),
(4, 'Pré-Treinos e Termogênicos', 'Suplementos para energia, foco, vascularização e queima de gordura'),
(5, 'Vitaminas e Saúde', 'Multivitamínicos, Omega 3, ZMA e suplementos para saúde geral');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `id_cupom` int(11) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `valor_desconto` decimal(10,2) NOT NULL,
  `data_validade` datetime NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cupons`
--

INSERT INTO `cupons` (`id_cupom`, `codigo`, `valor_desconto`, `data_validade`, `ativo`, `data_cadastro`) VALUES
(1, 'WATT10', 10.00, '2026-12-31 23:59:59', 1, '2026-10-21 20:40:33'),
(2, 'FIRST20', 20.00, '2026-12-31 23:59:59', 1, '2026-10-21 20:40:33'),
(3, 'CREATINA15', 15.00, '2026-12-31 23:59:59', 1, '2026-10-21 20:40:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cep` char(8) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `uf` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id_endereco`, `id_usuario`, `cep`, `numero`, `complemento`, `bairro`, `cidade`, `uf`) VALUES
(1, 1, '67325361', '100', 'casa 1', 'Janga', 'Paulista', 'PE'),
(2, 1, '08764578', '150', 'casa 2', 'Janga', 'Paulista', 'PE'),
(3, 1, '08754567', '50', 'casa 3', 'Janga', 'Paulista', 'PE'),
(4, 2, '23465434', '10', 'apto 10', 'Boa Viagem', 'Recife', 'PE'),
(5, 3, '76543212', '1', 'casa 4', 'agora não sei', 'Recife', 'PE'),
(6, 1, '44354361', '287', 'casa 1', 'Valentina', 'Joao Pessoa', 'PB');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id_item` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lojas`
--

CREATE TABLE `lojas` (
  `id_loja` int(11) NOT NULL,
  `nome_fantasia` varchar(100) NOT NULL,
  `cnpj` char(14) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lojas`
--

INSERT INTO `lojas` (`id_loja`, `nome_fantasia`, `cnpj`, `email`, `telefone`, `data_cadastro`) VALUES
(1, 'WattGYM Oficial', '12345678000199', 'Ricardosexy@wattgym.com', '81999999999', '2026-10-21 19:50:23'),
(2, 'Alpha Nutri Center', '98765432000188', 'Sapeca@alphanutri.com', '81988888888', '2026-10-21 19:50:23'),
(3, 'tchutchuco lab', '52523523532325', 'lerdo@tchutchucolab.com', '81993323292', '2026-10-21 19:50:23');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id_pagamento` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `forma_pagamento` varchar(50) NOT NULL COMMENT 'Pix, Cartao_Credito, Cartao_Debito, Boleto',
  `status_pagamento` varchar(50) NOT NULL COMMENT 'Pendente, Aprovado, Recusado',
  `valor_pago` decimal(10,2) NOT NULL,
  `data_pagamento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_endereco` int(11) NOT NULL,
  `status_pedido` varchar(50) DEFAULT 'Pendente' COMMENT 'Pendente, Pago, Em Transporte, Entregue, Cancelado',
  `valor_total` decimal(10,2) NOT NULL,
  `data_do_pedido` datetime DEFAULT current_timestamp(),
  `id_cupom` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `id_endereco`, `status_pedido`, `valor_total`, `data_do_pedido`, `id_cupom`) VALUES
(11, 1, 1, 'Pago', 99.90, '2026-08-05 10:30:00', NULL),
(12, 2, 2, 'Pago', 139.90, '2026-08-12 14:20:00', NULL),
(13, 3, 3, 'Entregue', 239.80, '2026-08-20 16:45:00', NULL),
(14, 1, 1, 'Entregue', 161.82, '2026-09-02 11:00:00', 1),
(15, 1, 1, 'Entregue', 119.90, '2026-09-15 09:15:00', 2),
(16, 2, 2, 'Entregue', 80.91, '2026-09-28 18:00:00', 1),
(17, 3, 3, 'Entregue', 449.70, '2026-10-01 13:10:00', 3),
(18, 3, 3, 'Pago', 299.70, '2026-10-10 15:30:00', NULL),
(19, 4, 4, 'Entregue', 79.90, '2026-10-05 08:20:00', NULL),
(20, 2, 2, 'Pago', 89.90, '2026-10-18 17:00:00', NULL),
(21, 1, 1, 'Pendente', 99.90, '2026-08-05 10:30:00', NULL),
(22, 2, 2, 'Pendente', 139.90, '2026-08-12 14:20:00', NULL),
(23, 3, 3, 'Pendente', 239.80, '2026-08-20 16:45:00', NULL),
(24, 1, 1, 'Pendente', 161.82, '2026-09-02 11:00:00', 1),
(25, 1, 1, 'Pendente', 119.90, '2026-09-15 09:15:00', 2),
(26, 2, 2, 'Pendente', 80.91, '2026-09-28 18:00:00', 1),
(27, 3, 3, 'Pendente', 449.70, '2026-10-01 13:10:00', 3),
(28, 3, 3, 'Pendente', 299.70, '2026-10-10 15:30:00', NULL),
(29, 4, 4, 'Pendente', 79.90, '2026-10-05 08:20:00', NULL),
(30, 2, 2, 'Pendente', 89.90, '2026-10-18 17:00:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `id_loja` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `preco` decimal(10,2) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `id_loja`, `id_categoria`, `nome`, `descricao`, `estoque`, `preco`, `data_cadastro`) VALUES
(26, 1, 1, '100% Whey Concentrate WattGYM - Chocolate (1kg)', 'Whey protein concentrado sabor chocolate de alta pureza.', 200, 99.90, '2026-10-21 19:52:12'),
(27, 1, 1, '100% Whey Concentrate WattGYM - Morango (1kg)', 'Whey protein concentrado sabor morango.', 180, 99.90, '2026-10-21 19:52:12'),
(28, 1, 1, '100% Whey Concentrate WattGYM - Baunilha (1kg)', 'Whey protein concentrado sabor baunilha.', 150, 99.90, '2026-10-21 19:52:12'),
(29, 1, 1, '100% Whey Concentrate WattGYM - Cookies (1kg)', 'Whey concentrado sabor cookies & cream.', 120, 104.90, '2026-10-21 19:52:12'),
(30, 1, 1, 'Whey Isolado WattIso 100% - Chocolate (900g)', 'Whey isolado de rápida absorção e zero lactose.', 90, 149.90, '2026-10-21 19:52:12'),
(31, 1, 1, 'Whey Isolado WattIso 100% - Natural (900g)', 'Whey isolado sem sabor adicionado.', 80, 144.90, '2026-10-21 19:52:12'),
(32, 2, 1, 'Beef Protein Pro (900g)', 'Proteína isolada da carne bovina sabor chocolate amargo.', 50, 159.90, '2026-10-21 19:52:12'),
(33, 2, 1, 'Vegan Protein Plant-Based (900g)', 'Proteína vegana à base de ervilha e arroz sabor baunilha.', 60, 119.90, '2026-10-21 19:52:12'),
(34, 1, 2, 'Coqueteleira WattGYM Shaker 700ml', 'Coqueteleira reforçada com mola misturadora.', 300, 24.90, '2026-10-21 19:52:12'),
(35, 1, 2, 'Coqueteleira WattGYM 2 Compartimentos', 'Coqueteleira com porta-pó e porta-cápsulas.', 150, 34.90, '2026-10-21 19:52:12'),
(36, 2, 2, 'Straps de Treino em Couro Alpha', 'Strap profissional para pegada firme em treinos pesados.', 80, 45.90, '2026-10-21 19:52:12'),
(37, 2, 2, 'Cinturão de Agachamento em Couro', 'Cinturão de proteção lombar ajustável.', 40, 129.90, '2026-10-21 19:52:12'),
(38, 2, 2, 'Grip de Calistenia e Crossfit', 'Grip de proteção para as mãos no bar de tração.', 70, 59.90, '2026-10-21 19:52:12'),
(39, 1, 3, 'Creatina Monohidratada WattGYM (250g)', 'Creatina monohidratada micronizada em pó 100% pura.', 250, 79.90, '2026-10-21 19:52:12'),
(40, 1, 3, 'Creatina Monohidratada WattGYM (500g)', 'Embalagem econômica de 500g de creatina pura.', 180, 139.90, '2026-10-21 19:52:12'),
(41, 1, 3, 'Creatina Creapure WattGYM (300g)', 'Creatina com selo de pureza internacional Creapure.', 100, 119.90, '2026-10-21 19:52:12'),
(42, 2, 3, 'Creatina em Cápsulas Alpha (120 caps)', 'Creatina monohidratada em cápsulas práticas.', 90, 89.90, '2026-10-21 19:52:12'),
(43, 1, 4, 'Pré-Treino WattShock Concentrado (300g)', 'Fórmula ultra concentrada com Beta-Alanina e Cafeína.', 110, 89.90, '2026-10-21 19:52:12'),
(44, 1, 4, 'Pré-Treino WattShock Limão (300g)', 'Pré-treino sabor limão para foco e pump vascular.', 95, 89.90, '2026-10-21 19:52:12'),
(45, 1, 4, 'Termogênico WattBurn 400mg (60 caps)', 'Termogênico em cápsulas com cafeína anidra pura.', 130, 59.90, '2026-10-21 19:52:12'),
(46, 2, 4, 'Pré-Treino Insane Power Alpha (350g)', 'Pré-treino avançado sabor uva com taurina e tirosina.', 75, 109.90, '2026-10-21 19:52:12'),
(47, 1, 5, 'Multivitamínico Complete WattGYM (90 caps)', 'Complexo de vitaminas e minerais de A a Z.', 200, 39.90, '2026-10-21 19:52:12'),
(48, 1, 5, 'Omega 3 Ultra Pure WattGYM (120 caps)', 'Óleo de peixe rico em EPA e DHA com selo MEG-3.', 160, 69.90, '2026-10-21 19:52:12'),
(49, 1, 5, 'ZMA Pro WattGYM (90 caps)', 'Suporte de Zinco, Magnésio e B6 para recuperação muscular.', 140, 49.90, '2026-10-21 19:52:12'),
(50, 2, 5, 'Vitamina C + Zinco Efervescente Alpha (10 tab)', 'Vitamina C efervescente para reforço da imunidade.', 110, 29.90, '2026-10-21 19:52:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` char(11) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data_de_nascimento` date NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `cpf`, `telefone`, `data_de_nascimento`, `data_cadastro`) VALUES
(1, 'Cauã Carlos', 'carlosloli@email.com', '11122233301', '81991112222', '1995-03-12', '2026-10-21 19:25:05'),
(2, 'Vitor Miguel', 'VTR@email.com', '22233344402', '81992223333', '1998-07-25', '2026-10-21 19:25:05'),
(3, 'Rafael Santana', 'pega_casada@email.com', '33344455503', '81993334444', '2001-11-05', '2026-10-21 19:25:05'),
(4, 'Luiz Arthur', 'SR.perguntas@email.com', '44455566604', '81994445555', '1992-01-30', '2026-10-21 19:25:05'),
(5, 'Jalyson Henrique', 'Willy_Wonka@email.com', '55566677705', '81995556666', '2000-09-18', '2026-10-21 19:25:05'),
(6, 'Joao Bigode', 'Odonodessadelicia@email.com', '99988877766', '81998887766', '1590-05-15', '2026-10-26 19:21:06');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id_cupom`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `lojas`
--
ALTER TABLE `lojas`
  ADD PRIMARY KEY (`id_loja`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_endereco` (`id_endereco`),
  ADD KEY `fk_pedidos_cupons` (`id_cupom`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_loja` (`id_loja`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id_cupom` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `lojas`
--
ALTER TABLE `lojas`
  MODIFY `id_loja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`);

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cupons` FOREIGN KEY (`id_cupom`) REFERENCES `cupons` (`id_cupom`),
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id_endereco`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_loja`) REFERENCES `lojas` (`id_loja`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
