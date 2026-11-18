## Requisitos funcionais (RF)

O que o sistema faz.

| Código | Descrição | Prioridade |
|--------|-----------|------------|
| RF01   | O sistema deve permitir que o visitante se cadastre com nome, data de nascimento, e-mail e senha.                                                      | Alta 
| RF02   | O sistema deve permitir que o usuário cadastrado entre (login) e saia (logout) do sistema.                                                             | Alta 
| RF03   | O sistema deve exibir o catálogo de produtos organizado por categoria (suplementos, creatinas, pré-treinos, vitaminas e acessórios), com preço e imagem. | Alta 
| RF04   | O sistema deve permitir que o usuário logado edite seus dados pessoais (nome, e-mail, CPF e telefone) e seu endereço de entrega.                           | Média 
| RF05   | O sistema deve permitir que o visitante envie uma mensagem pela página de contato, e guardar essa mensagem para a loja.                                   | Média 
| RF06   | O sistema deve mostrar ao administrador um painel com o total de pedidos, produtos, clientes, o faturamento e os pedidos em aberto.                      | Média 


## Requisitos não funcionais (RNF)

Como o sistema precisa ser: segurança, tecnologia, aparência.

| Código | Descrição |
|--------|-----------|
| RNF01  | As senhas devem ser guardadas criptografadas com `password_hash()` e conferidas com `password_verify()`. 
| RNF02  | O sistema deve ser feito em PHP 8 e MySQL 8, com acesso ao banco por PDO e consultas preparadas (proteção contra SQL injection). 
| RNF03  | Páginas restritas (perfil e dashboard) só podem ser abertas por quem está logado. O dashboard exige, além disso, perfil de administrador.


## Regras de negócio (RN)

As "leis" do mundo real que o sistema precisa respeitar.

| Código | Descrição |
|--------|-----------|
| RN01   | Cada e-mail só pode ter uma conta. O mesmo vale para o CPF, quando informado. 
| RN02   | A senha deve ter pelo menos 6 caracteres. 
| RN03   | Só administradores acessam o dashboard. Clientes são levados para a página inicial. 

## Histórias de usuário

Formato: **Como** [quem], **quero** [o quê], **para** [por quê].

### HU01 — Criar conta e entrar

**Como visitante, quero criar uma conta e entrar no site, para acompanhar minhas compras e guardar meus dados.**

Critérios de aceitação:

- Na página de cadastro, preencho nome, data de nascimento, e-mail, senha e confirmação da senha.
- Se as senhas forem diferentes, o sistema avisa e não cria a conta.
- Se o e-mail já estiver cadastrado, o sistema avisa.
- Depois de cadastrado, entro em "Login" com e-mail e senha e vou para a página inicial.
- Se errar o e-mail ou a senha, aparece "E-mail ou senha incorretos".
- Ao clicar em "Sair", saio do sistema e volto para o login.

### HU02 — Manter meus dados e meu endereço

**Como cliente, quero editar meus dados e meu endereço, para receber meus pedidos no lugar certo.**

Critérios de aceitação:

- A página "Perfil" só abre se eu estiver logado.
- Os campos já aparecem preenchidos com o que está salvo.
- Posso alterar nome, e-mail, CPF, telefone e endereço.
- O CPF e o CEP são formatados enquanto eu digito.
- Se eu preencher parte do endereço, o sistema pede os campos que faltam.
- Ao salvar, aparece "Alterações salvas com sucesso!".

### HU03 — Acompanhar a loja

**Como administrador, quero ver um painel com os números da loja, para saber quantos pedidos estão em aberto e quanto foi faturado.**

Critérios de aceitação:

- O dashboard só abre para administradores. Um cliente que tentar entrar é levado para a página inicial.
- O painel mostra o total de pedidos, produtos, clientes e o faturamento.
- A lista "Pedidos Restantes" mostra só pedidos `Pendente`, `Pago` ou `Em Transporte`, com cliente, data, valor e status.
- Cada status aparece com uma cor diferente.

