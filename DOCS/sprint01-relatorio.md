# Relatório da Sprint 01 — Cadastro e gestão de usuários

| Campo | Preenchimento |
|-------|---------------|
| Projeto / sistema | WattGYM |
| Aluno ou equipe | Joao Gabriel |
| Turma / disciplina | DS-2A |
| Professor(a) | Luiz Carlos |
| Data de início / prazo | não lembro |
| Versão do código entregue | 0.3.0 (ver `CHANGELOG.md`) |

## Resumo

O WattGYM ganhou um módulo de gestão de usuários, só para administradores: lista com busca, cadastro com perfil e setor vindos do banco, e exclusão lógica com confirmação, proteção CSRF e auditoria. As telas e o fluxo de login que já existiam continuam funcionando.

**Como os testes foram executados.** Os testes abaixo foram feitos contra um MariaDB real (10.11), com PHP 8.3, simulando as requisições HTTP de um administrador, de um cliente e de um visitante, sobre o dump original do WattGYM + `database/sprint01.sql`. O resultado completo, teste por teste, está em `docs/evidencias/testes-sprint01.txt` (**41 verificações, 0 falhas, nenhum aviso do PHP**).

**O que esses testes não cobrem:** o visual (CSS, imagens) e o clique nos botões em um navegador. A equipe precisa repetir o roteiro da seção "Roteiro de teste manual" no XAMPP, tirar os prints e anexar como evidência.

## Decisões de projeto

| Decisão | Motivo |
|---------|--------|
| O "setor" do modelo da sprint é a tabela `lojas`, ligada por `usuarios.id_loja` | O projeto já tinha `lojas`. A sprint aceita tabelas equivalentes, e foi só acrescentada a coluna `ativo` |
| `perfis` é uma tabela nova, e `usuarios.tipo` foi trocada por `usuarios.id_perfil` | Um texto livre não garante integridade. A FK garante que o perfil exista |
| O setor é opcional no banco e obrigatório no formulário do administrador | Quem se cadastra pelo site não tem setor, e não pode sumir da lista por isso (`LEFT JOIN`) |
| O cadastro público não deixa escolher o perfil | Senão qualquer visitante poderia se cadastrar como admin (teste E03) |
| A caixa de confirmação é gerada pelo servidor (`?excluir=ID`), sem JavaScript | Funciona em qualquer navegador, não depende de script e o ID mostrado é o mesmo que será enviado |
| O acesso do administrador é conferido no banco a cada página | Quem é colocado em Baixa perde o acesso na hora, mesmo com a sessão aberta (teste E04) |

## Os dez pontos de entrega

| Ponto | Demanda | Arquivos | Resultado |
|-------|---------|----------|-----------|
| 01 | Diagnóstico e organização | `docs/telas-e-fluxos.md`, `docs/banco-de-dados.md`, seção "Estado inicial" abaixo | ☑ Atendeu |
| 02 | Estrutura MySQL e integridade | `database/sprint01.sql`, `docs/der.png` | ☑ Atendeu |
| 03 | Conexão PDO | `includes/config.php` | ☑ Atendeu |
| 04 | Selects abastecidos pelo banco | `usuario_novo.php` | ☑ Atendeu |
| 05 | Formulário e validação | `usuario_novo.php` | ☑ Atendeu |
| 06 | INSERT real e resposta | `usuario_novo.php`, `cadastro.php` | ☑ Atendeu |
| 07 | SELECT, listagem e busca | `usuarios.php` | ☑ Atendeu |
| 08 | JOIN usuários, setores e perfis | `usuarios.php` | ☑ Atendeu |
| 09 | Confirmação antes de excluir | `usuarios.php`, `usuarios.css` | ☑ Atendeu |
| 10 | Exclusão segura, auditoria e entrega | `usuario_excluir.php`, `includes/funcoes.php` | ☑ Atendeu |

"Atendeu" significa que o comportamento foi verificado na bateria de testes. Os prints de tela continuam pendentes (ver "Pendências").

### Ponto 01 — Diagnóstico

**Estado inicial (antes da sprint):**

| Arquivo | Situação encontrada | O que foi feito |
|---------|---------------------|-----------------|
| `cadastro.php`, `login.php` | Funcionam, sem relação com perfil ou status | Mantidos. O cadastro passou a gravar o perfil `cliente` e a auditoria. O login passou a recusar usuários em Baixa |
| `perfil.php`, `contato.php`, `produtos.php`, `inicio.php`, `sobre.php`, `logout.php` | Fora do escopo | Preservados, sem alteração de comportamento |
| `dashboard.php` | Usava `usuarios.tipo` | Troca para `perfis`. O resto do painel não mudou |
| `header.php` | Menu | Só ganhou o link "Usuários" para administradores |
| `includes/config.php` | Conexão PDO | Mantido |
| Banco | Sem perfis, status, auditoria nem vínculo de usuário com setor | Criados pelo `sprint01.sql` |

**Novos arquivos:** `usuarios.php`, `usuario_novo.php`, `usuario_excluir.php`, `includes/funcoes.php`, `usuarios.css`, `database/sprint01.sql`.

### Ponto 02 — Estrutura MySQL

Chaves estrangeiras criadas: `usuarios.id_perfil → perfis`, `usuarios.id_loja → lojas`, `auditoria_usuarios.id_usuario_alvo → usuarios` e `auditoria_usuarios.id_autor → usuarios`. O e-mail é `UNIQUE`. O script traz 3 perfis e 3 usuários fictícios e já encontra as 2 lojas do banco original. As colunas e as relações estão descritas em `docs/banco-de-dados.md`.

- Teste positivo: o script rodou sobre o dump original e também sobre um banco que já tinha os ajustes antigos (coluna `tipo`), duas vezes seguidas, sem erro. O usuário que era `admin` continuou admin.
- Teste de limite: e-mail repetido é rejeitado (T03).

### Ponto 03 — Conexão PDO

`includes/config.php` centraliza o acesso, usa consultas preparadas reais (`ATTR_EMULATE_PREPARES` desligado) e não guarda segredo (só o padrão do XAMPP). Teste de falha controlada (P03): ao trocar o nome do banco, a página responde HTTP 500 com a mensagem "Não foi possível conectar ao sistema. Tente novamente mais tarde.", sem nenhum detalhe técnico. O erro verdadeiro vai para o log do servidor.

### Pontos 04 a 06 — Formulário, validação e INSERT

- Os selects vêm de `SELECT ... WHERE ativo = 1`. Um setor criado no banco aparece no formulário, e um setor inativo some (P04, P04b, P04c). Sem perfil ou setor ativo, o formulário não é mostrado e a página explica o motivo (E05).
- A validação do servidor cobre nome, e-mail, data, senha, e se perfil e setor existem e estão ativos. Cada campo mostra o próprio erro, e os valores digitados voltam preenchidos, menos a senha (T02, T02b, T04).
- O INSERT é preparado, com `password_hash()`. E-mail repetido é tratado antes do INSERT e também pelo código de erro 23000, para o caso de duas pessoas cadastrarem ao mesmo tempo (T03, T03b). Depois de gravar, a lista abre já filtrando o usuário novo (T01, P06).

### Pontos 07 e 08 — Listagem, busca e JOIN

A consulta, as condições `ON` e o motivo de não duplicar os nomes em `usuarios` estão em `docs/banco-de-dados.md`, na seção "O JOIN de usuários, perfis e setores". Resumo: `JOIN perfis ON p.id_perfil = u.id_perfil` (todo usuário tem perfil) e `LEFT JOIN lojas ON l.id_loja = u.id_loja` (o setor é opcional).

- T05 e T05b: busca por nome e por e-mail. T05c: zero resultados mostra mensagem.
- T06: o JOIN mostra o perfil e o setor certos. T07: ao renomear um setor no banco, a lista mostra o nome novo, e `usuarios` não tem nenhuma coluna com nome de setor ou perfil.

### Pontos 09 e 10 — Exclusão, confirmação e auditoria

Fluxo descrito em `docs/telas-e-fluxos.md`. A baixa é feita com `UPDATE ... WHERE id_usuario = :id AND status = 'Ativo'` na mesma transação da auditoria, então repetir o pedido não muda nada e não gera baixa duplicada (T11). As recusas que acontecem antes de mexer no banco devolvem códigos HTTP próprios: 405 (GET), 403 (sem token) e 400 (id inválido).

## Matriz de testes obrigatórios

Resultado real: `docs/evidencias/testes-sprint01.txt`. Responsável e data: Joao Gabriel | 21/11/2026 (execução automatizada em 19/11/2026).

| ID | Cenário | Resultado esperado | Resultado real |
|----|---------|--------------------|----------------|
| T01 | Cadastrar usuário válido | Um registro e sucesso | ✅ Atendeu (um registro, senha com hash, auditoria `CADASTRO/Sucesso`) |
| T02 | E-mail inválido e campo vazio | Recusa, erro por campo, nada gravado | ✅ Atendeu (6 erros mostrados, 0 registros) |
| T03 | E-mail repetido | Sem duplicidade, aviso legível | ✅ Atendeu (aviso mostrado, sem texto de SQL) |
| T04 | ID de setor/perfil inexistente | Servidor bloqueia | ✅ Atendeu (999, `abc` e `-1` bloqueados) |
| T05 | Buscar existente e inexistente | Lista e trata vazio | ✅ Atendeu |
| T06 | JOIN de três tabelas | Usuário com setor e perfil corretos | ✅ Atendeu |
| T07 | Alterar dado de referência | JOIN mostra o novo valor, sem duplicar | ✅ Atendeu |
| T08 | Excluir e Cancelar | Não altera banco nem status | ✅ Atendeu (continua Ativo, 0 linhas de auditoria) |
| T09 | Excluir e confirmar | Baixa, sai dos ativos, auditoria | ✅ Atendeu (`Baixa`, `baixado_em` preenchido, auditoria `BAIXA/Sucesso`) |
| T10 | GET, sem CSRF, ID inválido | Servidor rejeita e preserva | ✅ Atendeu (405, 403, 400; cliente e visitante barrados) |
| T11 | Repetir a confirmação | Sem baixa duplicada | ✅ Atendeu (1 baixa, aviso, rejeição auditada) |
| T12 | Caracteres especiais e injeção | Entrada tratada como dado | ✅ Atendeu (`' OR '1'='1`, `%`, `<script>` e `DROP TABLE` tratados como texto) |

**Testes extras:** E01 e E02 (admin tentando excluir a si mesmo, pela tela e por POST forjado), E03 (cadastro público tentando virar admin), E04 (admin em Baixa perde o acesso com a sessão aberta), E05 (tabela de perfis vazia), P05 (cadastro sem token CSRF) e P10 (usuário em Baixa não consegue entrar). Todos passaram.

## Roteiro de teste manual (para repetir no XAMPP)

1. Importe `database/WattGYM.sql` e depois `database/sprint01.sql`.
2. Entre com `admin@teste.com` / `123456`. Abra **Usuários**: a lista deve mostrar perfil e setor.
3. Busque por `maria` (acha), por `zzz` (mensagem de zero resultados) e por `' OR '1'='1` (zero resultados).
4. **Novo usuário**: envie vazio (erros por campo), depois com e-mail repetido (aviso), depois válido (volta à lista com o usuário novo).
5. No banco, renomeie uma loja (`UPDATE lojas SET nome_fantasia = 'Teste' WHERE id_loja = 1`) e recarregue a lista.
6. **Excluir** → **Cancelar**: o usuário continua na lista. **Excluir** → **Confirmar**: ele some, e `SELECT * FROM auditoria_usuarios` mostra a baixa.
7. Abra `http://localhost/WattGYM/usuario_excluir.php` direto no navegador: deve mostrar "Método não permitido".
8. Saia e entre com `carlos@teste.com`: o menu não mostra **Usuários**, e `usuarios.php` leva para a página inicial.

## Pendências e impactos

| Pendência | Impacto | Responsável | Versão |
|-----------|---------|-------------|--------|
| Tirar os prints das telas (`usuarios`, `usuario-novo`, `confirmar-exclusao`) e repetir o roteiro manual no XAMPP | A sprint não aceita item entregue só por teste automatizado. Falta a demonstração visual | Equipe | 0.3.1 |
| Colar `usuarios.css` no `style.css` | Sem isso, a página funciona mas sem o estilo da tabela e da caixa de confirmação | Equipe | 0.3.1 |
| Não existe tela para reativar um usuário em Baixa | Só quem tem acesso ao banco desfaz uma baixa | Equipe | futura |
| O perfil `atendente` existe, mas não tem permissões próprias | Só `admin` acessa a gestão | Equipe | futura |
| O `sprint01.sql` usa `ADD COLUMN IF NOT EXISTS`, que é do MariaDB | Funciona no XAMPP, mas não em MySQL puro | Equipe | futura |
| A lista mostra no máximo 200 usuários, sem paginação | Irrelevante para o volume atual | Equipe | futura |
| Não há limite de tentativas de login nem recuperação de senha | Fora do escopo desta sprint | Equipe | futura |

## Pacote final

- Antes de zipar, remova arquivos soltos e duplicados (`sobre_1.php`, `wattgym_1_.sql` etc.), e confira se não há senha real ou dado pessoal real em nenhum arquivo. Os usuários de teste são fictícios.
- A estrutura deve ser a real do projeto (ver README).

## Registro final da equipe

| Campo | Preenchimento |
|-------|---------------|
| Resumo da implementação | Módulo de gestão de usuários (lista, busca, cadastro e exclusão lógica com confirmação, CSRF e auditoria) sobre `perfis`, `lojas` e `auditoria_usuarios` |
| Arquivos adicionados/alterados | Ver "Ponto 01" |
| Consultas SQL / JOIN explicados | `docs/banco-de-dados.md` |
| Exclusão e auditoria verificadas | Testes T08 a T11 e E01 a E04 |
| Testes positivos e negativos | `docs/evidencias/testes-sprint01.txt` |
| Correções realizadas | A primeira execução dos testes falhou em cascata porque o PHP de teste não tinha a extensão `mbstring`: o código passou a funcionar com ou sem ela. Na execução seguinte, só o teste de banco indisponível falhou, e o problema era do próprio script de teste (cache do PHP), não do sistema |
| Pendências e impactos | Ver tabela acima |
| Data / versão / responsáveis | 19/11/2026 · 0.3.0 · Joao Gabriel |
