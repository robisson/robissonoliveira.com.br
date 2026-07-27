# robissonoliveira.com.br

Site pessoal estatico de Robisson Oliveira, atualmente em migracao para uma arquitetura moderna com Astro.

A ideia desta versao e manter o site 100% estatico, com HTML gerado no build, conteudo versionado no Git e uma experiencia local simples para escrever, testar e evoluir o design antes de publicar.

## Requisitos

- Node.js em versao compativel com Astro 7.
- npm.

## Desenvolvimento local

Instale as dependencias:

```bash
npm install
```

Suba o servidor de desenvolvimento:

```bash
npm run dev
```

O Astro sobe o site localmente em `http://localhost:4321`. O script usa `--host 0.0.0.0`, entao tambem pode ser acessado por outros dispositivos da rede usando o IP da maquina, util para testar mobile e tablet.

## Build estatico

Gere a versao final estatica:

```bash
npm run build
```

O resultado fica em `dist/`. Esse diretorio e gerado pelo Astro e nao deve ser editado manualmente.

## Preview do build

Depois do build, rode:

```bash
npm run preview
```

Use esse comando quando quiser testar exatamente o HTML gerado em `dist/`.

Se quiser testar como um servidor estatico simples, sem o dev server do Astro, rode:

```bash
npm run preview:static
```

Esse comando gera o build e serve `dist/` em `http://localhost:8080`. Use essa porta para validar a versao estatica final, incluindo CSS, imagens de `/blog/...`, RSS e sitemap. A raiz do servidor precisa ser `dist/`; servir uma subpasta ou outro diretorio pode quebrar caminhos absolutos como `/blog/reactjs-7-dias-hello-world/image-01.png`.

## Validacao

Antes de publicar ou abrir PR, rode:

```bash
npm run validate
```

Esse comando executa:

- `npm run check`: valida tipos, content collections e arquivos Astro.
- `npm run build`: gera o site estatico completo.

Hoje podem aparecer hints em arquivos legados como `gulpfile.js` e `server.js`, mas o importante e manter `0 errors`.

## Estrutura de diretorios

```text
.
|-- astro.config.mjs
|-- package.json
|-- package-lock.json
|-- tsconfig.json
|-- README.md
|-- AGENTS.md
|-- .codex/
|   |-- agents/
|   `-- skills/
|-- src/
|   |-- content.config.ts
|   |-- content/
|   |   `-- blog/
|   |-- layouts/
|   |   `-- BaseLayout.astro
|   |-- lib/
|   |   |-- blog.ts
|   |   `-- books.ts
|   |-- pages/
|   |   |-- index.astro
|   |   |-- rss.xml.ts
|   |   |-- blog/
|   |   |-- en/
|   |   |-- livros/
|   |   `-- sobre-mim/
|   `-- styles/
|       `-- global.css
|-- public/
|   `-- blog/
|-- assets/
|   |-- images/
|   |-- font/
|   `-- scss/
|-- blog/
|   `-- wp-content/
|-- livros/
|-- *.htm
`-- dist/
```

## Como a arquitetura atual funciona

### Astro

O Astro e responsavel por gerar as paginas novas como HTML estatico. As rotas ficam dentro de `src/pages`.

- `src/pages/index.astro`: home em portugues.
- `src/pages/en/index.astro`: home em ingles.
- `src/pages/sobre-mim/index.astro`: pagina sobre em portugues.
- `src/pages/en/about/index.astro`: pagina sobre em ingles.
- `src/pages/blog/index.astro`: listagem do blog em portugues.
- `src/pages/blog/[slug].astro`: artigo individual em portugues.
- `src/pages/en/blog/index.astro`: listagem do blog em ingles.
- `src/pages/en/blog/[slug].astro`: artigo individual em ingles.
- `src/pages/livros/index.astro`: pagina principal de livros, mostrando o ano mais recente.
- `src/pages/livros/[year].astro`: paginas estaticas por ano.
- `src/pages/rss.xml.ts` e `src/pages/en/rss.xml.ts`: feeds RSS estaticos.

### Layout

`src/layouts/BaseLayout.astro` concentra a estrutura comum do site:

- HTML base.
- Navegacao.
- Alternancia PT/EN.
- Rodape.
- Inclusao do CSS global.
- Script do menu mobile/sidebar.

### Estilos

`src/styles/global.css` concentra o CSS da nova versao Astro, incluindo:

- Layout legado preservado para home e sobre.
- Blog responsivo.
- Pagina de livros.
- Menu mobile em sidebar.
- Breakpoints para desktop, tablet e mobile.

### Blog

Os posts ficam em `src/content/blog` como Markdown.

Cada arquivo precisa ter frontmatter no topo:

```markdown
---
title: "Titulo do post"
description: "Descricao curta"
pubDate: 2024-08-12
tags: ["AWS", "Resilience"]
series: "Cloud Resilience"
language: "pt-BR"
---
```

Para posts em ingles, use:

```markdown
language: "en"
```

As imagens usadas nos artigos ficam em `public/blog/...`. Tudo dentro de `public/` e servido na raiz do site. Exemplo:

```markdown
![Imagem do artigo](/blog/availability-zone-independence-azi/image-01.png)
```

### Livros

A pagina de livros foi mantida estatica, mas reorganizada para evitar uma pagina unica pesada.

- `src/lib/books.ts`: indice dos anos, contagens e caminhos.
- `src/pages/livros/index.astro`: entrada principal, hoje apontando para o ano mais recente.
- `src/pages/livros/[year].astro`: gera uma pagina estatica por ano usando os HTMLs existentes como fonte.
- `livros.htm` e `livros/*.htm`: HTMLs legados usados como base para os anos.

Como o site e estatico, as contagens de livros por ano ficam declaradas no codigo e devem ser atualizadas manualmente quando novos livros forem adicionados.

### Assets

- `assets/images/`: imagens usadas por paginas da home, sobre e projetos.
- `assets/font/`: fontes legadas do site.
- `assets/scss/`: SCSS legado ainda mantido no repositorio.
- `public/blog/`: imagens dos posts do blog Astro.
- `blog/wp-content/uploads/`: assets legados ainda usados por algumas paginas, principalmente livros e certificacoes.

### Arquivos legados

Ainda existem arquivos estaticos antigos na raiz e em algumas pastas:

- `index.htm`
- `livros.htm`
- `livros/*.htm`
- `server.js`
- `gulpfile.js`

Eles foram preservados durante a migracao para reduzir risco e manter historico do site original. A nova arquitetura deve evoluir preferencialmente dentro de `src/`, `public/` e `assets/`.

## Fluxo para criar um novo post

1. Crie um arquivo Markdown em `src/content/blog`.
2. Defina o frontmatter com `title`, `description`, `pubDate`, `tags`, `series` e `language`.
3. Coloque imagens do post em `public/blog/nome-do-post/`.
4. Referencie imagens com caminho absoluto a partir da raiz, por exemplo `/blog/nome-do-post/image-01.png`.
5. Se o post tiver traducao, registre o par de slugs em `src/lib/blog.ts`.
6. Se o post tiver imagem principal, atualize `postImage()` em `src/lib/blog.ts`.
7. Rode `npm run validate`.

## Codex, agentes e skills

O projeto agora possui uma camada de instrucoes versionada para uso com Codex:

- `AGENTS.md`: regras principais para qualquer agente trabalhando neste repositorio.
- `.codex/agents/`: papeis recomendados para migracao de conteudo, publicacao estatica e revisao responsiva/SEO.
- `.codex/skills/`: checklists operacionais para converter posts do Medium, publicar Astro estatico e revisar responsividade/SEO.

Esses arquivos documentam o fluxo que vem sendo usado aqui: manter o site 100% estatico, converter conteudo para Markdown Astro, gerar HTML no build, validar localmente com `npm run validate` e copiar `dist/.` para a raiz quando a mudanca for publicada pelo GitHub Pages atual.

## Publicacao

Este branch moderno e para validacao local antes de publicar. Quando estiver aprovado, gere o build com `npm run build` e publique o conteudo estatico gerado conforme o fluxo do GitHub Pages/repo.
