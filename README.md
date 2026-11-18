# WattGYM

E-commerce de produtos para academia. O usuário navega pelo catálogo, cria uma conta e gerencia seus dados pelo site. A loja também tem um painel administrativo.

**Projeto em desenvolvimento. A seção "Em desenvolvimento" lista o que ainda não está pronto.**

## Funcionalidades

- Cadastro e login de usuários, com senha protegida por hash (`password_hash` / `password_verify`)
- Logout
- Catálogo de produtos por categoria (suplementos, creatinas, pré-treinos, vitaminas e acessórios), com preços e imagens
- Perfil para edição de dados pessoais e endereço
- Painel administrativo (dashboard), restrito a administradores, com total de pedidos, produtos, clientes, faturamento e lista de pedidos em aberto
- Página de contato: as mensagens enviadas ficam salvas no banco de dados
- Páginas institucionais (início e sobre)

## Em desenvolvimento

- Carrinho de compras
- Barra de pesquisa
- Finalização e envio de pedidos pelo site
- Alteração do status dos pedidos pelo painel
- Catálogo lendo os produtos direto do banco (hoje a lista está fixa no `produtos.php`)

## Tecnologias

- PHP 8.2
- MySQL 8 (acesso via PDO)
- HTML, CSS e JavaScript
- XAMPP (ambiente local)

## Como rodar

1. Instale o XAMPP e ligue o Apache e o MySQL.
2. Copie a pasta `WattGYM` para `C:\xampp\htdocs`.
3. No phpMyAdmin, importe `database/WattGYM.sql`.
4. Rode os comandos abaixo na aba SQL, para criar as colunas e a tabela que o sistema usa:


ALTER TABLE usuarios
  ADD COLUMN senha VARCHAR(255) NOT NULL DEFAULT '',
  ADD COLUMN tipo ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
  MODIFY COLUMN cpf CHAR(11) NULL DEFAULT NULL;

ALTER TABLE enderecos
  ADD COLUMN rua VARCHAR(100) NOT NULL DEFAULT '' AFTER cep;

CREATE TABLE mensagens_contato (
  id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  assunto VARCHAR(150) NOT NULL,
  mensagem TEXT NOT NULL,
  data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


5. Confira em `includes/config.php` se o usuário e a senha do MySQL estão corretos (no XAMPP, o padrão é `root` sem senha).
6. Acesse http://localhost/WattGYM

## Como criar um administrador

O painel (`dashboard.php`) só abre para quem tem `tipo = 'admin'`.

1. Crie uma conta normal em `cadastro.php`.
2. No phpMyAdmin, rode (trocando pelo seu e-mail):


UPDATE usuarios SET tipo = 'admin' WHERE email = 'seu@email.com';


3. Saia e entre de novo em `login.php`. O administrador vai direto para o dashboard.

Contas criadas antes da coluna `senha` existir não conseguem entrar. Crie uma conta nova ou grave um hash com `password_hash()`.

## Estrutura


WattGYM/
├── index.php          # redireciona para a página inicial
├── inicio.php         # página inicial
├── produtos.php       # catálogo
├── sobre.php
├── contato.php
├── cadastro.php
├── login.php
├── logout.php
├── perfil.php         # exige login
├── dashboard.php      # exige login de administrador
├── header.php
├── footer.php
├── includes/
│   └── config.php     # conexão com o banco (PDO)
└── assets/
    ├── CSS/style.css
    ├── JS/script.js
    └── IMG/


## Documentação

- Requisitos: [DOCS/requisitos.md](DOCS/requisitos.md)
- Banco de dados: [DOCS/banco-de-dados.md](DOCS/banco-de-dados.md)
- Manual do usuário: [DOCS/manual-usuario.md](DOCS/manual-usuario.md)

## Equipe

- João Gabriel - back-end (PHP), front-end, banco de dados e documentação

Projeto do curso técnico em Desenvolvimento de Sistemas — ETE Porto Digital, 2026.