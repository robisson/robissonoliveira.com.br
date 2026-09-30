---
title: 'Além do Spec-Driven Development: construindo um sistema de engenharia AI-native'
seoTitle: 'Além do Spec-Driven Development: engenharia AI-native com Build Like Amazon'
description: >-
  Por que coding agents exigem mais do que prompts e specs: uma análise sobre
  Spec Kit, OpenSpec, Kiro, AI-DLC e o Build Like Amazon como control plane de
  engenharia para agentes.
pubDate: 2026-08-10
tags:
  - AI
  - Software Engineering
  - Architecture
  - Agentic Engineering
  - Distributed Systems
series: AI-native Engineering
language: pt-BR
---
**TLDR;** A discussão sobre AI coding está saindo de "como fazer a IA escrever mais código" e entrando em uma pergunta mais difícil: como permitir que agentes executem uma parcela crescente da engenharia sem perder intenção, coerência, qualidade, rastreabilidade e controle? Spec Kit, OpenSpec, Kiro e AI-DLC atacam partes importantes desse problema. O Build Like Amazon Agent Skills começa antes da specification, no problema do cliente, e termina depois do deployment, em operação e aprendizado. Minha tese é que a próxima evolução não é apenas Spec-Driven Development. É um **engineering control plane** para agentes.

![Build Like Amazon Agent Skills](/assets/images/build-like-amazon.png)

Durante muito tempo, quando falávamos sobre inteligência artificial aplicada ao desenvolvimento de software, a conversa ficava presa em produtividade local.

O modelo completava uma função. Explicava um erro. Escrevia um teste. Gerava uma classe. Convertia código de uma linguagem para outra. Ajudava a navegar por uma codebase desconhecida.

Isso já parece uma etapa anterior da evolução.

Os coding agents modernos conseguem permanecer trabalhando por períodos muito maiores, modificar dezenas de arquivos, executar testes, utilizar ferramentas externas, investigar failures, corrigir a própria implementação e decompor problemas relativamente complexos em várias etapas.

À medida que essa capacidade de execução aumenta, a pergunta mais importante deixa de ser:

> Quanto código uma IA consegue escrever?

e passa a ser:

> Como permitimos que agentes executem uma parcela crescente da engenharia sem perder intenção, coerência, qualidade, rastreabilidade e controle?

Essa mudança é mais importante do que parece.

Gerar código é apenas uma pequena parte da engenharia de software. Um sistema real existe dentro de um conjunto muito maior de decisões: requisitos, contratos, princípios arquiteturais, APIs, modelos de dados, expectativas operacionais, políticas de segurança, trade-offs de resiliência, estratégias de rollout, critérios de rollback, testes, runbooks, incidentes anteriores e dezenas de decisões que normalmente vivem distribuídas entre documentos, tickets, pull requests, reuniões e conhecimento tácito.

Um engenheiro experiente não lê somente a tarefa que está na sua frente.

Ele carrega um modelo mental do sistema.

Ele sabe que determinada API já possui consumidores externos. Sabe que uma migration pode ser tecnicamente pequena, mas difícil de reverter. Sabe que uma chamada externa precisa de timeout porque aquela dependência já causou um incidente. Sabe que determinada abstração foi evitada deliberadamente. Sabe que uma mudança aparentemente simples pode alterar o blast radius do serviço.

Um agente não possui esse contexto naturalmente.

Se aumentarmos em uma ordem de magnitude a velocidade com que software pode ser modificado sem aumentar proporcionalmente nossa capacidade de preservar esse contexto, não estaremos apenas acelerando desenvolvimento. Estaremos acelerando também a capacidade de produzir divergência.

É por isso que acredito que a discussão sobre AI coding está migrando rapidamente de **code generation** para **engineering systems**.

E é nesse contexto que ferramentas e metodologias como [GitHub Spec Kit](https://github.github.io/spec-kit/), [OpenSpec](https://openspec.dev/docs), [Kiro](https://kiro.dev/docs/) e [AI-DLC](https://aws.amazon.com/blogs/devops/open-sourcing-adaptive-workflows-for-ai-driven-development-life-cycle-ai-dlc/) ficam interessantes.

À primeira vista, elas parecem diferentes implementações de Spec-Driven Development.

Quando observamos seus mecanismos internos, entretanto, aparece algo mais importante: cada uma está tentando resolver uma parte diferente do mesmo problema.

Spec Kit tenta manter intenção e implementação convergentes.

OpenSpec tenta manter o estado semântico do sistema coerente através do tempo.

Kiro tenta transformar especificações em trabalho executável por agentes, com hooks, tasks e automações conectadas ao ambiente de desenvolvimento.

AI-DLC tenta reorganizar o lifecycle de engenharia ao redor de AI-led execution e human decision-making.

Ao construir o Build Like Amazon Agent Skills, comecei de outro ponto: como transformar práticas públicas associadas à forma Amazon de construir e operar serviços - Working Backwards, written narratives, one-way e two-way doors, API-first thinking, operational excellence, progressive deployment, Correction of Errors e bar raising - em processos que um agente pudesse executar consistentemente.

O resultado começou como uma library de skills.

Mas olhando para onde o projeto chegou, essa descrição já é incompleta.

O que está surgindo é algo mais próximo de um **engineering control plane para agentes**.

Ao revisar essa tese contra a versão `0.4.0`, a mudança ficou mais concreta. O projeto já possui relatórios duráveis de conclusão, um verificador determinístico, revisão de implementação com contexto isolado do autor e métricas do próprio fluxo. Parte da infraestrutura que antes eu imaginava como futuro já existe, embora ainda tenha limites importantes.

Nesta análise, uso como referência o estado da `main` no commit [`107c3f1`](https://github.com/robisson/build-like-amazon-agent-skills/tree/107c3f1), consultado em 30 de setembro de 2026. Os exemplos de arquitetura futura continuam sendo propostas minhas; não representam recursos publicados nem um compromisso de lançamento de uma versão chamada V2.

## O problema não é mais fazer a IA escrever código

O primeiro modelo de desenvolvimento assistido por IA é simples:

```text
Human
  |
  v
Prompt
  |
  v
AI
  |
  v
Code
```

A inteligência está concentrada na conversa.

O humano fornece contexto, descreve o que deseja e corrige o modelo quando ele se desvia. Esse processo funciona muito bem para mudanças pequenas. O problema aparece quando o trabalho ultrapassa aquilo que pode ser mantido coerentemente dentro de uma única interação.

Imagine que a instrução seja simplesmente:

> Adicione pagamentos recorrentes ao sistema.

Existem dezenas de perguntas escondidas nessa frase.

O que significa recorrente? Em que frequência? Existe retry? Como idempotência funciona? Quem controla o schedule? O que acontece quando uma cobrança falha? Existe grace period? O contrato público muda? Existe migration? Como eventos são publicados? Quais métricas definem sucesso? Como fazemos rollout? Como desligamos a feature se o failure rate crescer?

Um modelo suficientemente capaz pode inventar respostas razoáveis.

Esse é precisamente o problema.

Engenharia de produção não é encontrar uma resposta tecnicamente plausível. É encontrar uma resposta consistente com o problema do cliente, as restrições do sistema e as decisões que a organização está disposta a assumir.

Por isso, os frameworks modernos começaram a deslocar o estado para fora da conversa.

Em vez de:

```text
Prompt -> Code
```

surge algo mais próximo de:

```text
Intent
  |
  v
Persistent Engineering State
  |
  v
Agent
  |
  v
Execution
```

O Markdown utilizado por muitos desses sistemas pode parecer trivial. Na realidade, a escolha conceitual é importante.

Requirements, specifications, plans, steering files, constitutions, proposals, task graphs e design documents transformam decisões temporárias em estado durável.

A janela de contexto deixa de ser a única memória do processo.

Essa é provavelmente a fundação comum mais importante de Spec Kit, OpenSpec, Kiro, AI-DLC e Build Like Amazon.

Eles discordam sobre como organizar esse estado, mas concordam implicitamente sobre uma coisa:

> Prompts transitórios são uma abstração insuficiente para engenharia complexa.

## Spec Kit: especificação como contrato e convergência como mecanismo

O GitHub Spec Kit começou associado fortemente ao conceito de Spec-Driven Development, mas sua documentação atual o descreve como um **intent-driven harness** capaz de conduzir diferentes coding agents através do SDLC ou de workflows customizados.

A documentação consultada também apresenta avaliação de ideias e correção de falhas como entradas independentes, oferecidas por extensões opcionais. Portanto, seria incorreto atribuir a ele uma fronteira rígida que só começa quando a intenção já está pronta. Aqui comparo o ciclo principal de especificação com os mecanismos do Build Like Amazon, sem assumir que cada ecossistema termina nesse ciclo.

Seu processo principal continua sendo spec-driven, mas o framework agora possui integrations, extensions, presets, workflows e bundles. Workflows podem combinar comandos, prompts, shell steps, condições, loops, checkpoints humanos e fan-out/fan-in.

O lifecycle agentic padrão pode ser entendido assim:

```text
Constitution
  |
  v
Specify
  |
  v
Clarify
  |
  v
Plan
  |
  v
Checklist
  |
  v
Tasks
  |
  v
Analyze
  |
  v
Implement
  |
  v
Converge
```

A primeira ideia importante é a **Constitution**.

A Constitution contém princípios que deveriam permanecer estáveis através das features. Em vez de redescobrir em cada mudança que determinado projeto exige TDD, backwards compatibility ou uma preferência arquitetural específica, essas regras tornam-se uma camada superior de governança contra a qual as fases posteriores podem ser avaliadas.

Isso separa duas classes diferentes de conhecimento:

```text
Project Invariants
  |
  v
Feature Intent
  |
  v
Implementation
```

A feature possui liberdade dentro dos invariantes, mas não redefine os invariantes implicitamente.

A segunda ideia importante é a separação entre **WHAT** e **HOW**.

O specification phase tenta definir comportamento, necessidade e critérios de sucesso antes que o modelo comece a escolher componentes e tecnologias. O planning phase vem depois, transformando aquilo em arquitetura e abordagem técnica.

Isso combate uma tendência particularmente perigosa de modelos generativos: resolver a solução antes de compreender suficientemente o problema.

A terceira ideia, e talvez a mais importante, é **convergence**.

Depois da implementação, `/speckit.converge` compara a codebase com spec, plan e tasks. Se detectar gaps, ele pode acrescentar novas tasks ao `tasks.md`. O agente implementa essas tasks e executa convergence novamente até que não sejam encontrados gaps.

O fluxo deixa de ser:

```text
Spec
  |
  v
Code
  |
  v
Done
```

e torna-se:

```text
Spec
  |
  v
Code
  |
  v
Compare
  |
  +-- Gap --> New Tasks --> Code
  |                         |
  +-------------------------+
  |
  v
Converged
```

Esse loop é extremamente importante para agentic development.

Uma das piores propriedades de um agente autônomo é que ele pode acreditar que terminou.

`Done` é uma avaliação feita pelo próprio executor.

Convergence introduz um avaliador adicional: a implementação precisa ser reconciliada contra aquilo que deveria existir.

Essa ideia se tornará importante novamente quando chegarmos ao Build Like Amazon.

## OpenSpec: mudança como entidade de primeira classe

OpenSpec escolhe outro centro de gravidade.

Sua documentação enfatiza um workflow fluido, iterativo, lightweight e brownfield-first. O projeto parte de uma observação muito prática: a maior parte do desenvolvimento real não cria sistemas novos. Ela modifica sistemas existentes.

Isso produz uma distinção arquitetural profundamente importante.

Em muitos sistemas de SDD, uma nova feature produz uma nova spec.

Depois de alguns anos, podemos terminar com algo conceitualmente assim:

```text
Feature A Spec
Feature B Spec
Feature C Spec
Feature D Spec
...
Feature Z Spec
```

Mas qual documento responde:

> Qual é exatamente o comportamento atual do sistema?

OpenSpec tenta responder isso mantendo duas coisas separadas:

```text
openspec/
  specs/
  changes/
```

`specs/` representa a verdade comportamental atual.

`changes/` representa modificações propostas.

A abstração central deixa de ser "a spec desta feature" e passa a ser:

```text
State(t) + Delta = State(t+1)
```

Essa pequena equação representa uma diferença enorme.

Uma change pode dizer:

```text
ADDED
MODIFIED
REMOVED
```

em relação à specification existente. No archive, os deltas são aplicados à canonical spec e a change inteira é preservada historicamente.

Isso produz duas propriedades valiosas.

A primeira é **semantic change visibility**.

Git pode mostrar:

```diff
- timeout = 30
+ timeout = 15
```

Mas isso não explica necessariamente o significado da mudança.

Uma delta spec pode expressar semanticamente:

```text
MODIFIED Requirement:
Sessions MUST expire after 15 minutes of inactivity.
Previously: 30 minutes.
```

Estamos versionando comportamento, não somente bytes.

A segunda propriedade é **canonical truth**.

Depois que a change é implementada e arquivada:

```text
Canonical Spec(t)
  +
Approved Delta
  =
Canonical Spec(t+1)
```

A próxima mudança começa contra o estado atualizado.

O archive preserva proposal, design, tasks e delta specs, mantendo o contexto necessário para entender não somente o que mudou, mas também por quê e como.

Esse mecanismo é extraordinariamente interessante para sistemas de longa duração.

Imagine perguntar a um agente daqui a três anos:

> Por que o ledger rejeita essa operação quando o saldo disponível está positivo?

Com Git, ele pode investigar código, commits e talvez pull requests.

Com semantic change history, ele pode encontrar a requirement que introduziu aquela regra, a change correspondente, o design utilizado e o contexto da decisão.

OpenSpec transforma evolução de software em algo mais próximo de semantic version control.

Seu ponto fraco está do outro lado.

O OpenSpec é deliberadamente menos interessado em ser um runtime sofisticado de agentes. Isso é excelente para evolução de conhecimento, mas scheduling de múltiplos agentes, enforcement em runtime e orchestration complexa não são o seu centro de gravidade.

## Kiro: quando a specification se transforma em runtime

Kiro parte de uma estrutura familiar:

```text
requirements.md
  |
  v
design.md
  |
  v
tasks.md
```

Mas sua diferença aparece quando essas tasks deixam de ser apenas um plano para humanos e se tornam entrada para execução agentic.

Neste ponto, specification começa a se aproximar de um executable work graph.

Se temos:

```text
        Task A
       /      \
      B        C
       \      /
        Task D
```

o runtime pode derivar:

```text
Wave 1
  A

Wave 2
  B || C

Wave 3
  D
```

A diferença é importante porque agentes introduzem um problema que humanos normalmente resolvem informalmente: scheduling.

Dois engenheiros podem conversar e perceber que suas mudanças conflitam.

Dois sub-agentes precisam receber explicitamente boundaries, dependencies e shared state.

Kiro também possui uma primitive poderosa que frameworks baseados apenas em Markdown normalmente não possuem: hooks.

A documentação atual lista triggers como `PreToolUse`, `PostToolUse`, `PreTaskExec`, `PostTaskExec`, `UserPromptSubmit`, `Stop` e eventos de arquivo. Também explicita que alguns triggers conseguem bloquear execução, como `PreToolUse`, `UserPromptSubmit` e `PreTaskExec`.

A disponibilidade depende da superfície e da versão da ferramenta. A [documentação de gatilhos](https://kiro.dev/docs/hooks/types/) separa IDE, CLI e Web; não é correto assumir que o mesmo gancho existe com a mesma semântica em todos esses ambientes.

Essa distinção merece atenção.

Existe uma diferença enorme entre:

```text
Do not deploy if tests are failing.
```

e:

```text
BeforeDeploy:
  run: test-suite
  if: failed
  action: block
```

O primeiro é uma policy descrita para o modelo.

O segundo é uma policy executada pelo sistema.

Essa distinção entre instruction e enforcement será uma das lacunas que quero fechar futuramente no Build Like Amazon.

Kiro também aborda context engineering de maneira interessante. Steering mantém conhecimento persistente do workspace, podendo incluir product context, technology stack, project structure e regras específicas. Isso tenta resolver outro problema que cresce conforme agents se tornam mais poderosos: contexto demais também é um problema.

Carregar toda documentação, todas tools e todas policies em toda interação não escala.

O ideal é que o sistema consiga carregar o contexto certo, na hora certa, para o trabalho certo.

## AI-DLC: o lifecycle também precisa mudar

AI-DLC escolhe a abstração mais ampla.

Sua tese é que colocar IA dentro de um SDLC desenhado para execução humana produz ganhos, mas preserva estruturas que talvez não façam mais sentido.

A abordagem tenta evitar dois extremos.

No primeiro:

```text
Human drives
AI assists
```

No segundo:

```text
Human prompts
AI autonomously decides everything
```

AI-DLC procura uma divisão diferente:

```text
AI drives execution
Human governs decisions
```

A implementação descrita nos materiais públicos trabalha com fases como Inception, Construction e Operations, adaptando profundidade de processo ao tipo de trabalho. Uma correção simples não deveria pagar o mesmo custo de processo de uma mudança arquitetural multi-region.

Isso parece óbvio quando escrito.

Na prática, é um problema crônico de processos de engenharia.

Quando uma metodologia é leve demais, ela falha nas decisões irreversíveis.

Quando é pesada demais, desenvolvedores começam a contorná-la em mudanças simples.

O resultado saudável deveria ser algo próximo de:

```text
Ceremony = f(Risk, Irreversibility, Impact)
```

e não:

```text
Ceremony = constant
```

AI-DLC também deixa explícita a importância da supervisão humana. Planos e artifacts são apresentados para review e approval em pontos relevantes.

Isso estabelece uma distinção que considero central para engenharia agentic:

> Autonomy is not authority.

Um agente pode possuir ampla autonomia de execução sem possuir autoridade para tomar toda decisão.

Essa é uma diferença fundamental.

## Quatro frameworks, quatro loops diferentes

Depois de estudar essas abordagens, passei a enxergá-las menos como competidores diretos e mais como sistemas que tentam fechar loops diferentes.

Spec Kit fecha:

```text
Intent <-> Implementation
```

Sua pergunta central é:

> Construímos aquilo que especificamos?

OpenSpec fecha:

```text
Current State <-> Change <-> New State
```

Sua pergunta central é:

> A descrição do sistema continua verdadeira depois da mudança?

Kiro fecha:

```text
Requirement <-> Execution <-> Runtime Feedback
```

Sua pergunta é:

> A execução do agente está conectada a tasks, hooks e automações que conseguem orientar ou bloquear ações?

AI-DLC fecha:

```text
Intent <-> Decision <-> Execution
```

Sua pergunta é:

> Estamos aplicando a profundidade certa de processo e mantendo humanos nos pontos em que judgment é necessário?

É possível aprender muito com os quatro.

Mas ainda existe um loop ausente.

Um sistema pode possuir uma specification perfeita.

Pode implementar a specification perfeitamente.

Pode inclusive ter testes e checks excelentes.

E mesmo assim construir a coisa errada.

```text
Customer Problem: WRONG
Specification: PERFECT
Implementation: PERFECT
Verification: PERFECT
```

O sistema converge perfeitamente para uma intenção ruim.

Foi justamente nesse ponto que minha exploração começou a tomar outra direção.

## Começando antes da specification: Working Backwards

O Build Like Amazon Agent Skills nasceu da ideia de organizar práticas de engenharia inspiradas em mecanismos públicos da Amazon de maneira que coding agents pudessem utilizá-las como processos repetíveis.

O projeto não tenta representar um processo oficial interno da Amazon. É uma interpretação pessoal, baseada em práticas públicas, livros, talks, Builders' Library, Well-Architected e mecanismos conhecidos de engenharia.

O lifecycle conceitual é circular:

```text
Working Backwards
  |
  v
Design
  |
  v
Build
  |
  v
Deploy
  |
  v
Operate
  |
  v
Learn
  |
  +----> Working Backwards
```

A diferença fundamental é que a specification não é o primeiro artifact.

Antes de perguntar como construir alguma coisa, o framework tenta estabelecer por que ela deveria existir.

Working Backwards começa pela experiência desejada do cliente.

Isso muda a cadeia:

```text
Idea
  |
  v
Specification
```

para:

```text
Signals
  |
  v
Customer Problem
  |
  v
Desired Outcome
  |
  v
Solution Space
  |
  v
Validation
  |
  v
Design
```

Em outras palavras, Build Like Amazon acrescenta um **customer-intent loop** antes do engineering loop.

E essa diferença não é apenas produto versus tecnologia.

Ela altera como agents raciocinam.

Um agente que começa imediatamente em design tende a otimizar a implementação.

Um agente que começa pelo problema pode primeiro desafiar a necessidade de implementar.

Essa talvez seja uma das utilizações mais poderosas de IA em engenharia: não gerar mais rapidamente uma solução, mas ajudar a evitar construir uma solução desnecessária.

## Uma estrutura para separar a biblioteca do estado de engenharia

A versão `0.4.0` concentra os artefatos produzidos pelo processo em `.bla/`, dentro do projeto que adota a biblioteca. Essa pasta deve ser versionada. Ela guarda decisões e evidências que um revisor precisa conseguir ler, não um cache descartável da sessão.

```text
Project Repository
  |
  +--> Source Code
  +--> .bla/
         +--> working-backwards/
         +--> design/
         +--> specs/
         |      +--> recurring-payments/
         |             +--> requirements.md
         |             +--> design.md
         |             +--> tasks.md
         |             +--> coherence-review.md
         |             +--> implementation-review.md
         |             +--> .reports/
         +--> reviews/
         +--> deployment/
         +--> operations/
         +--> coe/
         +--> implementation-memory.md
         +--> metrics.jsonl
```

O arquivo de métricas é opcional; a árvore ilustra onde ele fica quando a medição está habilitada. Nem toda mudança precisa produzir todos esses documentos.

A separação resolve uma ambiguidade prática: `docs/` deixa de misturar a documentação da biblioteca com os documentos que ela produz para outro sistema. Mas seu efeito arquitetural é maior. O estado de engenharia passa a ter um endereço previsível, que continua existindo quando o modelo, a conversa ou a ferramenta de execução muda.

O [catálogo de artefatos](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/docs/artifact-catalog.md) explicita, para cada documento, quem o produz, onde ele fica, qual modelo define sua estrutura e quem o consome. Isso estabelece contratos entre fases: o resultado de uma fase precisa ser uma entrada utilizável para a próxima.

Existe uma diferença entre acumular documentos e construir essa cadeia. Um documento sem consumidor pode ser apenas burocracia. Um contrato de API consumido pelo planejamento, pela implementação e pela revisão participa diretamente do controle da mudança.

## Operating contract: uma Constitution mais ampla

Spec Kit possui Constitution.

Kiro possui Steering.

OpenSpec possui config e context.

AI-DLC possui workflow scaffolds e decision checkpoints.

Build Like Amazon possui algo que atualmente está distribuído entre `AGENTS.md`, o meta-skill `using-amazon-skills`, Leadership Principles, bar raiser personas e regras específicas de cada skill.

O `AGENTS.md` funciona como um operating contract entre a skill library e o agent. Ele contém behaviors não negociáveis relacionados a approval gates, assumptions, simplicity, verification, API-first, proportionality, autonomous execution e task tracking.

Isso representa uma camada conceitual semelhante à Constitution:

```text
                  OPERATING CONTRACT

Customer Obsession      Verify, Do Not Assume
Simplicity              API First
Approval Gates          Mechanisms Over Intentions
One/Two-Way Doors       Operational Ownership

                         |
                         v
                  Every Workflow
```

Existe, entretanto, uma diferença.

Uma Constitution tradicional tende a declarar princípios do projeto.

O operating contract do Build Like Amazon também declara comportamentos esperados do agente.

Ele não diz somente que assumptions são ruins. Ele instrui o agente a interromper determinada sequência quando encontra uma assumption não resolvida.

Ele não diz somente que quality importa. Ele define verification checkpoints.

Ele não diz somente que decisões reversíveis merecem menos cerimônia. Ele classifica one-way e two-way doors e altera o processo aplicado.

Estamos começando, portanto, a transformar princípios culturais em **agent execution semantics**.

Esse é um território muito interessante.

## Adaptive ceremony: processo proporcional ao risco

Uma das decisões de design mais importantes do Build Like Amazon foi deixar de tratar o lifecycle completo como obrigatório para toda mudança.

O framework classifica trabalho em níveis como Trivial, Small, Medium, Large e New Product, considerando tamanho, customer impact, reversibility e estado do repository. O próprio agent deve escolher a cerimônia apropriada, pedindo intervenção do usuário somente quando existe ambiguidade material.

O resultado pode ser pensado assim:

```text
Change
  |
  v
Context Assessment
  |
  v
Risk / Impact / Reversibility
  |
  +--> Trivial --> Code + Tests
  |
  +--> Medium  --> Spec + Build
  |
  +--> Large   --> Working Backwards + Design + Spec + Build
```

Essa ideia aproxima Build Like Amazon de AI-DLC, mas existe uma característica adicional: one-way doors.

Uma mudança de código pequena pode receber cerimônia alta se for difícil de reverter.

Por exemplo:

```text
3-line public API change
```

pode ser uma decisão muito mais séria que:

```text
3,000-line internal refactoring
```

O número de linhas não é uma boa medida de risco.

Na versão atual, essa proporcionalidade também se tornou legível na própria tabela de classificação. Cada nível declara quais revisores são obrigatórios, quais verificações determinísticas se aplicam, qual evidência de conclusão é exigida, como a iteração é registrada e quais eventos do fluxo são emitidos.

Mudanças triviais e pequenas não herdam automaticamente os relatórios e a medição de mudanças médias. A partir do nível médio, aparecem a revisão de implementação isolada do autor, os relatórios por tarefa e as verificações de encerramento das ondas. Mudanças grandes acrescentam as avaliações de segurança e operação conforme o contexto exige.

Essa explicitação importa porque um processo adaptativo também pode sofrer deriva. Se cada agente interpreta livremente quanto processo aplicar, uma pequena alteração pode acabar pagando o custo de uma nova arquitetura. A tabela delimita o custo esperado de cada nível e torna a classificação revisável.

O framework considera como exemplos de one-way doors coisas como contratos públicos, migrations destrutivas, deletion de dados, mudanças em security model ou service boundaries.

Isso produz uma função mais realista:

```text
Required Rigor =
  f(
    Customer Impact,
    Blast Radius,
    Reversibility,
    Architectural Reach,
    Operational Risk
  )
```

Essa talvez seja uma das diferenças mais importantes entre workflow automation e engineering judgment.

## Design como composição de mecanismos

Uma parte particularmente importante do Build Like Amazon é que `/design` não significa "gere um design.md".

O command compõe uma cadeia de mecanismos:

```text
Dependency Management
  |
  v
Feature Flag Lifecycle
  |
  v
Operational Excellence
  |
  v
Pattern Catalog
  |
  v
Design Document
  |
  v
API Contract
  |
  v
Threat Model
  |
  v
Design Review
  |
  v
Vertical Specs
```

Essa composição é importante porque arquitetura real é transversal.

Escolher uma database não é apenas escolher storage.

Escolher uma external dependency altera failure modes.

Escolher cell-based architecture altera routing, data partitioning, observability, deployment e operational ownership.

Adicionar feature flags altera deployment semantics e cleanup requirements.

Adicionar uma API pública altera compatibility obligations.

Essas consequências normalmente ficam implícitas na experiência de engenheiros seniores.

O framework tenta torná-las explícitas.

O Pattern Catalog leva isso um passo além.

Skills representam como executar um processo.

Patterns representam uma decisão arquitetural cujas consequências atravessam vários processos.

Quando um pattern é adotado, seu Skill Impact Map pode alterar como design, implementation, deployment, observability e operations devem ser tratados.

Um pattern deixa de ser somente:

```text
PATTERN.md
```

e começa a funcionar como:

```text
Architecture Decision
  |
  v
Constraint Propagation
  |
  +--> API
  +--> Data
  +--> Testing
  +--> Deployment
  +--> Observability
  +--> Operations
```

Arquitetura passa a possuir efeitos executáveis sobre o lifecycle.

Isso é muito mais poderoso do que simplesmente pedir para o modelo "seguir boas práticas".

## API-first como dependency ordering

Outro princípio incorporado ao framework é API First.

O conceito não está restrito a REST.

Uma interface pode ser REST/OpenAPI, GraphQL SDL, gRPC/protobuf, AsyncAPI para eventos, data contracts, MCP tool definitions, agent tool schemas ou outro contrato explicitamente consumido.

O princípio é:

```text
Contract
  |
  v
Consumers
```

e não:

```text
Consumer A --+
Consumer B --+--> Implicit Contract
Consumer C --+
```

O `/design` exige que a interface seja identificada antes que clients dependentes sejam planejados, e o spec-driven implementation coloca API specs antes de client specs.

O efeito disso em agentic development é ainda mais importante do que em human development.

Se dois agents trabalham paralelamente em producer e consumer sem contrato congelado, cada um tende a preencher ambiguidades de maneira independente.

O resultado pode ser integração quebrada apesar de ambas as implementações parecerem corretas isoladamente.

Contracts reduzem o espaço de interpretação.

Em sistemas multi-agent, isso equivale a reduzir **distributed coordination ambiguity**.

## Vertical specs: transformando macro design em trabalho executável

Depois que o macro design é aprovado, Build Like Amazon não pula diretamente para code.

O system-level design é decomposto em vertical slices.

Cada slice possui:

```text
requirements.md
  |
  v
design.md
  |
  v
tasks.md
```

As requirements utilizam acceptance criteria. O design faz traceability de volta para requirements e pode incluir properties para property-based testing. Tasks possuem size, requirement references, design references, dependencies e wave assignment. Um dependency graph machine-readable pode ser incluído ao final.

Isso cria várias relações explícitas:

```text
Customer Intent
  |
  v
System Design
  |
  v
Requirement
  |
  v
Slice Design
  |
  v
Task
  |
  v
Implementation
```

Traceability deixa de ser um relatório gerado depois.

Ela passa a ser parte da execução.

Outro detalhe importante é o uso de vertical slices.

Um slice ideal entrega comportamento observável de ponta a ponta, em vez de organizar trabalho puramente por camada técnica.

Isso reduz um problema comum de decomposição agentic.

É fácil decompor:

```text
Agent A -> database
Agent B -> service
Agent C -> API
```

Mas os três podem tomar decisões incompatíveis.

Uma vertical slice possui um contract de valor mais claro e pode ser validada como uma unidade.

## De tasks.md para um pequeno distributed scheduler

É aqui que o framework deixa mais claramente o território de "skill library".

O `/build` possui semantics explícitas de orchestration.

Todas as specs são lidas.

As tasks são executadas de acordo com um dependency graph.

Tasks independentes dentro de uma wave podem ser disparadas paralelamente como sub-agents.

O protocolo exige esse paralelismo quando as tarefas são elegíveis. Mas independência deixou de significar apenas ausência de uma dependência no grafo: o planejamento também declara os arquivos que cada tarefa pode escrever.

Entre waves existem green-build gates.

E `tasks.md` funciona como durable execution state.

Os estados são:

```text
[ ] Pending
[-] In Progress
[x] Done
[!] Blocked
```

O orchestrator é o único writer desse arquivo.

Sub-agents recebem contexto limitado, executam sua unidade de trabalho e reportam resultado ao orchestrator.

Isso é uma decisão aparentemente simples, mas resolve uma classe real de problemas de concorrência.

Imagine três sub-agents escrevendo simultaneamente em `tasks.md`.

Teríamos uma forma trivial de lost update.

A regra:

```text
Single Writer = Orchestrator
```

é equivalente a introduzir um ownership model para o control state.

O lifecycle de uma wave é:

```text
Orchestrator
  |
  v
Read pending tasks
  |
  v
Mark [-]
  |
  v
Persist state
  |
  v
Dispatch agents
  |
  +--> Agent A
  +--> Agent B
  +--> Agent C
  |
  v
Collect reports
  |
  v
Mark [x] or [!]
  |
  v
Re-read state
  |
  v
Green-build gate
  |
  v
Next wave
```

A ordem é intencional.

O framework determina que `tasks.md` seja persistido como in-progress antes do dispatch.

Por quê?

Porque crash recovery importa.

Se a agent session morre após persistir `[-]` mas antes de disparar trabalho, uma execução futura consegue identificar estado incompleto e reconciliá-lo.

Se o trabalho fosse disparado antes de persistir estado, poderíamos ter execução sem durable record.

Isso se aproxima de uma preocupação clássica de distributed systems.

Não estamos implementando exatamente um transaction log, mas estamos aplicando o mesmo princípio:

> State transition should become durable before relying on it for recovery.

Kiro possui uma implementação nativa mais integrada dessa classe de problema através de seu ambiente e suas tasks.

Build Like Amazon, por outro lado, descreve o protocolo de maneira harness-agnostic.

Esse trade-off é importante.

Kiro consegue enforcement mais forte.

Build Like Amazon possui maior portabilidade.

## Paralelismo exige independência de escrita

Considere duas tarefas sem dependência lógica: implementar a renovação de uma assinatura e implementar seu cancelamento. Elas podem consumir o mesmo contrato e ainda modificar o mesmo arquivo de serviço. Colocá-las na mesma onda cria uma disputa de escrita, mesmo que o grafo de requisitos pareça correto.

O planejamento atual inclui um conjunto `writes` por tarefa:

```json
{
  "tasks": {
    "2.1": {
      "depends_on": ["1.1"],
      "wave": 2,
      "writes": ["src/payments/renewal.ts", "tests/renewal.test.ts"]
    },
    "2.2": {
      "depends_on": ["1.1"],
      "wave": 2,
      "writes": ["src/payments/cancellation.ts", "tests/cancellation.test.ts"]
    }
  }
}
```

Duas tarefas só podem compartilhar uma onda quando seus conjuntos de escrita não se intersectam. Um caminho terminado em `/` representa um diretório e colide com arquivos abaixo dele. O `bla-check` consegue detectar mecanicamente essas interseções, além de dependências indevidas dentro da mesma onda.

Mas uma declaração pode estar errada. Por isso, o comando de construção também prescreve uma reconciliação com os caminhos efetivamente alterados no Git ao encerrar a onda. Alterações fora dos conjuntos declarados impedem seu encerramento até que a divergência seja resolvida. Essa comparação é parte do protocolo do orquestrador; não deve ser confundida com uma capacidade automática de monitoramento do `bla-check`.

```text
Before Execution
  Declared Write Sets -> Collision Check

After Execution
  Actual Changed Paths -> Declared Scope Reconciliation
```

Isso ainda não fornece isolamento transacional, bloqueio de arquivos ou execução exatamente uma vez. A checagem de conjuntos declarados também não identifica, por si só, qual agente escreveu cada byte. Ela reduz conflitos previsíveis e exige que o resultado seja reconciliado com o plano. É uma garantia menor do que um sistema de execução isolada, mas muito mais concreta do que pedir aos agentes que tenham cuidado.

Há outro detalhe importante: uma tarefa bloqueada não satisfaz a dependência de outra. Seus dependentes permanecem sem execução enquanto as demais tarefas elegíveis continuam. No encerramento da especificação, esses dependentes recebem o estado bloqueado com a causa registrada. O grafo passa a explicar também o que não pôde ser entregue.

## De convenções escritas a verificações determinísticas

O projeto agora distribui `tools/bla-check`, uma ferramenta em Python 3 que utiliza apenas a biblioteca padrão. Seus subcomandos implementados verificam links relativos, consistência de tarefas e a série de métricas do fluxo.

```sh
python3 tools/bla-check tasks .bla/specs/recurring-payments --wave 2
python3 tools/bla-check tasks .bla/specs/recurring-payments
python3 tools/bla-check metrics .bla/metrics.jsonl
```

O primeiro comando restringe as verificações à onda que está encerrando. Isso evita reprovar tarefas de ondas posteriores que legitimamente ainda não começaram. O segundo verifica o encerramento da especificação inteira. Falhas retornam código diferente de zero; avisos preservam o sucesso do comando.

Esse é um movimento importante na essência do projeto. Uma convenção que antes dependia de o modelo ler corretamente todos os marcadores agora pode ser conferida por código. A interpretação do requisito continua exigindo julgamento, mas verificar se uma tarefa ficou aberta ou se duas tarefas declararam o mesmo arquivo não precisa consumir esse julgamento.

A integração contínua da própria biblioteca também passou a verificar contagens, estrutura dos arquivos, referências, integridade dos rótulos de revisão, localização dos artefatos e paridade dos comandos entre ferramentas. Os diretórios de comandos de Gemini e Kiro são espelhos mantidos manualmente dos comandos canônicos de Claude; a verificação detecta quando eles divergem. Essa integração protege a biblioteca, e não instala automaticamente uma política em cada projeto que a copia.

A portabilidade continua sendo uma escolha explícita. Quando a ferramenta ou o Python não está disponível no projeto adotante, o fluxo deve declarar que a garantia caiu para verificação pelo modelo e continuar. Isso permite adoção em ambientes diferentes, mas não torna as garantias equivalentes. Uma execução com verificação determinística e outra conferida apenas por leitura precisam ser distinguíveis no relatório.

## Approval gates onde decisão importa

Um erro fácil ao criar agent workflows é colocar humanos em todo lugar.

Isso destrói o ganho de autonomia.

O outro erro é remover humanos de todos os pontos.

Isso transforma o agent em decision authority.

O Build Like Amazon tenta separar as duas coisas.

Durante Working Backwards e Design existem gates.

Depois que requirements, design e tasks estão aprovados, `/build` muda para um comportamento autonomously-to-completion por default. O agente não deveria terminar cada task perguntando "posso continuar?". Existem apenas algumas classes explícitas de stop condition: hard blocker, failed implementation review, unrecoverable green-build failure ou pedido explícito do usuário.

Isso produz uma divisão que considero saudável:

```text
Decision Uncertainty
  |
  v
Human Gate

Execution Certainty
  |
  v
Agent Autonomy
```

Ou, de outra forma:

```text
Human = Authority
Agent = Executor
```

mas executor aqui não significa executor burro.

O agent pode planejar, investigar, corrigir e adaptar.

O limite é autoridade sobre determinadas classes de decisão.

Essa fronteira pode inclusive mudar com risk.

É possível imaginar futuramente uma policy como:

```yaml
internal_refactor:
  authority: agent

additive_private_api:
  authority: agent
  required_evidence:
    - automated_tests
    - contract_check

public_api:
  authority: human
  required_evidence:
    - compatibility_review
    - consumer_impact_analysis

destructive_migration:
  authority: human
  required_evidence:
    - rollback_plan
    - backup_validation
    - operational_runbook
```

Nesse ponto, estamos saindo de prompt engineering e entrando em **delegated authority systems**.

## Coherence antes de implementation

Existe uma classe de erro que ocorre antes de qualquer linha de code ser escrita:

> O micro-plan já divergiu do macro-design.

Build Like Amazon introduz um Spec Coherence Review entre spec creation e build.

Ele compara requirements, slice design e tasks contra o approved system design, verificando coisas como contradições, missing traceability, new architectural decisions, inconsistent one-way doors e perda de decisões relacionadas a dependencies, feature flags ou operational excellence.

Podemos pensar nele como:

```text
Macro Design
  |
  +--> Requirements
  +--> Slice Design
  +--> Tasks
          |
          v
       Compare
          |
          +--> coherent
          |
          +--> drift --> revise
```

Esse mecanismo existe porque decomposition também pode introduzir drift.

Uma design decision feita em alto nível pode ser reinterpretada de maneira diferente na slice.

Um requirement pode aparecer sem origem no design.

Uma task aparentemente pragmática pode introduzir um novo datastore que nunca foi discutido.

Em uma organização humana, um senior engineer normalmente percebe isso durante design review.

Em um sistema agentic, precisamos transformar essa percepção em mecanismo.

## Convergence depois de implementation

Depois do build existe um segundo loop.

O Post-Implementation Review compara requirement por requirement e acceptance criterion por acceptance criterion contra a implementação.

Ele também procura skipped tasks, properties não verificadas, scope creep e operational concerns.

O resultado pode ser `PASSED`, `PASSED WITH FIXES NEEDED` ou `FAILED`.

A versão atual também define uma escala canônica de severidade e uma escala de vereditos. Rótulos históricos diferentes continuam existindo, mas passam a ter equivalências explícitas. Isso permite entender que um `[FIX REQUIRED]` no relatório de implementação corresponde a um achado bloqueante, mesmo quando outro revisor usa uma palavra diferente.

Os achados persistidos possuem âncora no arquivo, identificador estável, impacto, confiança, correção mínima e estado. Na nova revisão, o identificador permanece e o estado muda. Assim, corrigir um problema não apaga a evidência de que ele existiu.

Uma pendência bloqueante em aberto retira a opção de aprovar e avançar normalmente. A aceitação excepcional de risco exige registro com mitigação, responsável e data de revisão; uma justificativa solta na conversa não substitui isso. Um relatório sem bloco de veredito legível também bloqueia o avanço. A escala distingue ainda uma revisão incompleta de uma reprovação: não conseguir avaliar não é o mesmo que avaliar e encontrar uma implementação errada.

Isso torna os pontos de decisão interpretáveis ao longo do processo. Sem essa semântica, um agente pode confundir uma observação pequena com impedimento, ou tratar uma falha séria como recomendação opcional.

Quando existem gaps pequenos:

```text
Implementation
  |
  v
Review
  |
  v
PASSED WITH FIXES
  |
  v
New Fix Tasks
  |
  v
Execute
  |
  v
Reverify
```

Essa é essencialmente uma forma de convergence.

É conceitualmente muito próxima do mecanismo que considero uma das melhores ideias do Spec Kit.

A diferença é que atualmente Build Like Amazon distribui convergence entre várias primitives:

```text
Spec Coherence Review
Green-Build Gates
Implementation Review
Property-Based Testing
Fix Tasks
```

Uma próxima evolução poderia elevar a convergência a uma operação explícita.

Não porque falte o comportamento.

Ele já existe.

Falta tornar o conceito central no state model.

## Done não deveria significar "o agent terminou"

Esse ponto merece ser enfatizado.

Agentic software development possui um problema epistemológico.

O mesmo sistema que executou a mudança frequentemente é aquele que declara que a mudança está completa.

O Build Like Amazon passou a atacar esse problema diretamente. Em mudanças médias e grandes, a revisão de implementação deve ser delegada a um agente com contexto isolado do autor. O revisor recebe os requisitos, o projeto da fatia, as tarefas, o contrato congelado, o conjunto de alterações e as restrições da revisão de coerência. Ele não recebe a defesa que o autor construiu para suas escolhas.

A separação é importante: ao receber uma explicação convincente sobre por que um atalho foi necessário, o revisor pode acabar avaliando a justificativa em vez do resultado. O contexto isolado o obriga a partir dos compromissos assumidos e do que está efetivamente implementado.

Quem executa as correções é o orquestrador ou seus agentes de implementação. A nova verificação é uma nova delegação ao verificador, que recebe o relatório anterior para preservar os identificadores dos achados. O autor da correção não certifica sua própria correção.

Esse isolamento reduz a contaminação pelo raciocínio do autor, mas não torna o revisor infalível. Agentes que utilizam o mesmo modelo podem compartilhar pontos cegos. A revisão continua precisando de testes executados e evidências verificáveis. Para mudanças triviais e pequenas, o processo mantém a avaliação no mesmo contexto, com custo proporcional.

Uma arquitetura mais robusta deve distinguir:

```text
Execution State
```

de:

```text
Evidence State
```

Hoje Build Like Amazon já possui elementos importantes de evidence.

Requirements possuem acceptance criteria.

Designs podem possuir properties.

Build roda unit, integration e contract tests.

Green-build gates verificam regressions.

Implementation Review reconcilia code contra requirements.

Deploy possui progressive rollout e rollback criteria.

Operate exige metrics, alarms e runbooks.

Agora existe também evidência durável por tarefa. Em mudanças médias e grandes, o agente deve escrever `.bla/specs/<slice-name>/.reports/<task-id>.md` antes de reportar conclusão. A primeira linha identifica a tarefa; o restante registra arquivos alterados, comandos de teste efetivamente executados e seus resultados resumidos.

```markdown
**Agent:** 2.1

## Changed Files
- src/payments/renewal.ts
- tests/renewal.test.ts

## Verification
Command: npm test -- tests/renewal.test.ts
Result: 8 tests passed; exit code 0.
```

O exemplo é ilustrativo, não o resultado de uma execução real. Sua função é mostrar que o próximo agente pode consultar evidência no repositório sem depender da mensagem final da sessão anterior.

Existe uma limitação que precisa permanecer visível: o `bla-check` verifica esses relatórios somente quando o diretório `.reports/` existe. Se ele inteiro estiver ausente, a ferramenta não conclui sozinha que uma mudança de nível médio descumpriu a obrigação. E conferir a presença do arquivo e sua identificação não prova que os testes narrados ali foram executados. A obrigação é mais ampla do que a garantia mecânica implementada.

O verificador de implementação também distingue propriedades testadas de propriedades avaliadas por inspeção. Quando o teste não foi executado, o relatório deve registrar `NOT EXECUTED` ou `VERIFIED BY INSPECTION`, nunca apresentar a leitura do código como um teste aprovado.

Isso muda a definição de conclusão em dois níveis:

```text
Execution Closure
  Every task is Done or Blocked
  No task remains Pending or In Progress

Delivery Verdict
  Every acceptance criterion must be satisfied for PASSED
  A blocked criterion prevents a clean pass
```

Uma execução pode terminar porque não há mais trabalho elegível, deixando uma integração bloqueada por um serviço externo. Isso não significa que a funcionalidade foi entregue. O resumo precisa expor o bloqueio, e o veredito não pode afirmar que todos os critérios foram atendidos.

Portanto, já não seria preciso dizer que o projeto carece de evidência persistente. A oportunidade seguinte é conectar essa evidência de maneira estruturada a cada requisito, propriedade e observação em produção, com proveniência e validade explícitas.

Imagine que cada requirement possua explicitamente:

```text
Requirement
  |
  v
Property
  |
  v
Evidence
```

Evidence pode ser:

```text
unit-test
integration-test
contract-test
property-test
static-analysis
security-scan
benchmark
load-test
deployment-check
canary-metric
production-observation
```

Um artifact machine-readable poderia ser semelhante a:

```yaml
requirement: PAY-17
statement: Payment processing SHALL be idempotent.
properties:
  - PAY-17-P1
evidence:
  - type: property-test
    artifact: tests/payment_idempotency_test.go
    result: passed
  - type: integration-test
    artifact: tests/payment_retry_test.go
    result: passed
  - type: canary
    metric: duplicate_payment_rate
    expected: 0
    observed: 0
status: satisfied
```

Então done deixa de ser:

```text
Agent said complete
```

e passa a ser:

```text
Required Evidence Set = Satisfied
```

Esse modelo se torna especialmente poderoso quando combinado com property-based testing e contract testing.

O conceito pode ser generalizado muito além de testing.

## Deploy, Operate e Learn: a parte normalmente ausente de SDD

Muitos sistemas de Spec-Driven Development terminam no code.

Mas software não cria valor quando mergeia.

Software cria valor quando funciona em produção.

Isso significa que um framework de engenharia completo precisa continuar depois do implementation review.

Build Like Amazon possui skills dedicadas a progressive deployment, feature flags, rollback, operational readiness, runbooks, metrics review e Correction of Errors. Seu lifecycle é explicitamente circular, fazendo Learn voltar para a próxima iteração.

Esse é um diferencial arquitetural, não somente uma coleção maior de skills.

Considere:

```text
Requirement:
p99 latency < 100 ms
```

O implementation test pode mostrar:

```text
benchmark p99 = 65 ms
```

Mas produção pode mostrar:

```text
real p99 = 320 ms
```

Qual é a realidade?

Produção.

Portanto:

```text
Spec -> Implementation Evidence
```

não fecha completamente o loop.

Precisamos de:

```text
Spec
  |
  v
Implementation
  |
  v
Deployment
  |
  v
Production
  |
  v
Observed Evidence
```

Production reality precisa ser capaz de contradizer design assumptions.

Uma arquitetura AI-native precisa aceitar essa possibilidade.

## Correction of Errors como mecanismo de aprendizado

Quando algo falha em produção, simplesmente corrigir o bug recupera o estado anterior.

Mas não melhora o sistema de engenharia.

A ideia de Correction of Errors é diferente.

O incidente produz conhecimento.

O conhecimento deveria produzir um mecanismo.

```text
Incident
  |
  v
Analysis
  |
  v
Root Cause
  |
  v
Corrective Action
  |
  v
Mechanism
  |
  v
Future Prevention
```

É aí que entra uma das partes mais interessantes do Build Like Amazon atual: Implementation Memory.

## Implementation Memory: memória procedural, não histórico infinito

Memory para agents é frequentemente tratada como:

> Salve tudo e recupere semanticamente depois.

Isso parece atraente, mas possui vários problemas.

Contexto cresce.

Regras antigas continuam aparecendo.

Feature-specific facts contaminam outras features.

Incidental details começam a parecer invariants.

Build Like Amazon escolhe uma abordagem deliberadamente limitada.

Implementation Memory é definida como **procedural memory, not history**.

Ela não tenta armazenar todas as decisões ou todos os eventos.

Ela guarda um pequeno conjunto de regras de implementação que demonstraram valor para builds futuros.

Essas regras podem vir de falhas de construção, revisões e análises de incidentes, mas a captura também passou a considerar rejeições recorrentes no projeto e achados de coerência repetidos entre especificações. Todas passam pelos mesmos filtros de admissão.

Podem incluir tags, file patterns, Applies when, confidence, impact, hit count, prevented count, created date e last used.

O limite é de 12 regras ativas no total, não 12 por fase.

Cada regra possui um campo `Phase`. A seleção exige que a fase corresponda à execução atual e que pelo menos um sinal de contexto corresponda: domínio, caminhos de arquivos ou condição de aplicação. Uma regra útil para projeto não se transforma automaticamente em requisito de implementação.

Hoje existem pontos de seleção conectados a `/design`, `/spec` e `/build`. Os valores `wb`, `deploy` e `operate` podem ser armazenados, mas ainda estão reservados: sem um ponto de seleção nesses comandos, a regra não será aplicada ali. Essa distinção evita confundir um campo aceito pelo formato com uma capacidade já conectada ao processo.

Existem regras de merge.

Existe staleness.

Existe decay.

Existe human review antes que determinados learnings sejam promovidos à memória.

O loop pode ser representado assim:

```text
Build
  |
  v
Review / Failure / Feedback
  |
  v
Reflection
  |
  v
Candidate Learning
  |
  v
Admission Filter
  |
  v
Human Review
  |
  v
Implementation Memory
  |
  v
Context Matching
  |
  v
Future Build
  |
  v
Hit / Prevention Evidence
  |
  +----> Reflection
```

Isso é um mecanismo de aprendizado organizacional por memória procedural. A analogia com aprendizado por reforço ajuda a pensar no ciclo de feedback, mas não descreve um algoritmo de otimização implementado pelo projeto.

Não estamos treinando pesos de um modelo.

Estamos melhorando procedural guidance de maneira controlada.

E bounded memory é extremamente importante.

A pergunta não é:

> O que aconteceu no passado?

A pergunta é:

> Qual pequeno número de regras comprovadas deveria alterar a próxima execução?

Esse é um problema muito mais útil.

## Medir o processo sem premiar a burocracia

Se o projeto pretende melhorar o sistema de engenharia, também precisa observar o próprio fluxo. A versão atual define seis eventos, emitidos opcionalmente a partir do nível médio:

```text
phase_started
phase_completed
spec_completed
gate_approved
gate_rework
review_blocking_finding
```

A série fica em `.bla/metrics.jsonl`, com um objeto JSON por linha. Cada evento tem um responsável declarado. O evento de conclusão de especificação pertence exclusivamente a `/build`; ele representa encerramento da execução, não uma afirmação independente de entrega completa.

O leitor calcula tempo de ciclo por fase e especificação e quantidade de retornos pelos pontos de revisão. O tempo de ciclo considera o início preservado e o término registrado, incluindo retrabalho. Uma fase sem evento de conclusão fica sem tempo de ciclo calculável; o leitor não substitui esse término pelo horário atual.

Isso parece um detalhe de implementação, mas carrega uma posição sobre evidência. Reconstruir retrospectivamente horários plausíveis produziria uma história que aparenta ter sido medida. O protocolo exige registrar quando o evento ocorre e aceitar lacunas quando o registro não foi possível.

Também não mede quantidade de documentos ou agentes como indicador de qualidade. Esses números crescem quando o processo fica mais pesado. As métricas disponíveis observam andamento e retrabalho; não demonstram, sozinhas, satisfação do cliente, redução de incidentes ou retorno financeiro.

Há limites deliberados: custo e defeitos que escaparam para produção dependem de fontes que o fluxo não observa. Sem uma relação verificável entre incidente e especificação, atribuir uma taxa de defeitos por especificação seria inventar precisão. O projeto prefere declarar a ausência.

A medição nunca bloqueia a entrega. Se a série não puder ser escrita, o fluxo informa a ausência e continua. Uma série inválida, por outro lado, não é agregada pelo verificador. O mecanismo permite observar o processo sem transformar a observabilidade em um novo motivo para paralisar uma mudança.

## Brownfield: a realidade da maior parte do software

Qualquer metodologia que funcione apenas para novos projetos terá relevância limitada.

O problema brownfield é difícil porque documentação e realidade frequentemente divergiram há anos.

Build Like Amazon adicionou `/onboard` para executar uma reverse engineering pass sobre code, infrastructure, CI/CD, observability, dependencies, tests, security boundaries e data stores.

O resultado pode incluir reverse-engineered Design Document, API contracts, Threat Model e observed patterns.

Existe uma decisão importante aqui:

> Reverse-engineered artifacts não fingem possuir autoridade histórica.

Eles são explicitamente marcados como inferidos, com confidence levels.

Isso evita um erro sutil.

Observar:

```text
The code uses DynamoDB.
```

não significa saber:

```text
DynamoDB was chosen because of single-digit millisecond latency requirements.
```

O primeiro é fact.

O segundo é historical intent.

Code consegue provar o primeiro.

Não necessariamente o segundo.

Essa separação entre observed state e inferred rationale é importante para agents.

O `/onboard` tenta transformar uma codebase sem documentação agent-ready em um baseline persistente para futuras mudanças.

Aqui existe sobreposição forte com OpenSpec, mas os sistemas atacam o problema em momentos diferentes.

Build Like Amazon reconstrói o presente.

OpenSpec versiona semanticamente a evolução futura.

Juntar os dois conceitos é provavelmente a melhoria mais importante possível.

## Onde o Build Like Amazon ainda perde para OpenSpec

Esta é a maior lacuna do framework atual.

Imagine que `/onboard` produza uma ótima representação de Payments.

Depois fazemos 50 features.

Cada uma possui requirements, design e tasks.

Qual artifact responde:

> Qual é, agora, o comportamento canônico de Payments?

Hoje essa resposta não é tão forte quanto poderia ser.

Temos:

```text
Design Documents
Specs
Implementation Reviews
Code
Git History
```

Mas não necessariamente uma única:

```text
Canonical Behavior Specification
```

OpenSpec resolve exatamente isso com:

```text
Canonical State(t)
  +
Change Delta
  =
Canonical State(t+1)
```

Essa ideia continua sendo uma possibilidade para a próxima evolução. Centralizar artefatos em `.bla/` e manter um catálogo não equivale a manter uma especificação comportamental canônica. A versão `0.4.0` melhorou a localização e os contratos dos documentos, mas ainda não implementa esse ciclo de aplicação de deltas.

Não substituiria os vertical specs.

Eles resolvem um problema diferente.

Eu adicionaria uma camada de semantic state.

Por exemplo:

```text
.bla/
  constitution/
  canonical/
    payments/
      behavior.md
      invariants.md
      contracts/
    ledger/
      behavior.md
      invariants.md
  changes/
    active/
      add-recurring-payments/
        intent.md
        delta/
        design/
        specs/
        evidence/
    archive/
```

Uma Change passaria a ser uma entidade explícita.

Antes de implementação:

```text
Canonical Payments(t)
  |
  +--> Change Delta
  |
  v
Proposed Payments(t+1)
```

Depois de convergence:

```text
Canonical Payments(t)
  +
Verified Delta
  =
Canonical Payments(t+1)
```

A Change então vai para archive.

Isso produz duas timelines complementares:

```text
Git
  |
  +--> Physical history of code

Engineering State
  |
  +--> Semantic history of behavior and decisions
```

Esse é um salto grande.

Agents futuros poderiam raciocinar não apenas sobre "o que existe", mas sobre como o contrato do sistema evoluiu.

## Onde o Build Like Amazon ainda perde para Kiro

A segunda grande lacuna é enforcement.

Build Like Amazon possui muitas regras do tipo:

```text
MUST
MUST NOT
BLOCK
STOP
VERIFY
```

Mas, na maior parte dos harnesses, essas regras ainda dependem de o LLM obedecer.

Essa afirmação agora precisa de uma precisão: o projeto já combina instruções com verificações determinísticas. O `bla-check` detecta determinadas violações no encerramento, e a integração contínua protege a consistência da biblioteca. A lacuna está principalmente na interceptação da ação antes que ela aconteça, vinculada às permissões e aos pontos de decisão do projeto adotante.

Kiro consegue mover parte dessa policy para runtime. Um `PreToolUse`, por exemplo, pode realmente bloquear uma ação antes da ferramenta executar. Um `PreTaskExec` pode validar pré-condições antes de uma task começar.

Essa diferença pode ser representada assim:

```text
Build Like Amazon Today

Policy Markdown
  |
  v
Agent Reasoning
  |
  v
Action
  |
  v
Checkpoint Verification
  |
  +--> Deterministic Check When Available
  +--> LLM Verification Otherwise
```

versus:

```text
Runtime Enforcement

Policy
  |
  v
Interceptor
  |
  +--> allow --> Action
  |
  +--> deny  --> Agent Feedback
```

Uma evolução desse mecanismo não deveria exigir construir uma IDE completa.

Isso destruiria a portabilidade do framework.

Mas deveria introduzir uma Runtime Policy Interface.

Algo como:

```text
BeforeAction
AfterAction
BeforeStage
AfterStage
BeforeDecision
AfterDecision
OnEvidence
OnFailure
OnStop
```

Então diferentes harnesses poderiam implementar adapters:

```text
KiroAdapter
ClaudeCodeAdapter
CodexAdapter
GitHubActionsAdapter
CIAdapter
```

Quando o host possuir hooks nativos, policies podem ser enforced.

Quando não possuir, continuam funcionando como agent instructions.

Essa arquitetura mantém:

```text
Portable Semantics
```

sem abrir mão de:

```text
Native Enforcement When Available
```

## Onde Spec Kit ainda possui uma primitive mais clara

Build Like Amazon já possui convergence behavior.

Mas Spec Kit possui a vantagem de dar um nome central ao mecanismo.

`converge` é uma operação clara.

No Build Like Amazon, as responsabilidades equivalentes estão distribuídas.

Eu criaria explicitamente:

```text
/converge
```

não necessariamente como um novo user-facing command obrigatório, mas como uma runtime primitive.

Ela teria a missão de reconciliar:

```text
Intent
Design
Canonical Delta
Requirements
Tasks
Implementation
Evidence
```

O resultado poderia ser:

```text
CONVERGED
```

ou:

```text
DIVERGENCES:
  - implementation-gap
  - spec-gap
  - design-drift
  - evidence-gap
  - intent-change
```

Perceba que isso é mais amplo do que comparar code contra spec.

Nem toda divergência significa que code está errado.

Às vezes, durante implementation, descobrimos que design estava errado.

Às vezes requirement precisa mudar.

Às vezes customer intent mudou.

Um verdadeiro convergence engine deve descobrir qual representação está stale.

## O próximo passo: transformar artifacts em um state model

Hoje o framework utiliza Markdown muito bem.

E eu continuaria utilizando.

Markdown é excelente porque humanos e LLMs conseguem consumi-lo diretamente.

Mas execution state precisa progressivamente de uma representação mais formal.

O `tasks.md` já possui estados explícitos, seu grafo já é legível por máquina e os relatórios e eventos já carregam campos verificáveis. Não estamos partindo de Markdown sem estrutura.

O próximo passo poderia formalizar relações entre esses estados:

```text
ChangeState
DecisionState
SpecState
TaskState
EvidenceState
DeploymentState
LearningState
```

Por exemplo:

```yaml
change:
  id: recurring-payments
  state: implementing
risk:
  reversibility: one-way
  level: high
  reasons:
    - public-api-change
    - payment-state-migration
artifacts:
  working_backwards: approved
  design: approved
  contracts: approved
  specs: approved
execution:
  current_wave: 3
  tasks:
    total: 23
    done: 17
    running: 4
    blocked: 0
    pending: 2
evidence:
  requirements: 18
  satisfied: 13
  pending: 5
deployment:
  state: not_started
```

Os narrative artifacts continuam em Markdown.

O control plane ganha um estado machine-readable.

Isso permitiria uma série de coisas que hoje são difíceis.

Resume se torna determinístico.

Uma dashboard pode representar lifecycle state.

Policies podem consultar state sem depender de interpretação textual.

Automation pode reagir a transition events.

Audit pode mostrar exatamente quem aprovou uma one-way door.

Multiple agents podem coordenar sem precisar reinterpretar toda a documentação.

Neste ponto, o framework começa a se parecer claramente com um engineering control plane.

## Uma arquitetura unificada

Se juntarmos as melhores ideias de Spec Kit, OpenSpec, Kiro e AI-DLC com aquilo que Build Like Amazon já possui, a arquitetura que emerge é algo assim:

```text
Constitution
  |
  v
Customer
  |
  v
Working Backwards
  |
  v
Intent
  |
  v
Risk Assessment
  |
  v
Change
  |
  +--> Canonical State(t)
  |
  +--> Delta Spec
  |
  v
Design
  |
  +--> Architecture Patterns
  +--> API Contracts
  +--> Operations / Security
  |
  v
Vertical Specs
  |
  v
Execution DAG
  |
  +--> Agent A
  +--> Agent B
  +--> Agent C
  |
  v
Evidence
  |
  v
Converge
  |
  +--> divergence --> DAG
  |
  +--> converged
          |
          v
     Canonical State(t+1)
          |
          v
       Archive
          |
          v
       Deploy
          |
          v
       Operate
          |
          v
 Production Evidence
          |
          v
        Learn
          |
          v
 Implementation Memory
          |
          +--> Future Execution
```

Essa estrutura combina cinco loops diferentes.

O primeiro é:

```text
Customer <-> Intent
```

Estamos resolvendo o problema correto?

O segundo:

```text
Intent <-> Design
```

A solução preserva aquilo que queremos alcançar?

O terceiro:

```text
Spec <-> Implementation
```

Construímos aquilo que prometemos?

O quarto:

```text
Requirement <-> Evidence
```

Temos evidência independente de que a propriedade está satisfeita?

O quinto:

```text
Production <-> Learning
```

O que aconteceu na realidade está alterando como construiremos no futuro?

Acredito que uma arquitetura AI-native madura precisa fechar todos os cinco.

## Por que isso é diferente de apenas ter um agente melhor

Existe uma tentação natural de acreditar que modelos melhores resolverão progressivamente todos esses problemas.

Alguns certamente diminuirão.

Modelos melhores esquecerão menos contexto.

Planejarão melhor.

Cometerão menos erros.

Serão capazes de executar por mais tempo.

Mas isso não elimina a necessidade do sistema.

Um engenheiro extremamente competente ainda utiliza Git.

Ainda utiliza CI.

Ainda utiliza contracts.

Ainda utiliza deployment pipelines.

Ainda utiliza alarms.

Ainda utiliza access control.

Não fazemos isso porque o engenheiro é incapaz.

Fazemos porque:

> Mechanisms scale reliability better than intentions.

O mesmo princípio deve valer para agents.

Quanto mais capaz o modelo, maior a quantidade de trabalho que podemos delegar.

Quanto maior a delegação, mais importante se torna possuir boundaries e feedback loops explícitos.

Agent capability e engineering control não são substitutos.

Na realidade, são complementares.

```text
Higher Autonomy
  |
  v
Higher Required Observability
  +
Stronger Contracts
  +
Better Evidence
  +
Clearer Authority Boundaries
```

Esse é um padrão conhecido em distributed systems.

Quanto mais independentes os componentes, mais importantes tornam-se protocolos claros.

Multi-agent engineering provavelmente seguirá a mesma regra.

## Build Like Amazon não deveria tentar ser um Spec Kit melhor

Essa talvez seja a decisão de posicionamento mais importante.

Se o objetivo do projeto fosse competir diretamente como outro spec framework, boa parte de sua diferenciação desapareceria.

Spec Kit já possui um ecossistema grande, múltiplas integrações, extensions, presets e workflows.

OpenSpec possui um modelo extremamente elegante de semantic change.

Kiro possui um runtime agentic profundamente integrado.

AI-DLC está formalizando adaptive workflows em nível de lifecycle.

O espaço natural do Build Like Amazon é diferente.

Ele organiza explicitamente descoberta do problema, decisões arquiteturais, execução, operação e aprendizado dentro do mesmo modelo. Outros ecossistemas também expandem suas fronteiras; a diferença relevante precisa ser demonstrada pelos mecanismos e pela composição, não pela afirmação de exclusividade.

Sua cadeia é:

```text
Customer
  |
  v
Intent
  |
  v
Decision
  |
  v
Architecture
  |
  v
Specification
  |
  v
Execution
  |
  v
Deployment
  |
  v
Operations
  |
  v
Learning
```

Não é somente Spec-Driven Development.

É uma tentativa de codificar engineering mechanisms ao longo de todo o lifecycle.

E talvez o ponto mais importante seja que esses mechanisms não foram escolhidos apenas para aumentar agent productivity.

Grande parte deles existe para controlar aquilo que acontece quando productivity aumenta.

Working Backwards reduz o risco de construir a coisa errada.

One-way doors aumentam rigor onde rollback é difícil.

API-first reduz coordination ambiguity.

Specs reduzem implementation ambiguity.

Dependency graphs tornam parallelism explícito.

Green-build gates impedem propagação de failures.

Implementation Review reduz self-certified completion.

Progressive rollout reduz blast radius.

Operational Readiness evita lançar software que ninguém consegue operar.

Correction of Errors transforma failure em aprendizado.

Implementation Memory tenta fazer esse aprendizado alterar execuções futuras.

Quando vistos isoladamente, parecem skills.

Quando vistos juntos, formam um control system.

## A melhor descrição talvez seja Engineering OS

Eu ainda usaria "Agent Skills" como nome do projeto porque descreve seu packaging atual e mantém compatibilidade com vários agent ecosystems.

Mas conceitualmente, o projeto está evoluindo em direção a algo semelhante a um **AI-native Engineering Operating System**.

Não OS no sentido de gerenciar processos e memória de uma máquina.

OS no sentido de fornecer primitives e contracts sobre os quais vários executores podem trabalhar.

Um operating system tradicional abstrai hardware e fornece primitives como process, file, memory e permission.

Um engineering operating system para agents poderia fornecer:

```text
Intent
Change
Decision
Policy
Spec
Task
Evidence
Approval
Deployment
Learning
```

Agents seriam executors.

Models seriam replaceable compute.

Kiro, Claude Code, Codex, Copilot ou outros harnesses seriam runtimes.

O engineering state permaneceria acima deles.

Essa separação é particularmente importante porque modelos mudam rapidamente.

Uma organização não deveria precisar reescrever seu processo de engenharia toda vez que troca Claude por GPT ou um IDE por outro.

O framework deve ser mais durável que o executor.

## Como eu pensaria a próxima evolução

Depois dessas releases, uma próxima evolução deveria partir das garantias que já existem.

A arquitetura atual já contém a maioria dos mecanismos importantes.

Eu organizaria esse trabalho ao redor de cinco conceitos. Dois ainda precisam de um modelo próprio; os outros pedem ampliar e conectar mecanismos que já estão implementados.

A primeira é **Canonical State**.

Precisamos de uma representação atual do comportamento e invariants do sistema.

A segunda é **Change / Delta**.

Toda mudança significativa deveria declarar semanticamente o que pretende adicionar, modificar ou remover da realidade conhecida.

A terceira é **Evidence**.

Os relatórios por tarefa e a revisão já persistem evidências. A ampliação seria relacionar cada requisito a seus testes executados, resultados, versões dos artefatos e observações de produção, sem confundir presença de relatório com validação do comportamento.

A quarta é **Runtime Policy**.

As verificações determinísticas existentes podem ser complementadas por interceptação de ações quando a ferramenta de execução oferece esse mecanismo. O processo também precisa continuar declarando onde a garantia é preventiva, onde é detectiva e onde depende apenas de interpretação pelo modelo.

E a quinta é **Convergence**.

Ela já existe distribuída no framework, mas deveria se tornar um conceito de primeira classe.

Essas primitives mudariam a arquitetura do projeto de:

```text
Skill Library
  +
Commands
  +
Agent Rules
  +
Durable Reports
  +
Deterministic Checks
  +
Flow Events
```

para:

```text
Engineering State Model
  |
  +--> Human-readable artifacts
  +--> Machine-readable state
  +--> Workflow semantics
  +--> Policy semantics
  +--> Agent adapters
```

Skills continuariam fundamentais.

Mas se tornariam aplicações dessas primitives.

## Uma extensão possível da estrutura atual

O ponto de partida deve continuar sendo `.bla/`. Uma hipótese seria acrescentar estado canônico, mudanças e políticas à estrutura existente, preservando o endereço dos artefatos já utilizados. A árvore abaixo é uma proposta, não o layout publicado da versão `0.4.0`:

```text
.bla/
  working-backwards/
  design/
  specs/
  reviews/
  deployment/
  operations/
  coe/
  implementation-memory.md
  metrics.jsonl
  constitution/
    engineering.md
    security.md
    operations.md
  canonical/
    payments/
      behavior.md
      invariants.md
      contracts/
    ledger/
      behavior.md
      invariants.md
  changes/
    active/
      recurring-payments/
        change.yaml
        intent.md
        decisions.md
        delta/
        design/
        specs/
        execution/
        evidence/
    archive/
  policies/
    one-way-door.yaml
    security.yaml
    deployment.yaml
  workflows/
    working-backwards.yaml
    design.yaml
    build.yaml
    deploy.yaml
    learn.yaml
  runtime/
    adapters/
      kiro/
      claude-code/
      codex/
      github-actions/
  state.yaml
```

O `change.yaml` carregaria lifecycle state.

Os `.md` carregariam reasoning e narrative.

Evidence seria estruturada.

Policies poderiam possuir enforcement adapters.

Canonical specs seriam atualizadas somente depois de convergence.

Nesse modelo, executar `/build` seria apenas uma operação sobre um state machine maior.

## Production como parte do proof

Essa evolução poderia ir ainda mais longe.

Hoje pensamos em evidence principalmente durante build.

Mas algumas properties só podem ser realmente avaliadas em produção.

Considere:

```text
SLO:
99.9% of operations complete in less than 200 ms.
```

Nenhum unit test consegue provar isso.

Um benchmark local fornece evidence parcial.

Um load test fornece evidence melhor.

Production telemetry fornece evidence mais forte.

Então poderíamos possuir evidence levels:

```text
Requirement
  |
  +--> Design Evidence
  +--> Build Evidence
  +--> Test Evidence
  +--> Deployment Evidence
  +--> Production Evidence
```

Isso permitiria distinguir:

```text
IMPLEMENTATION VERIFIED
```

de:

```text
PRODUCTION VALIDATED
```

Algumas changes só atingiriam estado final depois de uma observation window.

Por exemplo:

```text
DEPLOYED
  |
  v
OBSERVING
  |
  +--> metrics fail --> rollback/change
  |
  +--> metrics pass
          |
          v
       VALIDATED
          |
          v
       ARCHIVED
```

Nesse ponto, o archive semântico deixa de representar apenas "code merged".

Ele representa:

> A organização considera esta mudança incorporada à realidade do sistema e possui evidence suficiente para essa crença.

Essa é uma definição de done muito mais forte.

## Agents como força de multiplicação tornam mecanismos mais importantes

É comum enxergar governance e velocidade como forças opostas.

Essa relação é verdadeira quando governance significa manual approval para tudo.

Mas não precisa ser verdadeira quando governance é implementada como mechanism.

Uma boa API contract aumenta controle e paralelismo ao mesmo tempo.

Um DAG aumenta estrutura e paralelismo.

Automated evidence aumenta rigor e reduz review manual.

Progressive deployment aumenta segurança e permite releases mais frequentes.

Feature flags aumentam controle e reversibility.

Good observability aumenta segurança e reduz tempo de diagnóstico.

O mesmo princípio pode se aplicar a agentic engineering.

O objetivo não deve ser:

```text
More gates
```

Deve ser:

```text
Better mechanisms
```

Um gate humano existe porque determinada decisão ainda exige judgment.

Se no futuro conseguirmos transformar parte daquele judgment em policy + evidence confiável, o gate pode desaparecer.

Isso sugere que adaptive ceremony também pode aprender.

Hoje:

```text
One-way door -> Human review
```

Amanhã, talvez:

```text
One-way door
  |
  +--> known pattern
  +--> evidence complete
  +--> automated checks
  +--> rollback verified
  +--> bounded blast radius
          |
          v
    reduced ceremony
```

Ou seja, a organização pode ganhar velocidade porque os mechanisms ficaram melhores, não porque standards foram relaxados.

Isso é exatamente o tipo de compounding improvement que um engineering system deveria buscar.

## O verdadeiro produto não é código

Se olharmos para todo esse movimento, uma conclusão aparece.

O objeto que estamos tentando otimizar não deveria ser "generated code".

Código é uma representação transitória de uma decisão de engenharia.

A cadeia completa é mais importante:

```text
Intent
  |
  v
Decision
  |
  v
Specification
  |
  v
Execution
  |
  v
Evidence
  |
  v
Reality
```

Qualquer quebra nessa cadeia pode produzir failure.

Intent errado produz produto errado.

Decision errada produz design errado.

Spec ambígua produz implementação divergente.

Execution errada produz bugs.

Evidence fraca produz confiança falsa.

Reality ignorada produz conhecimento stale.

O desafio de AI-native engineering é manter essas representações convergentes enquanto a velocidade de mudança cresce.

Esse é, para mim, o problema fundamental.

## De AI-assisted development para AI-native engineering

AI-assisted development preserva o humano como scheduler.

O humano decide o que fazer.

O humano divide trabalho.

O humano chama a IA.

O humano olha o resultado.

O humano chama a IA novamente.

```text
Human
  |
  +--> plan
  +--> delegate
  +--> inspect
  +--> re-plan
  +--> delegate
  +--> inspect
```

O modelo pode ser extraordinariamente inteligente, mas o throughput do sistema continua limitado pela capacidade humana de orquestração.

AI-native engineering muda a responsabilidade.

```text
Human
  |
  v
Intent + Authority Boundaries
  |
  v
Engineering Control Plane
  |
  v
Agents
  |
  v
Evidence
  |
  v
Human only where judgment is required
```

O humano deixa de ser o executor central do workflow.

Passa a definir intenção, boundaries e decisões de alta entropia.

Agents cuidam do trabalho de baixa entropia.

Mechanisms verificam aquilo que pode ser verificado mecanicamente.

Evidence reduz a necessidade de confiança subjetiva.

Production feedback atualiza o sistema.

Esse é o shift real.

Não é autocomplete melhor.

Não é prompt engineering melhor.

Não é simplesmente Spec-Driven Development.

É uma mudança na arquitetura do próprio processo de engenharia.

## Onde Build Like Amazon se encaixa

O Build Like Amazon Agent Skills começou tentando ensinar agents a aplicar mecanismos de engenharia inspirados em práticas públicas da Amazon.

Mas a composição desses mechanisms produziu algo mais interessante.

Working Backwards introduziu customer intent.

One-way/two-way doors introduziram risk-adaptive decision governance.

Design chains introduziram cross-cutting architectural reasoning.

Pattern impact maps introduziram constraint propagation.

API-first introduziu contract-driven decomposition.

Vertical specs introduziram traceability.

Task waves introduziram parallel execution semantics.

Green-build gates introduziram automated checkpoints.

Coherence Review introduziu pre-execution convergence.

Implementation Review introduziu post-execution convergence.

Progressive deployment introduziu bounded blast radius.

Operational readiness introduziu production responsibility.

Correction of Errors introduziu organizational learning.

Implementation Memory introduziu procedural adaptation.

Brownfield discovery introduziu reconstruction of reality.

Nenhuma dessas primitives, isoladamente, é única.

A diferenciação está na composição:

```text
Customer
  |
  v
Product
  |
  v
Architecture
  |
  v
Specification
  |
  v
Execution
  |
  v
Operations
  |
  v
Learning
```

dentro de um único agent operating model.

Isso é mais amplo do que SDD.

## Ainda existe muito a construir

A coisa mais perigosa ao construir um framework é acreditar cedo demais que o framework está completo.

Não está.

OpenSpec demonstra claramente que Build Like Amazon ainda precisa de uma representação melhor de canonical semantic state e change deltas.

Kiro demonstra que instructions críticas deveriam migrar progressivamente para runtime enforcement.

Spec Kit demonstra o valor de tornar convergence uma primitive explícita e extensible workflows uma camada formal.

AI-DLC demonstra a importância de continuar refinando adaptive depth e manter o processo livre de workflows excessivamente hard-coded.

E todos eles apontam na mesma direção:

> O futuro provavelmente não pertence a um único coding agent.

Pertence a harnesses que coordenam models, context, artifacts, policies, tools, evidence e humans.

O que me interessa no Build Like Amazon é justamente poder explorar essa camada.

## Conclusão

A primeira geração de ferramentas de AI para software respondeu:

> A IA consegue escrever código?

A resposta foi sim.

A segunda geração está respondendo:

> A IA consegue implementar uma feature inteira?

A resposta também está rapidamente se tornando sim.

A próxima pergunta é mais difícil:

> Como uma organização permite que milhares de mudanças sejam executadas por agentes sem perder coerência, contexto, segurança, customer focus e capacidade de aprender?

Não acredito que a resposta seja simplesmente um modelo maior.

Também não acredito que seja escrever specifications mais detalhadas.

Precisamos de um sistema.

Spec Kit nos mostra que intenção precisa convergir com implementação.

OpenSpec nos mostra que mudança precisa ser representada como transformação explícita do estado semântico do sistema.

Kiro nos mostra que specifications podem se aproximar de execution graphs, runtime policies e feedback automatizado.

AI-DLC nos mostra que o lifecycle precisa se adaptar ao risco e que autonomia de execução não implica autoridade irrestrita.

Build Like Amazon acrescenta outra dimensão:

> Engenharia começa antes da specification e termina depois do deployment.

Ela começa no cliente.

Passa por decisões.

Passa por arquitetura.

Passa por código.

Passa por produção.

E volta através do aprendizado.

A arquitetura que quero continuar explorando é menos parecida com:

```text
Prompt
  |
  v
Agent
  |
  v
Code
```

e mais parecida com:

```text
Customer
  |
  v
Intent
  |
  v
Decisions
  |
  v
Canonical State
  |
  v
Change
  |
  v
Design
  |
  v
Executable Specs
  |
  v
Multi-Agent DAG
  |
  v
Evidence
  |
  v
Convergence
  |
  v
Production
  |
  v
Learning
  |
  +--> Future Intent
```

Quando chegarmos a esse ponto, agents deixarão de ser apenas ferramentas que escrevem software mais rápido.

Eles se tornarão participantes de um sistema de engenharia que consegue pensar, executar, verificar, operar e aprender.

E acredito que essa distinção - entre acelerar code generation e construir um engineering system capaz de controlar essa aceleração - será uma das mais importantes na próxima fase do desenvolvimento de software.

É esse problema que o Build Like Amazon Agent Skills está começando a explorar.

A versão `0.4.0` já conecta mecanismos a estado durável, evidências por tarefa, verificações determinísticas, revisão isolada e observação do fluxo.

O próximo salto é conectar essas garantias ao comportamento canônico do sistema, à evolução semântica das mudanças e à evidência observada em produção, ampliando a prevenção em tempo de execução onde as ferramentas permitirem.

E talvez seja justamente aí que a ideia deixe definitivamente de ser apenas uma coleção de Agent Skills.

E passe a ser um verdadeiro AI-native Engineering Operating System.

## Referências editoriais

A revisão do Build Like Amazon foi feita em 30 de setembro de 2026 sobre a versão `0.4.0`, commit `107c3f1`, depois de atualizar a `main` remota. Os links do projeto abaixo estão fixados nesse commit para preservar o estado analisado. As propostas de evolução e a interpretação arquitetural são minhas.

* [Build Like Amazon: histórico de releases](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/CHANGELOG.md) - mudanças de comportamento das versões `0.3.0` e `0.4.0`, incluindo a migração dos artefatos para `.bla/`.
* [Contrato operacional](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/AGENTS.md) - autoridade, pontos de aprovação, escalas de severidade e veredito e documentação de riscos aceitos.
* [Protocolo de construção](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/.claude/commands/build.md) - ondas, relatórios duráveis, dependências bloqueadas, escopo de escrita, revisão isolada e distinção entre encerramento e entrega.
* [Modelo de tarefas](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/skills/spec-driven-implementation/templates/tasks-template.md) - grafo de dependências e conjuntos de escrita.
* [Verificador determinístico](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/tools/bla-check) - verificações implementadas, condições de ativação e limites.
* [Catálogo de artefatos](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/docs/artifact-catalog.md) - produtores, consumidores, caminhos e modelos.
* [Métricas do fluxo](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/docs/flow-metrics.md) - seis eventos, tempo de ciclo, retrabalho e honestidade temporal.
* [Memória procedural](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/skills/implementation-memory/SKILL.md) - limite global de 12 regras, filtro por fase e pontos de seleção implementados ou reservados.
* [Proporcionalidade](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/skills/using-amazon-skills/SKILL.md) - custo explícito por nível de mudança.
* [Integração contínua da biblioteca](https://github.com/robisson/build-like-amazon-agent-skills/blob/107c3f1/.github/workflows/check.yml) - verificações de consistência, localização e paridade entre ferramentas.

* [GitHub Spec Kit Documentation](https://github.github.io/spec-kit/) - visão geral do Spec Kit como harness intent-driven, com integrations, extensions, presets, workflows e bundles.
* [Spec Kit Agentic SDD reference](https://github.com/github/spec-kit/blob/main/docs/reference/agentic-sdd.md) - lifecycle `/speckit.*`, incluindo Constitution, Specify, Plan, Tasks, Implement e Converge.
* [Spec Kit Workflows](https://github.github.io/spec-kit/reference/workflows.html) - workflows com comandos, prompts, shell steps, human checkpoints, loops e fan-out/fan-in.
* [OpenSpec Documentation](https://openspec.dev/docs) - modelo de specs, changes, deltas e archive.
* [OpenSpec FAQ](https://openspec.dev/docs/faq) - delta specs com `ADDED`, `MODIFIED` e `REMOVED`.
* [OpenSpec CLI Reference](https://openspec.dev/docs/reference/cli) - archive aplicando deltas em `openspec/specs/` e movendo changes para archive.
* [Using OpenSpec in an Existing Project](https://openspec.dev/docs/existing-projects) - abordagem delta-first para brownfield.
* [Kiro Hook Types](https://kiro.dev/docs/hooks/types/) - tipos de hooks como `PreToolUse`, `PostToolUse`, `PreTaskExec`, `PostTaskExec` e `Stop`.
* [Kiro Hooks](https://kiro.dev/docs/hooks/) - tabela de triggers e comportamento de bloqueio por exit code.
* [Open-Sourcing Adaptive Workflows for AI-DLC](https://aws.amazon.com/blogs/devops/open-sourcing-adaptive-workflows-for-ai-driven-development-life-cycle-ai-dlc/) - AI-DLC como workflow adaptativo com decisão humana, execução por IA, auditabilidade e checkpoints.
* [Building with AI-DLC using Amazon Q Developer](https://aws.amazon.com/blogs/devops/building-with-ai-dlc-using-amazon-q-developer/) - fases Inception, Construction e Operations, com profundidade adaptada ao tipo de mudança.
