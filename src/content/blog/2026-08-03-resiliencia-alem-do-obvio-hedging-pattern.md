---
title: 'Resiliência além do óbvio #2: Hedging pattern e tail latency'
seoTitle: 'Resiliência além do óbvio #2: Hedging pattern e tail latency'
description: >-
  Entenda como o hedging pattern usa redundância especulativa para reduzir a
  exposição da aplicação à latência de cauda sem amplificar incidentes.
pubDate: 2026-08-03
tags:
  - Resilience
  - Distributed Systems
  - Tail Latency
  - Hedging
  - Architecture
series: Cloud Resilience
language: pt-BR
---
**TLDR:** O hedging pattern aumenta resiliência quando usa redundância especulativa para reduzir a exposição da aplicação à tail latency. Frase um tanto pomposa! não? Mas a ideia é disparar uma tentativa primária de uma request a um destino, esperar um limite baseado no comportamento esperado da operação e, se ela entrar demorar além de um limite, enviar uma segunda tentativa por outro caminho plausivelmente saudável. A aplicação usa a primeira resposta válida e cancela ou limita o custo da tentativa perdedora. Hoje o assunto é hedging pattern e tail latency e já adianto que veremos muitos números no artigo e um pouco de matemática(espero que corretamente).

> Leia a versão em inglês aqui: [Resilience beyond the obvious #2: Hedging pattern and tail latency](/en/blog/2026-08-03-resilience-beyond-the-obvious-hedging-pattern-and-tail-latency/).

Em sistemas distribuídos, nem toda falha aparece como indisponibilidade. Muitas vezes uma dependência continua respondendo, mas responde tarde demais: uma réplica passa por uma pausa de GC, uma fila local cresce, uma rota de rede fica pior, uma partição específica fica momentaneamente mais lenta. O hedging pattern aumenta resiliência quando impede que essa variação localizada defina a experiência final da aplicação.

O padrão só funciona bem quando há independência entre caminhos, idempotência, cancelamento e budget para carga extra. Sem esses controles, o mesmo mecanismo que deveria mascarar uma lentidão localizada pode dobrar tráfego durante uma degradação correlacionada e transformar proteção em amplificação de incidente.

## Do trabalho constante ao hedging pattern

O primeiro artigo da série discutiu o princípio do trabalho constante: evitar que o sistema passe a executar um caminho diferente, pouco exercitado ou mais pesado exatamente durante uma falha. A ideia central era reduzir surpresa operacional. Um sistema resiliente precisa se comportar de forma previsível quando a dependência degrada.

O hedging pattern parte de outra técnica, mas busca o mesmo resultado: previsibilidade. Em vez de manter o volume de trabalho sempre igual, ele permite uma pequena quantidade de trabalho redundante para reduzir a exposição à pior réplica, ao pior caminho ou à pior fila naquele instante.

A diferença essencial é o controle. Hedging não é duplicar tudo. Não é enviar sempre duas requisições. Não é tentar compensar falta de capacidade com mais carga. O padrão só faz sentido quando a redundância é especulativa, idempotente, direcionada a outro caminho plausivelmente saudável e limitada por budget.

## Como o hedging pattern aumenta a resiliência

A resposta curta: hedging aumenta resiliência quando impede que uma lentidão transitória e localizada vire a experiência do usuário. Ele não torna o backend mais rápido por si só, não aumenta capacidade real e não conserta saturação. O que ele faz é diferente: ele reduz a dependência da aplicação em relação ao participante mais lento de uma composição distribuída.

Pense num checkout que precisa buscar dados de risco, saldo e histórico antes de responder. Todas as dependências estão disponíveis. Nenhuma caiu. Só que uma réplica do serviço de histórico entrou numa pausa de GC, ou pegou uma fila local ruim, ou ficou presa num caminho de rede momentaneamente pior. Sem hedging, o checkout inteiro herda esse azar: o usuário espera pela réplica lenta. Com hedging, depois do p95 a aplicação tenta outro caminho e usa a primeira resposta válida. O soluço continua existindo dentro do sistema, mas deixa de atravessar a fronteira até o usuário.

![Imagem 1 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-13.png)

Hedging não substitui redundância. Ele usa a redundância existente de forma seletiva para absorver variação interna. Sem hedge, uma réplica lenta pode decidir a latência percebida; com hedge, outra réplica saudável pode responder antes que a requisição lenta defina a experiência.

É aqui que o padrão entra na conversa de resiliência, e não só de performance. Resiliência não é apenas continuar disponível quando algo falha completamente. Em sistemas distribuídos, muita coisa não cai: ela fica lenta, parcial, intermitente ou assimétrica. O hedging atua nessa faixa de degradação. Ele transforma um problema que seria visível para o usuário em uma variação interna mascarada.

Um modelo pequeno em Python mostra a mecânica sem nenhuma biblioteca:

```python
def without_hedging(primary_ms):
    return primary_ms

def with_hedging(primary_ms, backup_ms, hedge_after_ms):
    if primary_ms <= hedge_after_ms:
        return primary_ms

    return min(primary_ms, hedge_after_ms + backup_ms)

history_primary = 180  # replica com GC pause
history_backup = 24    # outro caminho saudavel
hedge_after = 30       # p95 esperado para essa chamada

print(without_hedging(history_primary))                 # 180 ms
print(with_hedging(history_primary, history_backup, hedge_after))  # 54 ms
```

O segundo número não é `24 ms`, porque o hedge não dispara no tempo zero. A aplicação espera `30 ms` antes de decidir que a primeira tentativa entrou na cauda, então paga `30 + 24 = 54 ms`. Isso é o desenho correto: o sistema não duplica trabalho para todo o tráfego; ele compra uma rota de escape apenas quando a tentativa original já passou do limite esperado.

Repare também no que esse exemplo não promete. Se o serviço de histórico inteiro estiver saturado, ou se as duas réplicas baterem na mesma partição quente, o backup não volta em `24 ms`; ele volta lento também. Nesse caso, hedging não absorve a variação, ele amplifica carga. Por isso o padrão só aumenta resiliência quando três coisas são verdade ao mesmo tempo: existe outro caminho com chance real de ser saudável, a operação tolera duplicação, e a carga extra tem teto. Fora disso, ele deixa de ser proteção e passa a piorar o incidente.

## O que é hedging pattern

O nome vem do mundo financeiro: um **hedge** é uma aposta de proteção, algo que você faz em paralelo para cobrir o risco da aposta principal. Aplicado a requisições, o conceito é: em vez de mandar a requisição para uma réplica e depender de ela ser rápida, mande para mais de uma e use a primeira resposta que chega&#x72;**.** Com o paper de Dean e Barroso, [The Tail at Scale](https://cseweb.ucsd.edu/classes/sp18/cse124-a/post/schedule/p74-dean.pdf) se popularizou uma ideia que eles chamaram de hedging, simplesmente enviar a mesma requisição para múltiplos lugares e usar o primeiro a responder, só que não tão simples quando isso e recomendo a todos a leitura do paper.

Se isso for feito de forma ingênua, disparando duas requisições sempre, para todo o tráfego, você acabou de **dobrar a carga do seu sistema**. Cada requisição virou duas. Isso é caro e, como veremos na análise sobre amplificação de incidentes, pode ser catastrófico. O ponto importante do paper não está na ideia de executar sobre múltiplias réplicas. Está no refinamento que torna a ideia barata: você **não dispara o outra request(backup) imediatamente**. Você manda a primeira requisição e só dispara a segunda **depois de um pequeno atraso**, e cancela as pendentes assim que a primeira resposta boa chega.

Um ponto de partida comum para o atraso de disparo é o **p95**. Você adia a requisição secundária até que a primeira já tenha ultrapassado o p95 esperado para aquela classe de requisição. A lógica é direta: se a primeira requisição respondeu antes do p95, você não envia a segunda e não adiciona carga. Apenas nos casos em que a primeira tentativa entra na cauda o hedge dispara. Por definição, isso limita a carga extra a aproximadamente 5% enquanto encurta a cauda. O custo do trabalho redundante é pago onde ele efetivamente reduz exposição à latência de cauda.

O diagrama abaixo mostra o fluxo completo. Note o momento exato em que o cliente decide disparar o hedge — não no início, mas depois de esperar até o p95 — e o cancelamento da réplica lenta assim que a rápida responde.

![Imagem 2 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-02.png)

Uma armadilha de implementação é a segunite, **o hedge só ajuda se ele chegar em outro lugar**. Isso não acontece automaticamente. Se a segunda requisição usa o mesmo hostname, a mesma chave e o mesmo cliente HTTP, ela pode cair no mesmo backend lento. As causas mais comuns são o pool de conexão keep-alive, que reaproveita a conexão já aberta; o consistent hashing, que roteia a mesma chave para o mesmo nó; e a sticky session no balanceador. A regra prática é forçar explicitamente uma conexão nova ou uma réplica diferente da primeira. Um hedge que cai no mesmo lugar não é proteção; é carga adicional no mesmo gargalo.

O resultado mais citado do paper mostra a magnitude desse efeito. Num benchmark que lê 1.000 chaves distribuídas em 100 servidores de armazenamento, enviar um hedge após apenas 10 ms de atraso reduziu a latência de p99.9 de 1.800 ms para 74 ms, com apenas 2% de requisições extras. Uma redução de 96% na cauda por 2% de trabalho a mais. É uma troca grande o suficiente para justificar atenção arquitetural.

Se você quiser ver o efeito na distribuição inteira em vez de num único percentil, a distribuição mostra melhor por que isso não significa que o sistema inteiro ficou mais rápido, e desmente a ideia de que hedging "deixa o sistema mais rápido":

![Imagem 3 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-08.png)

Simulação com 5% de azar e réplicas independentes. À esquerda, a fração de requisições acima de cada latência (escala log): as duas curvas são idênticas até o p95 e só divergem na cauda. O hedge não toca o p50 (9 ms → 9 ms) nem o p95 (23 ms → 23 ms); ele reduz o p99 (170 ms → 35 ms) e o p99.9 (490 ms → 70 ms). Hedging não acelera o corpo da distribuição — ele reduz a cauda, a um custo de \~5% de requisições extras.

O mecanismo por trás desse ganho desproporcional é a matemática da cauda trabalhando a favor da aplicação, com uma condição central, **as duas lentidões precisam ser independentes.** Sob independência, a conta é favorável: se cada réplica tem 1% de chance de estar lenta, as duas ao mesmo tempo têm 0,01% — uma em dez mil. A probabilidade de azar simultâneo foi elevada ao quadrado. Mas a palavra importante é *independentes*: ela volta na análise de aplicabilidade, porque é a hipótese mais frágil do artigo inteiro.

## Hedging, retry, failover e constant work

Hedging fica mais claro quando comparado com retry, failover e constant work. As quatro técnicas lidam com degradação, mas cada uma tem gatilho, escopo e risco diferentes.

|                       | **Hedging**                                                   | **Retry**                             | **Failover**                              | **Constant work**                  |
| --------------------- | ------------------------------------------------------------- | ------------------------------------- | ----------------------------------------- | ---------------------------------- |
| **Quando dispara**    | Passou do p95, com a original **ainda em voo**                | Depois da falha ou do timeout         | Réplica/região declarada indisponível     | Nunca "dispara": roda sempre igual |
| **Postura**           | Proativo                                                      | Reativo                               | Reativo                                   | Nem uma coisa nem outra            |
| **O que resolve**     | Cauda de latência localizada                                  | Falha transitória e isolada           | Disponibilidade                           | Previsibilidade sob falha          |
| **Como falha feio**   | Dobra a carga sob latência correlacionada (falha metaestável) | Retry storm                           | Secundário sem capacidade                 | Desperdício em regime normal       |
| **Freio obrigatório** | Token bucket + cancelamento + deadline                        | Backoff + jitter + teto de tentativas | Capacidade reservada e testada no destino | — (não há modo a frear)            |

A distinção mais importante é o eixo proativo-reativo. **Retry é reativo**: ele espera a requisição falhar (um erro, um timeout) para então reenviar. **Hedging é proativo**: ele não espera falhar, ele reage à *lentidão*, disparando um backup enquanto a original ainda está viva e pode até responder. Se o seu problema são requisições que vão terminar, mas 10 vezes mais devagar por azar transitório, o retry é a ferramenta errada: você esperaria a coisa falhar (o que talvez nunca aconteça) e, ao reenviar, adicionaria carga a um backend já sob pressão. Já se o seu problema são **falhas** de verdade (a réplica retornou erro, a conexão caiu), o retry é o certo, e o hedge não faz sentido porque não há lentidão a contornar.

Hedging e retry pertencem à mesma família mecânica: ambos enviam trabalho adicional. O que os separa não é a mecânica, é o gatilho, falha (retry) versus lentidão (hedge), e é o gatilho que decide qual risco você corre e qual freio você precisa. Por isso eles compartilham o mesmo mecanismo de contenção, que pode ser um token bucket. Para o backend, um hedge e um retry são indistinguíveis, os dois são carga extra. A própria AWS trata as duas como estratégias distintas justamente nesse ponto: o hedging difere do retry do SDK, que só reenvia quando ocorre um timeout ou quando certo limite é atingido, quando usando adaptive retry, enquanto o hedge dispara a segunda requisição com a primeira ainda em voo, depois de um limite/risco calculado.

Failover e constant work completam o quadro. **Failover** também é reativo, mas opera numa granularidade maior, ele redireciona tráfego quando uma réplica ou região inteira é declarada indisponível; é uma ferramenta de disponibilidade, não de latência de cauda. E **constant work**, o tema do [post anterior](https://www.robissonoliveira.com.br/blog/2026-07-29-como-o-principio-trabalho-constante-aumenta-a-resiliencia-das-aplicacoes/) desta série, é a única das quatro que não dispara em resposta a nada, ela roda continuamente, no mesmo ritmo. Constant work reduz variabilidade mantendo trabalho estável, hedging reduz variabilidade adicionando trabalho redundante sob limites rígidos.

## Saber a média não respondi todas as perguntas

Essa é uma das armadilhas mais comuns em engenharia: perguntar "qual é a média?" como se ela representasse o que os usuários sentem. Para throughput, custo ou volume agregado, média pode ser uma boa pergunta. Para latência, ela frequentemente é a pergunta errada. A média pode estar ótima e, ao mesmo tempo, uma fatia relevante dos clientes pode estar sofrendo. E a distância entre essas duas coisas é onde mora este artigo inteiro.

Antes de entrar na matemática, precisamos esclarescer alguns pontos. Se `p50`, `p95` e `p99` ainda parecem siglas abstratas de dashboard, vale estabelecer essa base primeiro. Percentis são a linguagem usada para falar de experiência real em sistemas distribuídos, e sem eles o hedging parece só uma duplicação de trabalho sem critério.

Vamos a pergunta para reflexão: **se uma única requisição lenta pode definir a experiência do usuário em sistemas distribuídos, por que ainda é tão comum se apoiar na latência média?**

## Percentis: a linguagem da experiência

Quando você mede latência, está olhando para uma lista de tempos. Imagine 100 requisições: algumas responderam em 8 ms, outras em 12 ms, algumas em 40 ms, uma ou outra em 200 ms, talvez uma em 1 segundo. A média pega tudo isso, soma e divide. O problema é que ela perde a forma da distribuição: duas listas podem ter a mesma média e experiências completamente diferentes. Uma pode ser estável para quase todos os usuários; outra pode ser rápida para muitos e péssima para uma minoria. Percentil faz outra coisa, ordena as requisições da mais rápida para a mais lenta e pergunta em que ponto da lista a medição est&#xE1;**.**

O `p50`, também chamado de mediana, é o ponto em que metade das requisições ficou abaixo e metade ficou acima. Se o `p50` é 12 ms, isso quer dizer: 50% das requisições responderam em até 12 ms. O `p95` é o ponto em que 95% ficaram abaixo e 5% ficaram acima. O `p99` é o ponto em que 99% ficaram abaixo e 1% ficou acima. A fórmula mental é simples:

> **pX = X% das requisições foram mais rápidas ou iguais a esse tempo.**

Então `p99 = 200 ms` não quer dizer "quase todo o tráfego viu 200 ms". Quer dizer o contrário: 99% ficaram em até 200 ms, e o 1% restante ficou **pior** que isso. É por isso que percentil é tão útil para latência: ele não esconde a parte ruim dentro de uma média confortável.

![Imagem 4 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-12.png)

Percentis são posições numa lista ordenada de latências. O p50 descreve o corpo da distribuição; p95 e p99 mostram onde a cauda começa a doer.

Um exemplo pequeno deixa isso claro. Imagine estas dez latências, já ordenadas:

```text
10 ms, 11 ms, 11 ms, 12 ms, 12 ms, 13 ms, 14 ms, 15 ms, 18 ms, 500 ms
```

A média é `60,6 ms`. Ela parece dizer "o serviço está na casa de dezenas de milissegundos". Mas ninguém teve exatamente essa experiência. Nove chamadas ficaram entre 10 e 18 ms; uma chamada levou 500 ms. A média misturou duas realidades diferentes e inventou um número que não representa nenhuma delas. Pior, ela suavizou justamente o evento que define a percepção de quem pegou a pior rota. Percentis deixam essa mistura explícita, o corpo está rápido, a cauda está ruim.

Se você quiser calcular percentis em código, a versão conceitual é esta:

```python
import math

def percentile(values, p):
    sorted_values = sorted(values)
    index = math.ceil((p / 100) * len(sorted_values)) - 1
    index = max(0, min(index, len(sorted_values) - 1))
    return sorted_values[index]

latencies = [10, 11, 11, 12, 12, 13, 14, 15, 18, 500]

print(percentile(latencies, 50))  # 12
print(percentile(latencies, 90))  # 18
print(percentile(latencies, 99))  # 500
```

Esse exemplo não é uma implementação perfeita para todos os métodos estatísticos de percentil, bibliotecas diferentes interpolam de formas diferentes, mas ele captura a ideia principal, **ordene as latências e olhe para a posição que você prometeu proteger.** Para resiliência, essa pergunta importa mais do que a média, porque o usuário não sente a média. Ele sente a requisição dele.

Em escala, 1% não é pequeno. Num sistema com 5.000 requisições por segundo, `p99` representa 50 requisições por segundo acima daquele limite. São 3.000 por minuto. Mais de 4 milhões por dia. É por isso que a cauda da distribuição não é um detalhe estatístico, é uma parcela contínua de usuários atravessando a pior versão do seu sistema.

Gosto muito da [perspectiva do Marc Brooker](https://brooker.co.za/blog/2021/04/19/latency.html) onde podemos cometer o erro de olhar para o p99.9 e concluir que ele não importa, afinal 999 de 1000 chamadas veem uma latência menor que aquela. O problema, como ele mesmo completa, é que arquiteturas modernas têm muitos componentes, então uma única interação de usuário pode se traduzir em muitas chamadas de serviço.

É essa multiplicação que acontece em sistemas distribuídos que transforma um evento raro num evento comum.

## Tail latency: quando a cauda domina o sistema

Retomando e aprofundando um pouco mais, em 2013, Jeffrey Dean e Luiz André Barroso publicaram na Communications of the ACM o artigo [The Tail at Scale](https://cseweb.ucsd.edu/classes/sp18/cse124-a/post/schedule/p74-dean.pdf). O paper introduz um conceito central para este tema, assim como sistemas distribuídos são projetados para serem tolerantes a falhas, eles também precisam ser projetados para serem **tolerantes à cauda de latência**, ou seja, **tail-tolerant**, construindo um todo previsivelmente responsivo a partir de partes que, individualmente, são menos previsíveis. O paralelo é direto, a computação tolerante a falhas cria um todo confiável a partir de partes menos confiáveis, a tolerância à cauda cria um todo responsivo a partir de partes menos responsivas.

Pela natureza dos sistemas distribuídos e suas múltiplas partes, há muito variabilidiade, vale entender por que essa variabilidade é inevitável, é consequência de sistemas compartilhados. Contenção por recursos compartilhados, processos em segundo plano que consomem CPU por alguns milissegundos, atividades de manutenção como compaction e garbage collection, enfileiramento em várias camadas, e até características de hardware. Um exemplo do próprio paper, o garbage collection de um SSD pode aumentar a latência de leitura por um **fator de 100** com apenas uma atividade de escrita modesta acontecendo em paralelo.

Imagine um servidor que normalmente responde em 10 milissegundos, mas cujo p99 é de 1 segundo — ou seja, 1 em cada 100 respostas é lenta. Se uma requisição de usuário toca apenas um desses servidores, só 1 em cada 100 requisições fica lenta. Em uma leitura isolada, 1% parece pequeno. Mas sistemas distribuídos raramente tocam um servidor só. Eles fazem fan-out, disparam dezenas ou centenas de chamadas em paralelo e precisam esperar todas voltarem para montar a resposta final, como numa busca que consulta 100 shards ou numa timeline que agrega dezenas de serviços. Nesse cenário, se você precisa coletar respostas de **100 servidores em paralelo**, então **63% das requisições de usuário vão levar mais de um segundo**.

Essa requisição ou réplica que fica muito mais lenta que as demais é chamada de **straggler**. Dá para pensar nela como a chamada atrasada do conjunto, ela não necessariamente falhou, só ficou para trás. O problema é que, em fan-out, a resposta final costuma esperar justamente quem ficou para trás.

Um por cento de lentidão em cada servidor individual virou sessenta e três por cento de requisições lentas no nível do usuário. Se cada chamada tem 99% de chance de ser rápida, a chance de **todas** as 100 serem rápidas é `0,99` multiplicado por si mesmo 100 vezes:

* P(nenhum dos 100 é lento) = `(0,99)¹⁰⁰ ≈ 0,366`
* Logo, P(pelo menos um é lento) = `1 − 0,366 ≈ 0,634`, ou seja, **63,4%**.

A fórmula geral vale a pena gravar é:

> **P(pelo menos uma requisição atrasada) = 1 − (1 − p)ᴺ**
>
> `p` = probabilidade de uma chamada individual ser lenta · `N` = tamanho do fan-out.
> A cauda não soma: ela **compõe**. É por isso que ela cresce tão rápido.

A imagem abaixo mostra essa curva para três níveis de "azar" individual, e o que ela mostra é que, quanto mais a sua arquitetura se espalha, mais a cauda deixa de ser exceção e vira regra.

![Imagem 5 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-01.png)

P(≥1 lento) = 1 − (1 − p)ᴺ, para três níveis de azar individual. Com p = 1% (azul), 100 chamadas em paralelo levam 63% das requisições de usuário a pegar uma chamada atrasada. Mesmo com p = 0,1% (verde) a cauda alcança você, só precisa de mais fan-out. Com p = 5% (vermelho), pouco mais de 50 chamadas já bastam para passar de 90%.

\[\[tail-latency-simulator]]

Repare no formato com `p = 1%`, você sai de 1% de impacto em uma chamada para quase 10% em dez chamadas, e para 63% em cem. Mesmo com uma probabilidade dez vezes menor (`p = 0,1%`, a linha verde), a cauda aparece quando o fan-out cresce. O paper mostra esse extremo de forma ainda mais dramática: mesmo com apenas 1 em 10.000 requisições excedendo 1 segundo no nível de servidor, um serviço com 2.000 servidores verá **quase 1 em 5** requisições de usuário passando de 1 segundo.

Além do fan-out paralelo, existe o encadeamento serial. Quando um serviço chama outro, que chama outro, a latência final é a **soma** das latências da cadeia. Imagine dois mundos com a mesma média, um com uma cauda bimodal (99% das chamadas em torno de 10 ms, 1% em torno de 100 ms) e outro sem cauda nenhuma, a simples existência daquela cauda rara de 1% faz a variância da distribuição para a qual a cadeia converge (pelo teorema central do limite) ser 25 vezes maior do que seria no mundo sem cauda. Seja no paralelo, seja no serial, a cauda aparece na composição.

A implicação prática é, otimizar um serviço isolado pode não mover o p99 do sistema se o problema estiver na composição do fan-out. Ao invés de perguntar "qual serviço está lento?" devemos perguntar "como o sistema evita depender do participante mais lento daquela composição?". E é essa a pergunta que o hedging responde.

![Imagem 6 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-09.png)

A mesma requisição, com um único azarado entre 20 chamadas paralelas. Sem hedging, a resposta ao usuário é ditada pelo mais lento (168 ms). Com um hedge disparado no p95, o straggler é contornado e a resposta cai para 34 ms, sem que nenhuma das outras 19 chamadas mude. Você não espera a média; você espera o pior caso.

## Por que o p95 é um bom ponto de partida

No Tail at Scale, Dean e Barroso mediram um serviço real do Google com uma árvore de fan-out, uma raiz que fala com servidores intermediários, que por sua vez falam com um grande número de *leaf servers* (as folhas que efetivamente fazem o trabalho). Eles mediram a latência em três momentos: quando uma folha qualquer termina, quando 95% das folhas terminam, e quando 100% terminam.

O p99 de uma requisição individual, medido na raiz, é de meros 10 ms. Mas o p99 para todas as requisições daquele fan-out terminarem é de 140 ms. E o p99 para 95% terminarem é de 70 ms. Faça a conta, a diferença entre esperar 95% (70 ms) e esperar 100% (140 ms) é de 70 ms — exatamente metade do total. Esperar pelos 5% mais lentos das requisições é responsável por metade da latência total de p99.

Em termos práticos, metade da latência de cauda vem dos últimos 5% de respostas. Se existir uma forma controlada de não esperar por esses 5%, por exemplo, contornar o servidor que teve uma pausa de GC ou um pico de contenção naquele instante, a aplicação reduz parte importante da exposição à cauda. Esse é o problema que o hedging pattern atacam.

## A importância da independência entre caminhos

Volte à lista de causas de variabilidade e avalie quantas são independentes entre réplicas. Contenção por recurso compartilhado? Não, é o mesmo switch, o mesmo volume, o mesmo nó de banco atrás das duas réplicas; Atividade de manutenção disparada pelo mesmo agendador? Correlacionada; Uma partição quente? Aí é o oposto de independente, as duas réplicas servem a mesma partição, então fazer o hedging manda a cópia direto para o mesmo gargalo.

A matemática deixa isso preciso. Para dois eventos de mesma probabilidade `p` e coeficiente de correlação `ρ`:

> **P(A lenta E B lenta) = p² + ρ · p · (1 − p)**

Com `ρ = 0`, você recupera o `p²` do slide. Mas basta um pouco de correlação para o termo linear dominar, porque `p²` é minúsculo e `p·(1−p)` não é. Com `p = 1%`:

| `ρ`              | P(as duas lentas) | equivale a  | P(B lenta \| A lenta) |
| ---------------- | ----------------- | ----------- | --------------------- |
| 0 (independente) | 0,010%            | 1 em 10.000 | 1%                    |
| 0,1              | 0,109%            | 1 em \~917  | 11%                   |
| 0,3              | 0,307%            | 1 em \~326  | 31%                   |
| 1,0 (idêntico)   | 1,0%              | 1 em 100    | 100%                  |

Leia a última coluna, com correlação de apenas 0,3, saber que A está lenta significa **31% de chance** de B também estar. A probabilidade que parecia quadrática voltou a se comportar quase como um risco linear. Por isso falamos tanto de independência quando o assunto é resiliência(réplicas em AZs diferentes, hosts diferentes, caminhos de rede diferentes), ela é literalmente a tentativa de empurrar o `ρ` para perto de zero. E por isso hedging contra uma partição quente não ajuda — lá o `ρ` é 1, e a segunda tentativa apenas adiciona carga ao gargalo.

## Tied requests: cancelamento antes do trabalho duplicado

O hedging pattern tem uma pequena janela de vulnerabilidade que vale a pena entender, porque ela motiva uma variação mais sofisticada. Entre o instante em que você dispara o backup e o instante em que a primeira resposta chega, **os dois servidores podem estar executando a mesma coisa ao mesmo tempo**. Você limita esse desperdício esperando o p95 antes de disparar, mas isso, por sua vez, restringe o benefício a uma fração pequena das requisições, só àquelas que passaram do p95. É um balanço: quanto mais você espera, menos desperdício e menos benefício; quanto menos espera, mais dos dois.

Ainda no The Tail at Scale, oa autores propõem uma alternativa mais agressivam, os **tied requests** ("requisições amarradas"). Uma observação importante, **tied requests não são um padrão de cliente.** Você não implementa isso sozinho, do seu lad&#x6F;**.** Eles exigem três coisas que estão fora do alcance de quem é apenas o cliente de uma requisisção: 1/ modificar os servidores para entender uma marcação da request de backup e disparar o cancelamento da request; 2/ um canal de cancelamento servidor-a-servidor entre as réplicas; e 3/ que a rede entre elas seja mais rápida que o trabalho a economizar. É por isso que essa técnica mora dentro de sistemas de armazenamento e bancos distribuídos, construída pelos donos do servidor, e não numa lib de cliente. A seção serve como referência do que é possível quando você controla os dois lados.

Nesse cenário, você envia a requisição para dois servidores praticamente ao mesmo tempo, mas cada cópia carrega a identidade do outro servidor, elas estão "amarradas" uma à outra. O truque está no momento do cancelamento, quando um dos servidores começa a executar a requisição (não quando termina, quando começa a processar, tipicamente ao tirá-la da fila local), ele imediatamente manda uma mensagem de cancelamento para o seu par. O paper ainda acrescenta um detalhe prático: o cliente introduz um pequeno atraso, cerca de duas vezes a latência média de mensagem na rede, entre as duas cópias, para evitar que ambas sejam enviadas quando as duas filas estão vazias.

Por que cancelar ao começar e não ao terminar faz tanta diferença? Porque a maior parte da latência de cauda vem de tempo de fila, não de tempo de processamento. Um servidor está lento geralmente porque a requisição ficou esperando atrás de outras na fila, não porque a operação em si é lenta. Ao amarrar as duas cópias e cancelar no instante em que uma sai da fila e começa a rodar, você garante que o trabalho de fato só é feito por quem pegou a requisição primeiro, os dois servidores quase nunca chegam a processar em duplicata.

![Imagem 7 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-03.png)

O resultado empírico é o que sustenta o argumento. Nos cenários testados no paper, o overhead dos tied requests em utilização de disco ficou **abaixo de 1%**, indicando que a estratégia de cancelamento elimina de forma efetiva as leituras redundantes. O dado operacionalmente mais relevante é que o perfil de latência de um cluster ocupado, rodando um job de ordenação pesado em paralelo e usando tied requests, ficou quase idêntico ao perfil de um cluster **ocioso** sem tied requests. A leitura prática é que o sistema consegue absorver mais trabalho na mesma frota, com melhor utilização e menor custo, sem expor os clientes à cauda de latência. Não podemos nos esquecer também que estamos falando de jornadas de requisições que podem cruzar varias camadas de serviços distribuídos e por isso essa abordagem compensa como vimos nos números.

## Reduzindo exposição à cauda no fluxo de autorização com hedging pattern no Amazon DynamoDB

Teoria e paper são importantes, mas o valor arquitetural do hedging aparece melhor quando olhamos para um fluxo crítico. Num [post da AWS](https://aws.amazon.com/blogs/database/how-global-payments-inc-improved-their-tail-latency-using-request-hedging-with-amazon-dynamodb/), uma processadora de pagamentos que roda uma plataforma de autorização de cartão de crédito sobre o DynamoDB, projetada para lidar com centenas de milhões de transações por dia e picos de 5.000 transações por segundo, encontrou um problema típico de sistemas distribuídos em escala, a plataforma cumpria o SLA até o percentil 95, mas apresentava latências elevadas no p99 e no p99.9 durante testes de performance.

O ponto é que um fluxo de autorização ficou menos exposto ao pior comportamento transitório da dependência. Em 5.000 TPS, p99 representa cerca de 50 transações por segundo acima daquele limite. Em um sistema de pagamentos, isso significa dezenas de autorizações por segundo atravessando a pior versão daquele caminho naquele instante. Reduzir essa exposição é um ganho de resiliência, menos transações passam a depender do caminho mais lento da dependência.

Foi usado hedging pattern quando a requisição inicial ao DynamoDB ultrapassava um limiar de tempo, o sistema disparava automaticamente uma segunda requisição e usava a que respondesse primeiro. O resultado relatado foi uma **redução de 30% na latência de p99**. Esse número importa porque mede a redução da cauda, mas a leitura arquitetural é mais importante, menos transações ficaram presas no pior caminho disponível naquele momento.

Também é importante entender o tipo de independência e risco que esse caso assume. Fazer hedging no SDK contra o DynamoDB não é escolher explicitamente duas réplicas independentes. É o mesmo endpoint, a mesma chave e a mesma partição lógica. O próprio post deixa claro que, nesse desenho, o hedging no SDK endereça principalmente latência de rede, uma nova conexão, outro caminho e outra posição na fila, não contenção no nó de armazenamento. O ganho vem de reduzir exposição a jitter e variação transitória do caminho, não de resolver saturação estrutural da partição.

O que eu achei mais interessante é a análise do **delta value**, o tempo de espera antes de disparar o hedge. Foi testado diferentes pontos de disparo usando `GetItem`, medindo tanto a melhoria de p99 quanto a taxa de requisições duplicadas. O resultado mostra por que hedging precisa ser calibrado como mecanismo de resiliência, não como duplicação agressiva:

![Imagem 8 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-05.png)

No eixo azul, a melhoria de latência no p99; nas barras, a taxa de requisições duplicadas. O P80 é o ponto de equilíbrio: 29% de ganho por 8% de duplicação. A partir dali o ganho estaciona e regride (26% no P60 e no P50) enquanto a duplicação dispara para 27%.

O P90 trouxe 23% de ganho com 7% de duplicatas. O P80 trouxe 29% de ganho com 8%. A partir daí, antecipar mais o hedge deixou de comprar resiliência proporcional: no P50, o ganho caiu para 26% enquanto a duplicação subiu para 27%. No caso do DynamoDB que é cobrado por chamada, essa duplicação é custo direto. Na escala do próprio caso, 5.000 TPS com 8% de duplicação significam cerca de 400 leituras extras por segundo; com 27%, são 1.350 leituras extras por segundo.

Hedging aumentou a resiliência do fluxo ao reduzir a exposição de transações à cauda de uma dependência, dentro de um limite de duplicação medido. O padrão não deve ser avaliado apenas por "quanto reduziu o p99", mas por quanto reduziu a propagação de variação interna sem transformar essa proteção em carga descontrolada.

## Há um porém, trabalho redundante amplifica incidentes

Hedging é, no fundo, retry especulativo antecipado, você dispara cópias de requisições na expectativa de que uma delas seja mais rápida que um alvo projetado. Requisição redundante feita sem freio não é resiliência; é um mecanismo de amplificação de carga.

Para entender o risco assumido, vale comparar com **retry**. O problema é o que acontece quando o retry encontra um sistema já sob estresse, consome ainda mais recursos e produz um **retry storm**, por isso a recomendação é fazer retry adaptativo com token bucket, seguindo as práticas mais recentes. O retry existe para aumentar a chance de sucesso, mas sob sobrecarga pode consumir os recursos que o serviço precisaria para se recuperar.

Esse efeito tem um nome: **metastable failure( falha metaestável)**. Um sistema entra em estado metaestável quando três coisas se alinham: ele está num estado vulnerável (perto do limite de capacidade), um gatilho causa uma sobrecarga temporária, e essa sobrecarga dispara um **efeito sustentador** — tipicamente uma amplificação de trabalho vinda de uma otimização do caminho comum, como retries ou requisições redundantes. A característica que torna a falha metaestável tão perigosa é esta: **o sistema não se recupera sozinho mesmo depois que o gatilho original desaparece**. O ciclo de amplificação se auto-alimenta e mantém o sistema degradado. O diagrama abaixo mostra a anatomia desse loop.

![Imagem 9 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-06.png)

Um loop metaestável típico: o mecanismo que deveria mascarar a falha passa a amplificar trabalho justamente quando o serviço já está sob estresse.

No post sobre [Erasure coding VS tail latency](https://brooker.co.za/blog/2023/01/06/erasure.html), conecta-se isso diretamente com o hedging. Ele aponta que o argumento do paper original de que o hedging não adiciona carga significativa tende a desmoronar justamente nos casos de falha, quando aplicamos a lente das falhas metaestáveis. O cenário que ele descreve é plausível: a regra do p95 normalmente limita os hedges a uns 5%, mas e se houver um aumento de latência correlacionado no sistema inteiro, causado por tráfego, uma falha de infraestrutura ou um cache vazio, que eleve todas as latências até o p95 esperado? Nesse momento, até a expectativa de p95 ser atualizada, o tráfego pode dobrar e ainda receber tráfego de cancelamento por cima. Ou seja, o hedging, calibrado para disparar nos 5% mais lentos, dispara para **100%** das requisições exatamente durante um incidente, o pior momento para dobrar a carga.

### Por que 5% de carga extra não custa 5% de latência

Uma dúvida comum que pode surgir é, se vou fazer outra request para os 5% mais lento, isso não vai me custar o mesmo de latência ? Existe um erro comum nessa pergunts. Quando se diz que o hedging custa só 5% de requisições extras, o custo está sendo medido na unidade errada. O impacto real aparece em latência, e a relação entre carga e latência depende de onde o sistema está na curva de utilização.

A intuição vem da teoria de filas. Vamos pegar por exemplo o modelo M/M/1, que diz que quanto mais rapido chega trabalho em relação a capacidade de executar, mais tempo cada trabalho fica esperando. O tempo de resposta escala com `1 / (1 − ρ)`, onde `ρ` é a utilização do recurso. Enquanto `ρ` está baixo, adicionar carga quase não dói. Perto da saturação, a curva vira uma parede:

| Utilização `ρ` | Fator de espera `1/(1−ρ)` | Depois de +5% de carga |
| -------------- | ------------------------- | ---------------------- |
| 0,50           | 2,0×                      | 2,1×                   |
| 0,80           | 5,0×                      | 6,3× (**+25%**)        |
| 0,90           | 10×                       | 18× (**+82%**)         |
| 0,95           | 20×                       | 400× (**+1900%**)      |

Leia a linha de `ρ = 0,90`: 5% de carga extra não custam 5% de latência; custam **82%**. É por isso que o hedge é perigoso sob estresse. Em regime normal (`ρ` baixo) ele pode ser praticamente gratuito. Mas se o hedging dispara justamente quando as coisas estão lentas, e lento muitas vezes significa `ρ` alto. A aplicação injeta os 5% no ponto da curva onde 5% de carga viram uma fração grande de latência, o que faz mais requisições passarem do p95 e dispara mais hedges. É o loop metaestável do diagrama, agora com a conversão explícita, carga extra se transforma em latência a uma taxa que cresce perto da saturação.

A recomendação controlar esse risco é combinar a técnica sempre com uma abordagem tipo *token bucket* que limita as requisições adicionais ao que seria esperado. Vamos usar 5% como exemplo, calibrando pelo percentil em que o hedge dispara, e podemos observar que o mesmo token bucket usado para retries adaptativos funciona bem nesse caso. Falaremos disso mais tarde aqui no post.

### Token bucket como limite de amplificação

O token bucket é o limite explícito que impede o hedging de crescer junto com a degradação. Em regime normal, ele permite uma quantidade pequena de requisições especulativas. Durante um incidente de latência correlacionada, quando quase todas as requisições passam do limite de disparo, ele impede que o sistema dobre a carga. Um mecanismo de emergência sem teto não é proteção; é um multiplicador de trabalho.

![Imagem 10 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-10.png)

Durante um incidente de latência correlacionada (faixa vermelha), 100% das requisições passam do p95 e viram candidatas a hedge. Sem budget, a carga no backend dobra — exatamente no pior momento. Com um token bucket de 10%, o balde esvazia nos primeiros instantes e o hedge fica preso no teto que você autorizou: +10%, não +100%.

Porque o hedging é retry disparado mais cedo, antes mesmo de a primeira requisição falhar. Ele herda todos esses riscos de amplificação, com um agravante importante, ele dispara justamente nas caudas de latência, ou seja, exatamente durante o estresse. Sem idempotência, sem cancelamento e sem um freio de quota, o seu hedge não é uma proteção — é um incidente esperando para acontecer. É por isso que o Well-Architected é enfático em configurar um número máximo de tentativas ou tempo decorrido, especificamente para evitar backlogs que produzem falhas metaestáveis.

## Princípios para usar hedging pattern com segurança

Os três princípios a seguir definem a fronteira entre reduzir exposição à cauda e criar um mecanismo de amplificação de incidente.

### Idempotência é obrigatória, não opcional

Hedging duplica requisições, por definição. Se a operação tem efeito colateral — uma escrita, uma cobrança, uma mutação de estado — ela vai executar duas vezes. Cobrar o cliente duas vezes nesse caso não é um bug, é a consequência direta e previsível do hedging pattern para uma operação não-idempotente. O caso da AWS ilustra isso na prática de forma exemplar — eles aplicam hedging nas **leituras** de saldo e histórico (naturalmente idempotentes), mas processam as atualizações subsequentes de saldo sem hedging, para manter a integridade transacional.

Leituras são o caso simples. Para escritas, use um idempotency token, e a maioria das implementações erra num detalhe que o hedging garante que aparecerá, a corrida entre as duas cópias. A chave tem que nascer da intenção de negócio, gerada uma vez pelo chamador, um UUID por *pagamento*, não por tentativa. Se cada requisição do hedging gerar a própria chave, não existe deduplicação, existem duas operações distintas com nomes diferentes. E como o hedging faz as duas chegarem quase juntas, o backend precisa deduplicar com uma escrita **condicional** (um "insira só se esta chave ainda não existe", atômico), não com um "leia-depois-escreva", que é justamente a janela onde as duas cópias passam pelo teste ao mesmo tempo e ambas escrevem.

### Cancele o perdedor, sempre

Quando uma resposta vence, seja a primária, seja o hedge, todas as requisições ainda em voo devem ser canceladas. Mas tenha em mente o seguinte, **Cancelar no cliente não para o trabalho no servidor,** `task.cancel()`, `ctx.cancel()`, fechar o socket, tudo isso libera *os seus* recursos, a conexão, a thread, o slot no pool, o buffer. É um ganho real e é o motivo pelo qual a regra existe. O que não acontece é o servidor abandonar o trabalho, ele só abandona se for programado para isso, se o handler propaga o contexto de cancelamento e checa se o cliente ainda está lá. A maioria dos frameworks, por padrão, não faz isso. Ou seja, o cancelamento protege o cliente de vazar recursos, não o ***backend*** da carga duplicada. Quem protege o backend é o budget de carga extra, não o cancelamento.

Feita essa ressalva, cancele mesmo assim. Se você não cancela o perdedor, cada hedge deixa uma requisição órfã consumindo uma conexão, uma thread ou um slot no pool. Sob carga, esse acúmulo pode esgotar recursos do cliente e transformar uma tentativa de reduzir latência de cauda em falha por exaustão local.

Esse ponto é importante porque consome uma conexão esconde uma mecânica contra-intuitiva, o hedging pode criar o straggler que pretendia contornar. Quase todo cliente HTTP tem um teto de conexões por host. Quando o pool para aquele host lota, a próxima chamada não falha; ela **espera na fila do cliente**. Essa espera aparece na medição como latência da chamada, indistinguível de um servidor lento. O loop fica assim, hedgings enchem o pool → chamadas novas enfileiram no cliente → a latência medida sobe → mais chamadas passam do p95 → mais hedges. O sistema passa a gerar stragglers dentro do próprio processo. Por isso o cap absoluto de requisições em voo e o dimensionamento do pool não são detalhes, são parte do controle de resiliência.

Há uma decisão de design legítima aqui, no caso da AWS, optou-se por **manter as duas requisições ativas** e monitorá-las simultaneamente, deixando as duas competirem, em vez de cancelar a primeira ao disparar a segunda. Isso é aceitável quando o custo de deixar as duas terminarem é baixo (uma leitura pequena e barata). Mas a decisão precisa ser consciente, ou você cancela ativamente para liberar os seus recursos, ou você garante que deixar as duas rodarem é barato o suficiente. O que você não pode é ignorar a questão.

### Budget para carga extra

Esta é a regra que controla a amplificação. Você precisa de dois limites simultâneos. Primeiro, um **cap absoluto**, um número máximo de requisições em voo por operação (na prática, hedging além de 2 ou 3 raramente compensa, o gRPC por exemplo, que traz [hedging nativo na config](https://grpc.io/docs/guides/request-hedging/) de serviço, inclusive limita o número de tentativas a no máximo 5). Segundo, e mais importante, um **cap proporcional**: um token bucket que limita a taxa de hedging a uma fração do tráfego total, os 5% por exemplo como referência. No código adiante, uso 10% como exemplo. Com um budget de 10%, você adiciona no máximo 10% de requisições extras, nunca o dobro, mesmo que de repente 100% das requisições fiquem lentas.

Durante um incidente de latência correlacionada, todas as requisições podem ficar lentas ao mesmo tempo e todas podem virar candidatas a hedge. Sem budget, a aplicação dobra a carga. Com o token bucket, o hedging satura na taxa autorizada. Ele não vai a zero; ele fica limitado a, por exemplo, +10%. A garantia não é "carga extra zero durante o incidente"; é "carga extra limitada por uma constante escolhida antes do incidente". A diferença entre +10% e +100% de carga sobre um backend degradado pode ser a diferença entre degradar e colapsar.

Há um refinamento importante, usado por implementações de produção como o gRPC, em vez de calibrar o balde por um "RPS estimado", conte o tráfego **real**. Cada requisição bem-sucedida deposita uma fração de ficha; cada hedge saca uma ficha inteira. Assim o teto de "1 hedge a cada N requisições" se mantém sozinho, seja o serviço a 5 rps ou a 50.000 rps, sem estimar volume manualmente. O balde também encolhe naturalmente quando os sucessos param de chegar. O código abaixo usa essa versão. É o mesmo conceito de [adaptive retry](https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html) utilizado nas versões mais recentes do SDK da AWS

### Budget em chamadas com fan-out

Antes de sair desta seção, é preciso fechar um ponto deixado aberto na matemática da cauda. O problema foi construído em cima de **fan-out**, 100 chamadas paralelas, 63% de requisições lentas, mas as regras foram descritas como se houvesse uma chamada só. Não é a mesma coisa, e a diferença muda o dimensionamento.

É no fan-out que o hedging costuma ser mais valioso, porque o usuário não está esperando uma resposta; está esperando a última de muitas. Se metade do p99 vem do custo de esperar pelos 5% mais lentos, fazer hedging justamente com esses stragglers é uma forma direta de *tail-tolerance*. Mas o custo também compõe, se você fizer hedging com os 5% mais lentos de cada uma das 100 folhas, o número esperado de hedges por requisição de usuário não é "5% de uma chamada"; são 5% de 100, ou seja, **cinco hedges em voo** naquela requisição. O budget, descrito antes por operação, precisa ser raciocinado sobre o fan-out inteiro: 5% de carga extra medida na folha vira uma quantidade de trabalho paralelo bem maior no nível do usuário. Defina o budget e o cap absoluto pensando na largura do fan-out, não numa chamada isolada. Caso contrário, o "+5%" orçado pode virar uma rajada de trabalho não dimensionada.

## Um exemplo de Implementação

O objetivo do código abaixo é mostrar quais controles precisam existir em uma implementação segura. A implementação usa Python com `asyncio`, porque a natureza assíncrona deixa a lógica de dispare duas tentativas e use a primeira resposta válida explícita e legível.

Antes da versão segura, vale olhar para a implementação ingênua, porque ela parece razoável à primeira vista:

```python
# Hedge without control/limit: fires both ALWAYS, without a brake, deadline or cancellation.
async def get(urls):
    tasks = [asyncio.create_task(client.get(u)) for u in urls]  # fires ALL
    done, _ = await asyncio.wait(tasks, return_when=asyncio.FIRST_COMPLETED)
    return done.pop().result()
    # three problems: (1) doubles load on 100% of traffic, not on the slowest 5%;
    # (2) nobody cancels the loser -> leaks connections;
    # (3) a fast failure becomes the "winner" and ends the good request behind it.
```

Esse trecho pode reduzir a cauda em teste local e ainda assim amplificar carga em produção. A versão segura precisa incorporar os três princípios: idempotência, cancelamento e budget.

Primeiro, o limite. O componente central é o token bucket **proporcional ao tráfego real** discutido na seção de budget, no mesmo desenho do throttle de hedging do gRPC, sem RPS estimado, cada sucesso deposita fichas e cada hedge saca uma.

```python
import asyncio
import time

class HedgeBudget:
    """
    Token bucket based on the real TRAFFIC RATIO, the budget brake.

    Each successful logical request deposits 'ratio' tokens; each hedge fired
    withdraws 1. With budget_percent=10, at equilibrium hedges do not exceed
    1 in every 10 requests, whether the service runs at 5 rps or 50,000 rps.

    Second-order effect, exactly the point of the budget: during an incident,
    successes stop arriving, the bucket stops filling, and hedges saturate at
    the ceiling you authorized, for example +10%, instead of doubling load.
    Single-threaded under asyncio: no lock needed.
    """
    def __init__(self, budget_percent: float = 10.0, burst: int = 10):
        self.ratio = budget_percent / 100.0
        self.capacity = float(burst)     # maximum burst of "stored" hedges
        self.tokens = float(burst)

    def on_success(self) -> None:
        """Every successful logical request credits the bucket."""
        self.tokens = min(self.capacity, self.tokens + self.ratio)

    def try_take(self) -> bool:
        """Tries to spend 1 token to hedge. Empty bucket -> DO NOT fire."""
        if self.tokens >= 1.0:
            self.tokens -= 1.0
            return True
        return False   # graceful degradation, without amplification
```

Agora o hedge em si. A diferença em relação ao exemplo ingênuo está em três pontos: existe um **deadline** que o hedge herda, a **falha** dispara o hedge imediatamente em vez de encerrar a operação, e o `finally` **espera** os cancelamentos antes de devolver.

```python
class AllReplicasFailed(Exception):
    pass

async def hedged_get(client, urls: list[str], hedge_delay: float,
                     budget: HedgeBudget, deadline: float,
                     max_in_flight: int = 2):
    """
    Executes a request with hedging.

    PRECONDITION (idempotency): 'urls' must point to an
    idempotent operation (a GET, or a backend with dedup). Never pass
    a non-idempotent write through here.

    hedge_delay: the "firing delay". Must be >= observed p95 of the target, so
    that the hedge only fires on the slowest ~5%. Calibrate with real data.
    deadline: TOTAL time (seconds) for the entire operation, inherited from
    the caller. It is NOT optional: without it, if no one responds, the loop
    spins forever — the backlog factory that Well-Architected
    tells you to avoid. The hedge inherits the time that was LEFT, never a new clock.

    Note: whoever operates this hedged_get credits the budget by calling
    budget.on_success() on each successful logical request (it is the credit
    that sustains the proportional cap). Omitted here to focus on the loop.
    """
    loop = asyncio.get_running_loop()
    end = loop.time() + deadline
    tasks = [asyncio.create_task(client.get(urls[0]))]
    next_url = 1
    last_error: BaseException | None = None
    try:
        while True:
            remaining = end - loop.time()
            if remaining <= 0:
                raise TimeoutError("call deadline exceeded") from last_error
            # the hedge can NEVER exceed the caller's deadline
            done, pend = await asyncio.wait(
                tasks, timeout=min(hedge_delay, remaining),
                return_when=asyncio.FIRST_COMPLETED,
            )
            had_failure = False
            for t in done:
                error = t.exception()
                if error is None:
                    return t.result()          # legitimate winner: 1st GOOD response
                last_error = error              # loser with error: does NOT propagate yet
                had_failure = True
            tasks = list(pend)                  # 'pend' already excludes the completed ones

            # Hedges if the primary became a straggler (timeout) OR if it FAILED.
            # on failure, hedge NOW — a failure isn't waited on, it's worked around right away.
            can = next_url < min(max_in_flight, len(urls))
            if can and (had_failure or budget.try_take()):
                tasks.append(asyncio.create_task(client.get(urls[next_url])))
                next_url += 1
            elif not tasks:
                # nothing in flight and can't hedge → honest failure, no spinning
                raise AllReplicasFailed(str(last_error))
    finally:
        # Cancel and AWAIT the teardown, so the connection returns to the pool
        # before we return (canceling without waiting leaks the connection and pollutes the log).
        for t in tasks:
            if not t.done():
                t.cancel()
        if tasks:
            await asyncio.gather(*tasks, return_exceptions=True)
```

Quatro pontos separam essa versão do anti-padrão do começo da seção. Primeiro, o hedge dispara **um de cada vez** e só depois de reavaliar os caps, nunca em rajada. Segundo, uma **falha rápida da primária dispara o hedge imediatamente**, sem esperar o delay e sem encerrar a requisição. Terceiro, existe um **deadline global**, se ninguém responder, a operação falha num tempo limitado em vez de girar indefinidamente. Quarto, o `finally` não só cancela como **espera** o cancelamento propagar, devolvendo a conexão ao pool. A implementação foi exercitada nos casos de primária rápida, hedge vencedor, falha rápida, falha de todas as réplicas, timeout total e uma única URL.

Importante levar em conta, que dependendo do caso de uso e tipo de serviço envolvido, delay por operação pode ser algo a considerar, como por exemplo no caso do post da AWS envolvendo DynamoDB. Cada tipo de operação tem seu próprio perfil de latência: um `GetItem` tem latência tipicamente menor que uma `Query` que traz 100 registros ou um `Scan` que varre páginas inteiras, e isso pesa especialmente quando a mesma aplicação mistura operações. A fonte não prescreve um delay por operação, mas a conclusão prática é direta, se você usar um único `hedge_delay` global para operações com perfis diferentes, vai hedgear cedo demais em algumas e tarde demais em outras. Meça cada classe e calibre cada uma. Os testes da própria AWS, como a implementação da Global Payments, cobriram `GetItem`.

Também existe a questão de rollout. O `hedge_delay` inicial vem de um dashboard, e a distribuição real no caminho de código pode ser diferente. Se a estimativa errar por um fator de três para baixo, o primeiro deploy pode fazer hedging da maioria do tráfego. Por isso, antes de ativar hedging, rode em **modo shadow**, toda a lógica de decisão executa (timer, estimativa de percentil, cap, budget), mas no ponto em que dispararia o hedge ela apenas **registra uma métrica** ("teria feito o hedging agora") em vez de mandar a segunda requisição. Isso gera zero carga extra e revela a taxa real de disparo. Só depois faz sentido ativar de verdade, para uma fração pequena do tráfego, com um **kill switch** por configuração, não por deploy.

## Métricas para operar hedging em produção

Hedging falha em silêncio nas duas direções, e nenhuma das duas aparece num dashboard de latência comum. Calibrado para cima, ele custa dinheiro sem reduzir a cauda. Calibrado para baixo, ele amplifica. Antes de ligar isso em produção, instrumente quatro métricas.

**1. Taxa de hedge:** (`hedges disparados / chamadas`, por classe de operação). É a métrica de custo, e você tem um alvo declarado para ela: os 5% a 10% que orçou. Alarme no desvio, não no valor absoluto, se ela salta de 5% para 40% em minutos, ou o seu p95 estimado ficou obsoleto ou você está no começo de uma latência correlacionada.

**2. Taxa de vitória do hedge:** (`hedges que venceram / hedges disparados`). Essa é a métrica de *eficácia*, e é a que revela calibração errada. Se você dispara hedges e eles quase nunca vencem, o seu delay está curto demais, você está pagando trabalho extra por nada. Se vencem quase sempre, o delay está longo demais e você está deixando latência na mesa. Um número saudável é intermediário, o hedge deve ganhar com frequência suficiente para justificar o custo, sem ganhar sempre.

**3. Latência com e sem hedge, as duas, lado a lado:** Meça a latência efetiva (a que venceu) *e* a latência que a primária teria tido sozinha, sempre. A diferença é o ganho real do hedge, medido continuamente em vez de assumido. Essa é a métrica que responde se o hedge ainda vale a pena, em vez de depender da suposição criada no dia do rollout.

**4. Latência do caminho de hedge, separada da primária:** Esse é o alarme que o budget de taxa não cobre. Se o seu hedge vai para um caminho que pode ser uma ordem de grandeza mais lento (outra AZ, outra região, um cache mais frio), um hedge que **vence** mas demora dez vezes mais segura um slot do pool por dez vezes mais tempo, e enche o pool com hedges bem-sucedidos. A taxa de hedge fica dentro do orçamento e mesmo assim você satura por concorrência. Só uma métrica de latência do caminho de hedge, separada, pega isso antes do incidente.

Com essas quatro métricas, o hedging deixa de ser uma otimização invisível e passa a ser um mecanismo operável. Com apenas latência agregada, o sistema não mostra quando o padrão está amplificando carga exatamente no momento em que isso é mais perigoso.

## Quando não usar hedging

Aqui está a lista de situações em que hedging não é a resposta. Essa seção é tão importante quanto as anteriores, porque o padrão só aumenta resiliência quando aplicado no tipo certo de degradação. Cada item termina com o que usar **em vez** de hedging.

**Operações não-idempotentes sem deduplicação:** Se a operação tem efeito colateral e você não tem idempotency token nem deduplicação no backend, hedging vai executar duas vezes. Essa é uma restrição estrutural. Não faça hedging em escritas como experimento sem controle. Em vez disso, torne a operação idempotente primeiro (chave gerada pelo cliente + escrita condicional) e só então considere hedging.

**Backends de instância única:** O hedge ganha porque corre o backup contra uma réplica *diferente*, que provavelmente não está tendo o mesmo azar. Se as duas requisições vão para a **mesma** máquina (um backend de instância única, ou um cache sem réplicas), você não contornou nada, só adicionou carga à máquina que já estava lenta. Em vez disso, invista em réplicas/redundância antes de qualquer hedge, sem `ρ` baixo, hedging não tem o que comprar.

**Backends saturados de recurso:** Essa é a distinção mais sutil e mais importante. Hedging funciona quando a lentidão vem de fatores **transitórios e localizados**, uma pausa de GC, um pico de contenção, jitter de rede, uma partição quente momentânea. Nesses casos, a segunda réplica quase sempre pega uma máquina saudável. Mas se a lentidão vem de exaustão sustentada de recurso, CPU no talo, banco de dados degradado, saturação real, então adicionar um hedge só piora a saturação. Você está mandando mais trabalho para um sistema que já não dá conta do que tem. O hedge, nesse cenário, é o começo de uma falha metaestável. A pergunta que separa os dois casos, a lentidão é azar pontual ou é falta de capacidade? Em vez disso, load shedding, limite de concorrência adaptativo e backpressure, ferramentas que removem trabalho sob estresse, não que adicionam.

**Serviços atrás de um rate limit compartilhado:** Se o backend impõe uma cota global (uma API de terceiros, um limite de tokens por minuto), as requisições de hedge consomem essa cota. Você pode disparar throttling que não aconteceria de outra forma, e aí o "conserto" da cauda vira a causa dos erros. Em vez disso, cota do lado do cliente e priorização de requisições, para não gastar o orçamento compartilhado com trabalho especulativo.

**Serviços de baixíssimo tráfego:** Hedging adaptativo (que aprende o p95 em tempo real) precisa de volume para distinguir um straggler de variância normal. Num serviço que recebe menos de uma requisição por segundo, não há dados suficientes para calibrar, e você acaba hedgeando ruído. Em vez disso: um timeout fixo bem medido + retry com backoff e jitter ou adaptive retry resolve o caso esporádico sem exigir estatística que você não tem.

Quando as falhas são raras ou transitórias, os retries funcionam bem; quando são causadas por sobrecarga de recurso, eles podem piorar as coisas, e o mesmo vale, por extensão, para os hedges. Hedging é uma ferramenta específica para reduzir exposição a variação transitória. Não é solução para falta de capacidade.

Se você quiser um único artefato para levar ao seu próximo design review, é este — os testes em ordem, os que barram primeiro no topo:

![Imagem 11 do artigo](/blog/2026-08-03-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-11.png)

## Hedging e o princípio do trabalho constante

Fazendo um link com o [primeiro artigo](https://www.robissonoliveira.com.br/blog/2026-07-29-como-o-principio-trabalho-constante-aumenta-a-resiliencia-das-aplicacoes/). No princípio do trabalho constante, a recomendação era desconfiar de modos de emergência que só aparecem durante uma falha. Hedging também cria um segundo modo: em regime normal, uma tentativa; na cauda, uma tentativa adicional.

A diferença está em o que muda no segundo modo. O fallback perigoso é aquele que, sob falha, passa a fazer um trabalho diferente, troca caminho de execução, aciona uma lógica pouco exercitada ou muda o perfil de carga para algo que não foi dimensionado. O hedge não faz trabalho diferente; ele faz mais do mesmo trabalho, contra outro caminho ou réplica compatível. A natureza da operação não muda, só o destino e a quantidade.

Essa defesa só vale com budget. Sem token bucket, o hedge cai na mesma patologia discutida no primeiro artigo: "1× normal, 2× sob estresse" pode virar "1× normal, 2× para todo o tráfego ao mesmo tempo durante o incidente". Nesse caso, o segundo modo amplifica a falha em vez de contê-la. O que torna hedging aceitável é limitar a amplitude do segundo modo a uma constante pequena escolhida antes da degradação.

## Alternativas ao hedging

Hedging é uma ferramenta dentro de uma caixa maior. O [post do Marc Brooker](https://brooker.co.za/blog/2023/01/06/erasure.html) é, na verdade, uma alternativa e um argumento a favor de **erasure coding** como alternativa mais geral: em vez de mandar a mesma requisição duas vezes, você quebra o dado em `M` pedaços recuperáveis a partir de quaisquer `k`, dispara os `M` e responde assim que os `k` primeiros chegam. Brooker explicita o trade-off: isso amplifica **mais** requisições que o hedge (no exemplo dele, 5× em vez de 2×), mas com custo de banda e armazenamento menor, e pode melhorar até a mediana, não só a cauda, porque não é modal. Para caches e sistemas de armazenamento sensíveis a latência, é uma alternativa relevante.

## Conclusão

Hedging pattern aumenta resiliência quando reduz a exposição da aplicação à latência de cauda. Ele não aumenta capacidade real, não substitui redundância e não corrige saturação. O que ele faz é usar redundância já existente de forma especulativa e limitada para impedir que uma lentidão transitória e localizada defina a resposta final.

Essa distinção é importante. Se a lentidão vem de uma pausa de GC, jitter de rede, fila local ou uma réplica momentaneamente pior, hedging pode mascarar a variação e manter o fluxo previsível. Se a lentidão vem de saturação sustentada, partição quente ou falta de capacidade, hedging adiciona trabalho ao ponto errado e pode acelerar uma falha metaestável.

Por isso o padrão só é viavel ao mei ver com três controles: idempotência, cancelamento e budget. Idempotência impede efeitos colaterais duplicados. Cancelamento evita vazamento de recursos no cliente. Budget limita a carga extra quando a degradação deixa de ser localizada e passa a ser correlacionada. Sem esses controles, hedging deixa de ser resiliência e vira amplificação.

Hedging aumenta resiliência aceitando uma pequena quantidade de trabalho redundante para reduzir exposição à cauda. São técnicas diferentes, mas compartilham o mesmo princípio operacional: durante uma degradação, o sistema não pode amplificar trabalho sem limite.

Antes de aplicar hedging em um serviço crítico, responda objetivamente:

> 1. A operação é idempotente ou tem deduplicação atômica no backend?
> 2. O hedge realmente pode seguir por um caminho independente, ou cairá no mesmo gargalo?
> 3. A lentidão que você quer mascarar é transitória e localizada, ou é falta de capacidade?
> 4. Se 100% das requisições passarem do limite de disparo, existe um budget que impede dobrar a carga?

A resposta à quarta pergunta define se o hedging aumenta a resiliência do sistema ou apenas cria outro modo de falha.

**Referências:**

* [cseweb.ucsd.edu/classes/sp18/cse124-a/post/schedule/p74-dean.pdf](https://cseweb.ucsd.edu/classes/sp18/cse124-a/post/schedule/p74-dean.pdf)
* [brooker.co.za/blog/2021/04/19/latency.html](https://brooker.co.za/blog/2021/04/19/latency.html)
* [brooker.co.za/blog/2023/01/06/erasure.html](https://brooker.co.za/blog/2023/01/06/erasure.html)
* [https://aws.amazon.com/blogs/database/how-global-payments-inc-improved-their-tail-latency-using-request-hedging-with-amazon-dynamodb/](https://aws.amazon.com/blogs/database/how-global-payments-inc-improved-their-tail-latency-using-request-hedging-with-amazon-dynamodb/)
* [https://docs.aws.amazon.com/wellarchitected/2024-06-27/framework/rel\_mitigate\_interaction\_failure\_limit\_retries.html](https://docs.aws.amazon.com/wellarchitected/2024-06-27/framework/rel_mitigate_interaction_failure_limit_retries.html)
* [sigops.org/s/conferences/hotos/2021/papers/hotos21-s11-bronson.pdf](https://sigops.org/s/conferences/hotos/2021/papers/hotos21-s11-bronson.pdf)
* [grpc.io/docs/guides/request-hedging/](https://grpc.io/docs/guides/request-hedging/)
* [https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html](https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html)
