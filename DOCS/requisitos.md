# Requisitos

Combinado entre quem pede e quem desenvolve: o que o sistema faz, como ele precisa se comportar e quais regras do negócio ele segue.

**Situação:** ✅ pronto · 🔧 em desenvolvimento · 📋 planejado

## Requisitos funcionais (RF)

O que o sistema faz.

| Código | Descrição | Prioridade | Situação |
|--------|-----------|------------|----------|
| RF01 | O sistema deve permitir que o visitante se cadastre com nome, data de nascimento, e-mail e senha. | Alta | ✅ |
| RF02 | O sistema deve permitir que o usuário cadastrado entre (login) e saia (logout) do sistema. | Alta | ✅ |
| RF03 | O sistema deve exibir o catálogo de produtos organizado por categoria (suplementos, creatinas, pré-treinos, vitaminas e acessórios), com preço e imagem. | Alta | ✅ |
| RF04 | O sistema deve permitir que o usuário logado edite seus dados pessoais (nome, e-mail, CPF e telefone) e seu endereço de entrega. | Média | ✅ |
| RF05 | O sistema deve permitir que o visitante envie uma mensagem pela página de contato, e guardar essa mensagem para a loja. | Média | ✅ |
| RF06 | O sistema deve mostrar ao administrador um painel com o total de pedidos, produtos, clientes, o faturamento e os pedidos em aberto. | Média | ✅ |
| RF07 | O sistema deve permitir que o usuário adicione produtos ao carrinho. | Alta | 📋 |
| RF08 | O sistema deve permitir que o usuário finalize o pedido, escolhendo o endereço de entrega e a forma de pagamento. | Alta | 📋 |
| RF09 | O sistema deve permitir que o usuário aplique um cupom de desconto no pedido. | Baixa | 📋 |
| RF10 | O sistema deve permitir que o administrador altere o status dos pedidos. | Alta | 📋 |
| RF11 | O sistema deve permitir que o usuário busque produtos pelo nome. | Média | 📋 |
| RF12 | O sistema deve mostrar ao usuário o histórico dos seus pedidos. | Média | 📋 |
| RF13 | O sistema deve listar para o administrador os usuários ativos, com busca por nome ou e-mail. | Alta | ✅ |
| RF14 | O sistema deve mostrar, na lista de usuários, o perfil e o setor de cada usuário, lidos das tabelas relacionadas (JOIN). | Alta | ✅ |
| RF15 | O sistema deve permitir que o administrador cadastre um usuário escolhendo o perfil e o setor em listas vindas do banco. | Alta | ✅ |
| RF16 | O sistema deve permitir que o administrador exclua um usuário depois de uma confirmação explícita (exclusão lógica, status Baixa). | Alta | ✅ |
| RF17 | O sistema deve registrar na auditoria os cadastros e as baixas de usuários, com autor, data e hora e resultado. | Média | ✅ |

## Requisitos não funcionais (RNF)

Como o sistema precisa ser: segurança, tecnologia, aparência.

| Código | Descrição | Situação |
|--------|-----------|----------|
| RNF01 | As senhas devem ser guardadas criptografadas com `password_hash()` e conferidas com `password_verify()`. | ✅ |
| RNF02 | O sistema deve ser feito em PHP 8 e MySQL/MariaDB, com acesso ao banco por PDO e consultas preparadas (proteção contra SQL injection). | ✅ |
| RNF03 | Páginas restritas (perfil e dashboard) só podem ser abertas por quem está logado. O dashboard exige, além disso, perfil de administrador. | ✅ |
| RNF04 | Mensagens de erro técnico (banco de dados) não podem aparecer para o usuário. Elas vão apenas para o log do servidor. | ✅ |
| RNF05 | Os textos digitados pelo usuário devem ser tratados com `htmlspecialchars()` antes de aparecer na tela. | ✅ |
| RNF06 | O sistema deve funcionar em celulares (layout responsivo). | 📋 |
| RNF07 | Ações que alteram dados de usuários (cadastro pelo administrador e exclusão) só são aceitas por POST com token CSRF válido. | ✅ |
| RNF08 | Toda validação é feita também no servidor, mesmo que o navegador já valide. Dados enviados manualmente são verificados do mesmo jeito. | ✅ |
| RNF09 | A auditoria não guarda senha, e-mail nem outros dados pessoais: só ids, a ação e o resultado. | ✅ |

## Regras de negócio (RN)

As "leis" do mundo real que o sistema precisa respeitar.

| Código | Descrição | Situação |
|--------|-----------|----------|
| RN01 | Cada e-mail só pode ter uma conta. O mesmo vale para o CPF, quando informado. | ✅ |
| RN02 | A senha deve ter pelo menos 6 caracteres. | ✅ |
| RN03 | Só administradores acessam o dashboard. Clientes são levados para a página inicial. | ✅ |
| RN04 | O faturamento conta apenas pedidos com status `Pago`, `Em Transporte` ou `Entregue`. Pedidos `Pendente` e `Cancelado` ficam de fora. | ✅ |
| RN05 | O pedido segue os status: `Pendente`, `Pago`, `Em Transporte`, `Entregue` ou `Cancelado`. | ✅ |
| RN06 | O preço do produto é copiado para o item no momento da compra (`itens_pedido.preco_unitario`), porque o preço do catálogo pode mudar e o pedido antigo não pode mudar junto. | 📋 |
| RN07 | Um cupom só vale se estiver ativo e dentro da data de validade. | 📋 |
| RN08 | Produto com estoque zero não pode ser comprado. | 📋 |
| RN09 | Usuário em Baixa não aparece na lista de ativos e não consegue entrar. O registro e o histórico continuam no banco. | ✅ |
| RN10 | O administrador não pode excluir o próprio usuário. | ✅ |
| RN11 | O perfil de um usuário só é definido por um administrador. O cadastro feito pelo site cria sempre o perfil `cliente`. | ✅ |
| RN12 | Repetir a exclusão de um usuário já em Baixa não faz uma segunda baixa: o sistema avisa e registra a tentativa na auditoria. | ✅ |
| RN13 | Perfil e setor escolhidos no cadastro precisam existir e estar ativos. | ✅ |

## Histórias de usuário

Formato: **Como** [quem], **quero** [o quê], **para** [por quê].

### HU01 — Criar conta e entrar

**Como** visitante, **quero** criar uma conta e entrar no site, **para** acompanhar minhas compras e guardar meus dados.

Critérios de aceitação:
- Na página de cadastro, preencho nome, data de nascimento, e-mail, senha e confirmação da senha.
- Se as senhas forem diferentes, o sistema avisa e não cria a conta.
- Se o e-mail já estiver cadastrado, o sistema avisa.
- Depois de cadastrado, entro em "Login" com e-mail e senha e vou para a página inicial.
- Se errar o e-mail ou a senha, aparece "E-mail ou senha incorretos".
- Ao clicar em "Sair", saio do sistema e volto para o login.

### HU02 — Manter meus dados e meu endereço

**Como** cliente, **quero** editar meus dados e meu endereço, **para** receber meus pedidos no lugar certo.

Critérios de aceitação:
- A página "Perfil" só abre se eu estiver logado.
- Os campos já aparecem preenchidos com o que está salvo.
- Posso alterar nome, e-mail, CPF, telefone e endereço.
- O CPF e o CEP são formatados enquanto eu digito.
- Se eu preencher parte do endereço, o sistema pede os campos que faltam.
- Ao salvar, aparece "Alterações salvas com sucesso!".

### HU03 — Acompanhar a loja

**Como** administrador, **quero** ver um painel com os números da loja, **para** saber quantos pedidos estão em aberto e quanto foi faturado.

Critérios de aceitação:
- O dashboard só abre para administradores. Um cliente que tentar entrar é levado para a página inicial.
- O painel mostra o total de pedidos, produtos, clientes e o faturamento.
- A lista "Pedidos Restantes" mostra só pedidos `Pendente`, `Pago` ou `Em Transporte`, com cliente, data, valor e status.
- Cada status aparece com uma cor diferente.

### HU06 — Gerenciar usuários

**Como** administrador, **quero** cadastrar, consultar e excluir usuários, vendo o perfil e o setor de cada um, **para** manter os registros consistentes e evitar remoções acidentais.

Critérios de aceitação:
- A lista mostra só usuários ativos, com perfil e setor, e uma mensagem quando a busca não encontra ninguém.
- A busca por nome ou e-mail funciona e trata o texto digitado como dado, nunca como comando.
- No cadastro, as opções de perfil e setor vêm do banco, e erros aparecem embaixo de cada campo.
- Ao clicar em "Excluir", aparece a pergunta "Deseja realmente excluir este usuário?" com o nome e o ID, e os botões Cancelar e Confirmar.
- Cancelar não altera nada. Confirmar coloca o usuário em Baixa, ele some da lista e a ação fica na auditoria.
- A exclusão não funciona por link (GET), sem token CSRF ou com ID inválido.

### HU04 — Comprar produtos *(planejada)*

**Como** cliente, **quero** colocar produtos no carrinho e finalizar o pedido, **para** receber os produtos em casa.

Critérios de aceitação:
- Ao clicar em "Comprar", o produto vai para o carrinho.
- No carrinho, vejo os itens, as quantidades e o valor total.
- Ao finalizar, escolho o endereço de entrega e a forma de pagamento.
- O pedido aparece no meu histórico com o status "Pendente".

### HU05 — Atualizar o andamento dos pedidos *(planejada)*

**Como** administrador, **quero** mudar o status de um pedido, **para** o cliente saber em que etapa ele está.

Critérios de aceitação:
- Na lista de pedidos do dashboard, escolho o novo status de cada pedido.
- Os status possíveis são: Pendente, Pago, Em Transporte, Entregue e Cancelado.
- O faturamento do painel se atualiza conforme o status (RN04).
