# WattGYM

E-commerce de produtos para academia. O usuário navega pelo catálogo, cria uma conta e gerencia seus dados pelo site. A loja também tem um painel administrativo.

> Projeto em desenvolvimento. A seção "Em desenvolvimento" lista o que ainda não está pronto.

## Funcionalidades

- Cadastro e login de usuários, com senha protegida por hash (`password_hash` / `password_verify`)
- Logout
- Catálogo de produtos por categoria (suplementos, creatinas, pré-treinos, vitaminas e acessórios), com preços e imagens
- Perfil para edição de dados pessoais e endereço
- Painel administrativo (dashboard), restrito a administradores, com total de pedidos, produtos, clientes, faturamento e lista de pedidos em aberto
- Gestão de usuários (só administradores): lista com busca por nome ou e-mail, perfil e setor de cada usuário (JOIN), cadastro com seleção de perfil e setor, e exclusão lógica (status Baixa) com confirmação, proteção CSRF e auditoria
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
- MySQL/MariaDB (o XAMPP traz o MariaDB), acesso via PDO
- HTML, CSS e JavaScript
- XAMPP (ambiente local)

## Como rodar

1. Instale o XAMPP e ligue o Apache e o MySQL.
2. Copie a pasta `WattGYM` para `C:\xampp\htdocs`.
3. No phpMyAdmin, crie o banco `wattgym` (agrupamento `utf8mb4_general_ci`) e, com ele selecionado, importe `database/WattGYM.sql`. O arquivo já traz todas as tabelas, inclusive as da Sprint 01, e os usuários de teste.
4. Confira em `includes/config.php` se o usuário e a senha do MySQL estão corretos (no XAMPP, o padrão é `root` sem senha).
5. Acesse http://localhost/WattGYM


## Usuários de teste

Já vêm no `database/WattGYM.sql` (dados fictícios, senha `123456`):

| E-mail | Perfil |
|--------|--------|
| `admin@teste.com` | admin (acessa o Dashboard e a gestão de usuários) |
| `maria@teste.com` | atendente |
| `carlos@teste.com` | cliente |

Os usuários da loja que existiam antes da sprint (ids 1 a 6) não têm senha e não conseguem entrar. Em um servidor de verdade, apague os usuários de teste. Mais detalhes em [DOCS/instalacao.md](DOCS/instalacao.md).

## Estrutura

```
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
├── usuarios.php       # lista, busca e confirmação de exclusão (admin)
├── usuario_novo.php   # cadastro de usuário pelo admin
├── usuario_excluir.php # baixa do usuário (só POST, admin)
├── header.php
├── footer.php
├── includes/
│   ├── config.php     # conexão com o banco (PDO)
│   └── funcoes.php    # admin, CSRF, auditoria e avisos
├── assets/
│   ├── CSS/style.css
│   ├── JS/script.js
│   └── IMG/
├── database/
│   └── WattGYM.sql    # banco completo: estrutura e dados de teste
|
├── docs/              # documentação (requisitos, banco, telas, instalação, manual)
├── README.md
└── CHANGELOG.md
```

## Documentação

- Requisitos: [DOCS/requisitos.md](DOCS/requisitos.md)
- Banco de dados (DER e dicionário de dados): [DOCS/banco-de-dados.md](DOCS/banco-de-dados.md)
- Relatório da Sprint 01 (gestão de usuários) e testes: [DOCS/sprint01-relatorio.md](DOCS/sprint01-relatorio.md)
- Telas e fluxos: [DOCS/telas-e-fluxos.md](DOCS/telas-e-fluxos.md)
- Guia de instalação: [DOCS/instalacao.md](DOCS/instalacao.md)
- Manual do usuário: [DOCS/manual-usuario.md](DOCS/manual-usuario.md)
- Histórico de versões: [CHANGELOG.md](CHANGELOG.md)

## Equipe

- João Gabriel - back-end (PHP), front-end, banco de dados e documentação

Projeto do curso técnico em Desenvolvimento de Sistemas — ETE Porto Digital, 2026.
