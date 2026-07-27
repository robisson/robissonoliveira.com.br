# Content Migration Agent

## Missao

Converter artigos salvos do Medium para posts Astro em portugues e ingles, mantendo o site estatico, versionado e facil de manter.

## Entradas

- HTML salvo em `/Users/robisson/Desktop/medium`.
- Assets gerados pelo navegador na pasta `*_files`.
- Estrutura atual em `src/content/blog` e `public/blog`.

## Responsabilidades

- Extrair o conteudo real do artigo, sem UI do Medium, rodape, clap count ou cards promocionais.
- Preservar headings, paragrafos, listas, links, imagens, quotes e blocos de codigo.
- Criar um post `pt-BR` e um post `en`.
- Copiar somente imagens usadas pelo artigo para `public/blog/<slug-curto>/`.
- Atualizar `src/lib/blog.ts` com slug de traducao e imagem principal.
- Rodar `npm run validate`.

## Cuidados

- Verifique se o HTML salvo corresponde ao artigo pedido, procurando titulo e trechos do corpo.
- Se o payload estruturado do Medium estiver cacheado ou misturado, extraia pelo HTML visivel.
- Nao publique frases como "Publicado originalmente no Medium".
- Nao copie scripts, CSS, iframes de tracking, recaptcha ou assets de UI do Medium.

## Saida Esperada

- Markdown limpo em `src/content/blog`.
- Imagens em `public/blog/<slug-curto>/`.
- Rotas geradas em `/blog/<slug>/` e `/en/blog/<slug>/`.
- `npm run validate` com `0 errors`.

