# Banco de dados — WattGYM

Banco `wattgym` (MySQL 8, charset `utf8mb4`, mecanismo InnoDB). Script de criação: `database/WattGYM.sql`.

## DER

![DER do WattGYM](der.png)

Como ler: `PK` é a chave primária, `FK` é a chave estrangeira e `UK` é um valor que não pode se repetir. Cada linha liga o lado **1** ao lado **N**.

- Um **usuário** tem vários **endereços** e faz vários **pedidos**.
- Cada **pedido** é entregue em um **endereço** e pode usar um **cupom** (opcional).
- Um **pedido** contém vários **itens**, e um **produto** aparece em vários itens. A tabela `itens_pedido` é a ponte entre `pedidos` e `produtos` (relação N:N).
- Um **pedido** pode ter vários **pagamentos**.
- Cada **produto** pertence a uma **loja** e a uma **categoria**.
- `mensagens_contato` não se liga a nenhuma outra tabela: guarda o que chega pela página de contato.

## Resumo das tabelas

| Tabela | Para que serve |
|--------|----------------|
| `usuarios` | Clientes e administradores do site |
| `enderecos` | Endereços de entrega dos usuários |
| `pedidos` | Pedidos feitos pelos usuários |
| `itens_pedido` | Produtos de cada pedido |
| `pagamentos` | Pagamentos de cada pedido |
| `cupons` | Cupons de desconto |
| `produtos` | Catálogo de produtos à venda |
| `categorias` | Categorias dos produtos |
| `lojas` | Lojas vendedoras |
| `mensagens_contato` | Mensagens enviadas pela página de contato |

## Dicionário de dados

### usuarios

Pessoas cadastradas no site. Com `tipo = 'admin'`, a pessoa acessa o dashboard.

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_usuario | INT, AUTO_INCREMENT | Sim | PK | Número único do usuário |
| nome       | VARCHAR(100)        | Sim | —  | Nome completo |
| email      | VARCHAR(255)        | Sim | UK | E-mail usado para entrar no site |
| cpf        | CHAR(11)            | Não | UK | CPF, só números. Fica vazio (`NULL`) até a pessoa preencher no perfil |
| telefone | VARCHAR(15) | Sim | — | Telefone/WhatsApp com DDD, no formato `(81) 99999-9999`. Fica vazio até a pessoa preencher no perfil |
| data_de_nascimento | DATE | Sim | — | Data de nascimento |
| senha | VARCHAR(255) | Sim | — | Senha criptografada com `password_hash()`. Nunca guarda a senha pura |
| tipo | ENUM('cliente','admin') | Sim | — | Perfil de acesso. Padrão: `'cliente'` |
| data_cadastro | DATETIME | Não | — | Data e hora do cadastro. Padrão: horário atual |

### enderecos

Endereços de entrega. Se o usuário for apagado, os endereços dele também são (`ON DELETE CASCADE`).

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_endereco | INT, AUTO_INCREMENT | Sim | PK | Número único do endereço |
| id_usuario | INT | Sim | FK → usuarios.id_usuario | Dono do endereço |
| cep | CHAR(8) | Sim | — | CEP, só números |
| rua | VARCHAR(100) | Sim | — | Nome da rua. Padrão: vazio |
| numero | VARCHAR(10) | Sim | — | Número da casa ou prédio |
| complemento | VARCHAR(50) | Não | — | Apartamento, bloco, "casa 2" etc. |
| bairro | VARCHAR(50) | Sim | — | Bairro |
| cidade | VARCHAR(50) | Sim | — | Cidade |
| uf | CHAR(2) | Sim | — | Sigla do estado, como `PE` |

### pedidos

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_pedido | INT, AUTO_INCREMENT | Sim | PK | Número único do pedido |
| id_usuario | INT | Sim | FK → usuarios.id_usuario | Cliente que fez o pedido |
| id_endereco | INT | Sim | FK → enderecos.id_endereco | Endereço de entrega |
| id_cupom | INT | Não | FK → cupons.id_cupom | Cupom usado. `NULL` se não usou |
| status_pedido | VARCHAR(50) | Não | — | Situação do pedido: `Pendente`, `Pago`, `Em Transporte`, `Entregue` ou `Cancelado`. Padrão: `'Pendente'` |
| valor_total | DECIMAL(10,2) | Sim | — | Valor total em reais, já com desconto |
| data_do_pedido | DATETIME | Não | — | Data e hora do pedido. Padrão: horário atual |

### itens_pedido

Se o pedido for apagado, os itens dele também são (`ON DELETE CASCADE`).

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_item | INT, AUTO_INCREMENT | Sim | PK | Número único do item |
| id_pedido | INT | Sim | FK → pedidos.id_pedido | Pedido a que o item pertence |
| id_produto | INT | Sim | FK → produtos.id_produto | Produto comprado |
| quantidade | INT | Sim | — | Quantas unidades |
| preco_unitario | DECIMAL(10,2) | Sim | — | Preço de uma unidade **no dia da compra**. Fica guardado aqui porque o preço do produto pode mudar depois, e o pedido antigo não pode mudar junto |

### pagamentos

Se o pedido for apagado, os pagamentos dele também são (`ON DELETE CASCADE`).

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_pagamento | INT, AUTO_INCREMENT | Sim | PK | Número único do pagamento |
| id_pedido | INT | Sim | FK → pedidos.id_pedido | Pedido pago |
| forma_pagamento | VARCHAR(50) | Sim | — | `Pix`, `Cartao_Credito`, `Cartao_Debito` ou `Boleto` |
| status_pagamento | VARCHAR(50) | Sim | — | `Pendente`, `Aprovado` ou `Recusado` |
| valor_pago | DECIMAL(10,2) | Sim | — | Valor pago, em reais |
| data_pagamento | DATETIME | Não | — | Data e hora do pagamento. Padrão: horário atual |

### cupons

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_cupom | INT, AUTO_INCREMENT | Sim | PK | Número único do cupom |
| codigo | VARCHAR(30) | Sim | UK | Código digitado pelo cliente, como `WATT10` |
| valor_desconto | DECIMAL(10,2) | Sim | — | Valor do desconto |
| data_validade | DATETIME | Sim | — | Até quando o cupom vale |
| ativo | TINYINT(1) | Não | — | `1` = pode ser usado, `0` = desativado. Padrão: `1` |
| data_cadastro | DATETIME | Não | — | Data e hora de criação. Padrão: horário atual |

### produtos

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_produto | INT, AUTO_INCREMENT | Sim | PK | Número único do produto |
| id_loja | INT | Sim | FK → lojas.id_loja | Loja que vende o produto |
| id_categoria | INT | Sim | FK → categorias.id_categoria | Categoria do produto |
| nome | VARCHAR(100) | Sim | — | Nome exibido no catálogo |
| descricao | TEXT | Sim | — | Descrição do produto |
| estoque | INT | Sim | — | Unidades disponíveis. Padrão: `0` |
| preco | DECIMAL(10,2) | Sim | — | Preço atual, em reais |
| data_cadastro | DATETIME | Não | — | Data e hora do cadastro. Padrão: horário atual |

### categorias

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_categoria | INT, AUTO_INCREMENT | Sim | PK | Número único da categoria |
| nome | VARCHAR(100) | Sim | UK | Nome da categoria, como `Creatinas` |
| descricao | TEXT | Não | — | O que a categoria reúne |

### lojas

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_loja | INT, AUTO_INCREMENT | Sim | PK | Número único da loja |
| nome_fantasia | VARCHAR(100) | Sim | — | Nome da loja |
| cnpj | CHAR(14) | Sim | UK | CNPJ, só números |
| email | VARCHAR(255) | Sim | — | E-mail de contato da loja |
| telefone | VARCHAR(15) | Sim | — | Telefone da loja |
| data_cadastro | DATETIME | Não | — | Data e hora do cadastro. Padrão: horário atual |

### mensagens_contato

Mensagens enviadas pelo formulário da página de contato.

| Coluna | Tipo | Obrigatório | Chave | Descrição |
|--------|------|-------------|-------|-----------|
| id_mensagem | INT, AUTO_INCREMENT | Sim | PK | Número único da mensagem |
| email | VARCHAR(150) | Sim | — | E-mail de quem enviou, para a resposta |
| assunto | VARCHAR(150) | Sim | — | Assunto escrito pela pessoa |
| mensagem | TEXT | Sim | — | Texto da mensagem (até 2000 caracteres no formulário) |
| data_envio | DATETIME | Sim | — | Data e hora do envio. Padrão: horário atual |

## Alterações em relação ao script original

O script `WattGYM.sql` original não tinha as colunas abaixo. Elas são criadas pelos comandos da seção "Como rodar" do `README.md`:

- `usuarios.senha` e `usuarios.tipo`, para o login e para separar clientes de administradores.
- `usuarios.cpf` passou a aceitar `NULL`. A coluna tem índice `UNIQUE`, e o cadastro não pede CPF, então vários usuários sem CPF precisam guardar `NULL` (e não texto vazio, que se repetiria).
- `enderecos.rua`, que o formulário do perfil usa.
- A tabela `mensagens_contato`.

## Padrão de nomes

- Tabelas no plural e em minúsculas: `usuarios`, `pedidos`.
- Colunas em `snake_case`: `data_do_pedido`, `preco_unitario`.
- Chave primária e estrangeira no formato `id_` + nome da tabela no singular: `id_usuario`, `id_produto`.
- Sem acento e sem espaço em nomes de tabelas e colunas.