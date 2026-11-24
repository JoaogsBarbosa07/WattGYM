# Changelog

Todas as mudanças importantes do WattGYM ficam registradas aqui, da versão mais nova para a mais antiga.

O número da versão segue o padrão MAIOR.MENOR.CORREÇÃO. Enquanto o sistema não estiver completo (falta o carrinho e o envio de pedidos), as versões começam com 0.

## [Não lançado]

### Planejado
- Carrinho de compras e finalização de pedidos (RF07, RF08).
- Cupom de desconto e histórico de pedidos (RF09, RF12).
- Barra de pesquisa (RF11).
- Alteração do status dos pedidos pelo dashboard (RF10).
- Catálogo lendo os produtos direto do banco.
- Layout responsivo para celular (RNF06).

## [0.3.0] - 2026-11-19

Sprint 01: gestão de usuários.

### Adicionado
- Página **Usuários** (`usuarios.php`): lista de usuários ativos com perfil e setor (JOIN), busca por nome ou e-mail e mensagem para zero resultados (RF13, RF14).
- Página **Novo usuário** (`usuario_novo.php`): cadastro pelo administrador, com perfil e setor em listas vindas do banco, validação no servidor e erro por campo (RF15).
- Exclusão lógica de usuário (`usuario_excluir.php`): caixa de confirmação com Cancelar e Confirmar, só por POST, com token CSRF, status `Baixa` e data da baixa (RF16).
- Auditoria de cadastros e baixas na tabela `auditoria_usuarios`, sem dados sensíveis (RF17).
- Tabela `perfis` e colunas `status`, `atualizado_em`, `baixado_em`, `id_perfil` e `id_loja` em `usuarios`. `lojas.ativo`.
- `database/sprint01.sql`: script único, que pode ser executado mais de uma vez, com os ajustes do banco e usuários fictícios de teste.
- `includes/funcoes.php` (controle de administrador, CSRF, auditoria, avisos) e `usuarios.css`.
- Documentação: relatório da sprint, evidência dos testes (`docs/evidencias/testes-sprint01.txt`) e atualização do DER, do dicionário de dados, dos requisitos, das telas e do manual.

### Alterado
- A coluna `usuarios.tipo` foi trocada pela chave estrangeira `id_perfil`. O script converte os valores que já existiam.
- O login só aceita usuários com status `Ativo`.
- Dashboard e gestão de usuários conferem no banco, a cada acesso, se o administrador ainda está ativo e continua com o perfil admin.
- O cadastro pelo site sempre cria o perfil `cliente` e registra a ação na auditoria.
- O dashboard conta só clientes ativos.
- O menu do administrador ganhou o link **Usuários**.
- O README e o guia de instalação passam a usar o `sprint01.sql`, e o usuário de teste agora é `admin@teste.com` (senha `123456`).

### Segurança
- Exclusão e cadastro de usuário pelo administrador exigem POST com token CSRF.
- O administrador não consegue excluir o próprio usuário.
- A busca trata `%`, `_` e aspas como texto, e a saída é sempre escapada.

## [0.2.0] - 2026-11-18

### Adicionado
- Login com conferência de senha usando `password_verify()`, e cadastro que grava a senha criptografada com `password_hash()` (RF01, RF02, RNF01).
- Perfil que carrega os dados salvos e grava o endereço na tabela `enderecos`, com máscara de CPF, telefone e CEP (RF04).
- Dashboard com dados reais do banco (pedidos, produtos, clientes, faturamento e pedidos em aberto), restrito a administradores (RF06, RN03, RN04).
- Formulário de contato que salva as mensagens na nova tabela `mensagens_contato` (RF05).
- Catálogo dividido por categoria, com âncoras que o menu usa para rolar até cada seção (RF03).
- Colunas `senha` e `tipo` em `usuarios` e coluna `rua` em `enderecos`.
- Documentação em `docs/`: requisitos, dicionário de dados e DER, telas e fluxos, instalação e manual do usuário.
- Comentários PHPDoc nos arquivos e nas funções.

### Alterado
- `usuarios.cpf` passou a aceitar `NULL`, porque a coluna tem índice `UNIQUE` e o cadastro não pede CPF.
- Conexão com o banco (`config.php`) usa `utf8mb4` e não mostra mais o erro técnico na tela: ele vai para o log (RNF04).
- Menu mostra o link "Dashboard" só para administradores.
- README atualizado para listar só o que já está pronto.

### Corrigido
- O formulário de cadastro enviava para `inicio.php` sem método POST, e o cadastro nunca era processado.
- O login não conferia a senha: bastava o e-mail para entrar.
- O login não tinha a tag `<form>` de abertura, e a mensagem de erro nunca aparecia.
- O `logout.php` não iniciava a sessão antes de destruí-la.
- O `session_start()` do `header.php` rodava depois do HTML, causando o aviso "headers already sent".
- Caminhos diferentes para o `config.php` em cada arquivo (`includes/`, `include/` e raiz).
- O perfil não mostrava os dados salvos e ignorava os campos de endereço.
- O `script.js` usava um id que não existe (`confirma_senha`) e abria uma confirmação ao carregar o perfil, com uma variável indefinida.
- O formulário de contato não tinha `name` nos campos e enviava para a página errada.
- Favicon e ícone do olho apontavam para caminhos sem `assets/`.

### Removido
- `salvar.php`: arquivo de teste que inseria um usuário fixo e listava os e-mails de todos os usuários.

### Segurança
- Mensagem de erro do login não revela se o e-mail existe.
- Textos digitados passam por `htmlspecialchars()` antes de aparecer na tela (RNF05).
- `session_regenerate_id()` depois do login.

## [0.1.0] - data a confirmar pela equipe

### Adicionado
- Estrutura do site em PHP: páginas de início, produtos, sobre, contato, cadastro, login, perfil e dashboard.
- Menu e rodapé compartilhados (`header.php` e `footer.php`) e estilos em `style.css`.
- Banco de dados `wattgym` com as tabelas `usuarios`, `enderecos`, `pedidos`, `itens_pedido`, `pagamentos`, `cupons`, `produtos`, `categorias` e `lojas`.
- Conexão com o banco por PDO.
