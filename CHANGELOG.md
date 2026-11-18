# Changelog

Todas as mudanças importantes do WattGYM ficam registradas aqui, da versão mais nova para a mais antiga.

O número da versão segue o padrão MAIOR.MENOR.CORREÇÃO. Enquanto o sistema não estiver completo (falta o carrinho e o envio de pedidos), as versões começam com 0.

## [Não lançado]

### Planejado
- Carrinho de compras e finalização de pedidos 
- Cupom de desconto e histórico de pedidos 
- Barra de pesquisa 
- Alteração do status dos pedidos pelo dashboard 
- Catálogo lendo os produtos direto do banco.
- Layout responsivo para celular 

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