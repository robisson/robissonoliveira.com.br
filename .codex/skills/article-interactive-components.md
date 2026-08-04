# Skill: Article Interactive Components

Use esta skill quando o usuario pedir simuladores, graficos interativos, botoes de play, sliders, animacoes ou componentes ricos dentro de artigos tecnicos do blog.

## Padrao principal

Mantenha o site estatico. Como os posts sao editados pelo Keystatic, nao coloque HTML interativo bruto no Markdown (`section`, `input`, `canvas`, `button`, etc.). O editor tenta validar essas tags como componentes MDX e quebra com "Missing component definition".

Use marcador textual no Markdown, CSS global escopado e JavaScript puro:

1. Insira um marcador textual no artigo em `src/content/blog`, perto da explicacao que o componente complementa. Exemplo: `[[tail-latency-simulator]]`.
2. Coloque estilos em `src/styles/global.css`, com classes escopadas ao componente.
3. Coloque a logica em `public/assets/js/<nome-do-componente>.js`, sem dependencias externas. O script deve substituir o marcador pelo markup real do componente.
4. Carregue o script condicionalmente em `src/pages/blog/[slug].astro`, apenas para o slug do artigo que usa o componente.
5. Rode `node --check public/assets/js/<arquivo>.js` e `npm run validate`.

Use como referencia o simulador de tail latency:

- Marcador no artigo: `[[tail-latency-simulator]]` em `src/content/blog/2026-07-29-resiliencia-alem-do-obvio-hedging-pattern.md`
- CSS: bloco `.tail-simulator` em `src/styles/global.css`
- JS: `public/assets/js/tail-latency-simulator.js`, que cria o markup e substitui o marcador
- Loader condicional: `src/pages/blog/[slug].astro`

## Design visual

Siga a mesma proporcao e linguagem visual do simulador atual:

- Fundo branco.
- Borda `1px solid var(--color-line)`.
- Borda superior de `5px` usando `var(--color-blue)`.
- Sem gradientes, decoracao excessiva, cards dentro de cards ou visual de landing page.
- Controles compactos, utilitarios e faceis de escanear.
- Cores apenas do design system existente: `--color-ink`, `--color-ink-soft`, `--color-muted`, `--color-line`, `--color-wash`, `--color-red`, `--color-green`, `--color-blue`, `--color-green-light`.
- Texto serio e tecnico. Nao use piadas, titulos engraçados, metaforas decorativas ou tom de charada.
- Em desktop, prefira duas colunas para visualizacoes.
- Em tablet/mobile, use uma coluna.
- Para `canvas`, use tamanho intrinseco em torno de `760x300` e CSS responsivo com `width: 100%; height: auto;`.
- No mobile, deixe o componente ocupar a largura do artigo como imagens: `margin: 28px -16px`, sem bordas laterais.

## Imagens e diagramas de apoio

Quando um artigo bilingue tiver imagens, graficos ou diagramas tecnicos usados nas duas versoes do texto, mantenha o conteudo interno da imagem em ingles. O mesmo PNG deve ser reutilizado em `pt-BR` e `en`; traduza apenas o texto do artigo, `alt text`, legenda externa ou paragrafo explicativo quando necessario.

Essa regra evita divergencia visual entre idiomas e preserva termos tecnicos de programacao, observabilidade, arquitetura e resiliencia no formato em que o leitor normalmente os encontra.

## Estrutura de markup

No Markdown, use apenas um marcador textual:

```markdown
[[nome-do-simulador]]
```

No JavaScript, crie uma `<section>` autocontida, classes BEM e `data-*` para a ligacao com a logica:

```html
<section class="<prefix>-simulator" data-<prefix>-simulator aria-labelledby="<prefix>-title">
  <header class="<prefix>-simulator__header">
    <div>
      <p class="<prefix>-simulator__eyebrow">Simulador</p>
      <h3 id="<prefix>-title">Titulo claro do componente</h3>
    </div>
    <button class="<prefix>-simulator__button" type="button" data-action="play" aria-pressed="false">Play</button>
  </header>

  <p class="<prefix>-simulator__intro">Explique o que o leitor deve observar.</p>

  <div class="<prefix>-simulator__controls" aria-label="Parametros da simulacao">
    <label>
      <span>Parametro</span>
      <input type="range" min="1" max="100" value="50" data-control="parametro" />
      <output data-output="parametro">50</output>
    </label>
  </div>

  <div class="<prefix>-simulator__metrics" aria-live="polite">
    <div><strong data-metric="resultado">-</strong><span>Resultado</span></div>
  </div>

  <div class="<prefix>-simulator__views">
    <figure class="<prefix>-simulator__panel">
      <figcaption>Grafico principal</figcaption>
      <canvas width="760" height="300" data-canvas="main"></canvas>
    </figure>
  </div>

  <p class="<prefix>-simulator__note">Declare as simplificacoes da simulacao.</p>
</section>
```

Use um prefixo unico por componente, por exemplo `.tail-simulator`, `.queue-simulator` ou `.retry-simulator`. O marcador no Markdown deve ser substituido pelo script no carregamento da pagina.

## Regras de JavaScript

- Envolva tudo em uma IIFE.
- Comece criando ou localizando a raiz. Se ela nao existir, procure o paragrafo marcador em `.article__body p`, crie a `<section>` do componente e substitua o marcador.
- Depois aplique a guarda: `if (!root) return;`.
- Consulte controles, outputs, metricas, canvas e botoes por `data-*`.
- Mantenha estado local no arquivo do componente.
- Formate numeros em portugues com `toLocaleString("pt-BR")`.
- Para canvas, trate `window.devicePixelRatio` e `resize` para manter nitidez.
- Use `aria-pressed` em play/pause e `aria-live="polite"` em metricas que mudam.
- Nao use variaveis globais, chamadas de rede, bibliotecas de grafico ou dependencias novas sem pedido explicito.

## Qualidade editorial

O componente deve ensinar um conceito especifico, nao virar uma aplicacao separada. O estado inicial deve demonstrar o ponto principal do artigo antes de qualquer interacao.

Nao substitua a explicacao textual pelo componente. Adicione uma frase antes ou depois dizendo o que o leitor deve observar e como isso reforca o argumento do artigo.
