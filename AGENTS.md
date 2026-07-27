# Codex Agent Guide

Este repositorio e um site pessoal estatico em Astro. O objetivo de qualquer agente aqui e preservar a simplicidade do site: conteudo versionado no Git, HTML gerado no build, sem backend e sem dependencias dinamicas em producao.

## Regras do Projeto

- Trabalhe preferencialmente em `src/`, `public/` e `assets/`.
- Nao edite `dist/` diretamente. Gere com `npm run build` ou `npm run validate`.
- Como o GitHub Pages atual publica a raiz do repositorio, depois de um build aprovado copie `dist/.` para a raiz antes de commitar uma publicacao.
- Nao inclua `.DS_Store`, arquivos temporarios, caches locais ou HTMLs baixados do Medium.
- Mantenha o site bilingue quando a mudanca tocar blog, navegacao ou metadados: `pt-BR` e `en`.
- Para artigos traduzidos, registre o par de slugs em `src/lib/blog.ts`.
- Para artigos com imagem principal, atualize `postImage()` em `src/lib/blog.ts`.
- Antes de finalizar, rode `npm run validate` e mantenha `0 errors`.

## Fluxo de Trabalho

1. Entenda a mudanca lendo os arquivos envolvidos.
2. Faça edicoes pequenas e coerentes com a estrutura Astro atual.
3. Para novos posts, crie Markdown em `src/content/blog` com frontmatter completo.
4. Coloque imagens em `public/blog/<slug-curto>/` e referencie como `/blog/<slug-curto>/image-01.png`.
5. Rode `npm run validate`.
6. Se a mudanca for para publicacao no GitHub Pages, copie `dist/.` para a raiz.
7. Revise `git status --short` e ignore sujeira local nao relacionada.

## Padrao de Qualidade

- O site deve continuar 100% estatico.
- O layout precisa funcionar em desktop, tablet e mobile.
- Conteudo novo precisa ter titulo, descricao, data, tags, idioma e imagem coerente.
- SEO basico deve ser preservado: titulo unico, descricao clara, sitemap/RSS gerados pelo build.
- SEO tecnico deve ser tratado como parte do Definition of Done quando tocar paginas publicas: canonical, hreflang, Open Graph, Twitter card, JSON-LD, imagens publicas e rotas retornando `200`.
- Evite refatoracoes grandes quando a tarefa for conteudo ou publicacao.
