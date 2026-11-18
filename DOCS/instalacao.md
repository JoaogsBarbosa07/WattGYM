# Instalação do WattGYM

Guia para colocar o sistema para rodar em outro computador, do zero.

## Requisitos

- XAMPP 8.2 ou superior (Apache + PHP 8.2 + MySQL/MariaDB)
- Navegador atualizado (Chrome, Firefox ou Edge)

## Passo a passo

1. Abra o XAMPP Control Panel e clique em **Start** no **Apache** e no **MySQL**.
2. Copie a pasta `WattGYM` para `C:\xampp\htdocs\`.
3. Acesse http://localhost/phpmyadmin
4. Clique em **Novo** e crie o banco `wattgym`, com o agrupamento `utf8mb4_general_ci`. O nome precisa ser exatamente esse, em minúsculas.
5. Com o banco `wattgym` selecionado no menu da esquerda, vá em **Importar**, escolha o arquivo `database/WattGYM.sql` e clique em **Importar** (ou **Executar**).
6. Ainda com o banco selecionado, abra a aba **SQL**, cole o comando abaixo e clique em **Executar**. Ele cria as colunas e a tabela que o site usa e que não estão no arquivo do passo 5:


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


7. Abra o arquivo `includes/config.php` e confira os dados de acesso ao banco (tabela abaixo).
8. Acesse http://localhost/WattGYM

## Configuração (`includes/config.php`)

| Variável | Valor padrão no XAMPP |
|----------|-----------------------|
| `$host` | `localhost` |
| `$dbname` | `wattgym` |
| `$username` | `root` |
| `$password` | (vazio) |

## Criando o administrador

O painel (`dashboard.php`) só abre para administradores. Os usuários que já vêm no banco não têm senha, então não conseguem entrar. Para criar o seu acesso:

1. Em http://localhost/WattGYM/cadastro.php, crie uma conta nova (a senha precisa ter pelo menos 6 caracteres).
2. No phpMyAdmin, aba **SQL** do banco `wattgym`, rode (trocando pelo e-mail que você usou):


UPDATE usuarios SET tipo = 'admin' WHERE email = 'seu@email.com';


3. Se já estava logado, clique em **Sair** e entre de novo.

## Teste

Entre em http://localhost/WattGYM/login.php com a conta de administrador criada acima. Se você cair no **Dashboard**, com os números de pedidos, produtos e clientes, está tudo certo.

Para testar um cliente comum, cadastre outra conta. Ao entrar, ela vai para a página inicial, e o link "Dashboard" não aparece no menu.

## Problemas comuns

| Erro | Causa | Solução |
|------|-------|---------|
| "Não foi possível conectar ao sistema" | Banco `wattgym` não existe, MySQL desligado ou senha do MySQL diferente | Confira se o MySQL está com **Start** no XAMPP, se o banco foi criado (passo 4) e os dados de `includes/config.php` |
| "Unknown column 'senha'" ou "Unknown column 'tipo'" | O comando do passo 6 não foi executado | Rode o passo 6 |
| "Table 'wattgym.mensagens_contato' doesn't exist" | O `CREATE TABLE` do passo 6 não foi executado | Rode o passo 6 |
| "Duplicate entry '' for key 'cpf'" ao cadastrar | A coluna `cpf` ainda não aceita `NULL` | Rode o `MODIFY COLUMN cpf` do passo 6 |
| "E-mail ou senha incorretos" com um usuário que já veio no banco | Esses usuários não têm senha | Crie uma conta nova pelo cadastro |
| Não abre o Dashboard | A conta não é administradora | Rode o `UPDATE` da seção "Criando o administrador" e entre de novo |
| Imagens e estilos não aparecem | A pasta `assets` (com `CSS`, `JS` e `IMG`) ficou de fora da cópia | Copie a pasta `WattGYM` inteira |
| Apache não inicia | Porta 80 ocupada (Skype, IIS) | Feche o programa que usa a porta ou mude a porta do Apache |
| Acentos aparecem errados | Banco criado sem `utf8mb4` | Recrie o banco com o agrupamento `utf8mb4_general_ci` e importe de novo |
| Erro de "Duplicate column name" no passo 6 | O comando foi executado duas vezes | Tudo bem: aquela coluna já existe. Rode só os comandos que faltam |

## Segurança

Os dados deste guia (`root` sem senha) valem só para o XAMPP no computador de teste. Em um servidor de verdade, use um usuário e uma senha próprios do banco, e nunca coloque essa senha no GitHub nem na documentação.