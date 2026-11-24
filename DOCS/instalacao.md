# Instalação

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
6. Ainda com o banco `wattgym` selecionado, vá em **Importar** de novo e escolha o arquivo `database/sprint01.sql`. Ele cria as colunas e tabelas que o site usa (senha, perfis, status do usuário, auditoria e mensagens de contato) e os usuários de teste. Pode ser importado mais de uma vez sem dar erro.
7. Abra o arquivo `includes/config.php` e confira os dados de acesso ao banco (tabela abaixo).
8. Acesse http://localhost/WattGYM

## Configuração (`includes/config.php`)

| Variável | Valor padrão no XAMPP |
|----------|-----------------------|
| `$host` | `localhost` |
| `$dbname` | `wattgym` |
| `$username` | `root` |
| `$password` | (vazio) |

## Usuários de teste

O `sprint01.sql` cria três usuários fictícios, todos com a senha `123456`:

| E-mail | Perfil | Para que serve |
|--------|--------|----------------|
| `admin@teste.com` | admin | Acessa o Dashboard e a gestão de usuários |
| `maria@teste.com` | atendente | Perfil intermediário, sem acesso ao painel |
| `carlos@teste.com` | cliente | Cliente comum |

Os usuários que já vêm no `WattGYM.sql` original não têm senha, então não conseguem entrar. Para criar o seu próprio administrador, cadastre uma conta em `cadastro.php` e rode (trocando pelo seu e-mail):

```sql
UPDATE usuarios
SET id_perfil = (SELECT id_perfil FROM perfis WHERE nome = 'admin')
WHERE email = 'seu@email.com';
```

Depois, saia e entre de novo.

## Teste

Entre em http://localhost/WattGYM/login.php com `admin@teste.com` e a senha `123456`. Se você cair no **Dashboard**, com os números de pedidos, produtos e clientes, está tudo certo. Depois, abra **Usuários** no menu: a lista deve mostrar os usuários com o perfil e o setor de cada um.

Para testar um cliente comum, entre com `carlos@teste.com`. Ele vai para a página inicial, e os links "Dashboard" e "Usuários" não aparecem no menu.

## Problemas comuns

| Erro | Causa | Solução |
|------|-------|---------|
| "Não foi possível conectar ao sistema" | Banco `wattgym` não existe, MySQL desligado ou senha do MySQL diferente | Confira se o MySQL está com **Start** no XAMPP, se o banco foi criado (passo 4) e os dados de `includes/config.php` |
| "Unknown column" (como `senha`, `id_perfil` ou `status`) ou "Table ... doesn't exist" (como `perfis`) | O `sprint01.sql` do passo 6 não foi importado | Importe o `database/sprint01.sql` |
| Erro de sintaxe perto de `IF NOT EXISTS` ao importar o `sprint01.sql` | Banco MySQL puro (o script usa recursos do MariaDB, que é o do XAMPP) | Use o MariaDB do XAMPP |
| "Duplicate entry '' for key 'cpf'" ao cadastrar | A coluna `cpf` ainda não aceita `NULL` | Importe o `sprint01.sql` (ele corrige isso) |
| "Call to undefined function mb_..." | Extensão `mbstring` desligada no PHP | No XAMPP ela já vem ligada. Se desligou, ative `extension=mbstring` no `php.ini` e reinicie o Apache |
| "E-mail ou senha incorretos" com um usuário que já veio no banco | Esses usuários não têm senha | Crie uma conta nova pelo cadastro |
| Não abre o Dashboard nem a lista de Usuários | A conta não é administradora, ou está em Baixa | Entre com `admin@teste.com` ou rode o `UPDATE` da seção "Usuários de teste" e entre de novo |
| Imagens e estilos não aparecem | A pasta `assets` (com `CSS`, `JS` e `IMG`) ficou de fora da cópia | Copie a pasta `WattGYM` inteira |
| Apache não inicia | Porta 80 ocupada (Skype, IIS) | Feche o programa que usa a porta ou mude a porta do Apache |
| Acentos aparecem errados | Banco criado sem `utf8mb4` | Recrie o banco com o agrupamento `utf8mb4_general_ci` e importe de novo |

## Segurança

Os dados deste guia (`root` sem senha) valem só para o XAMPP no computador de teste. Em um servidor de verdade, use um usuário e uma senha próprios do banco, e nunca coloque essa senha no GitHub nem na documentação.
