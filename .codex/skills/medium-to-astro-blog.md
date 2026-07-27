# Skill: Medium to Astro Blog

Use esta skill quando o usuario pedir para converter artigos salvos do Medium para o blog Astro.

## Passos

1. Localize somente os HTMLs solicitados em `/Users/robisson/Desktop/medium`.
2. Confirme titulo, data e trechos do corpo no HTML.
3. Extraia conteudo do artigo, removendo UI do Medium.
4. Copie imagens usadas para `public/blog/<slug-curto>/`.
5. Crie Markdown em portugues com `language: "pt-BR"`.
6. Crie Markdown equivalente em ingles com `language: "en"`.
7. Atualize `translationSlugs` em `src/lib/blog.ts`.
8. Atualize `postImage()` em `src/lib/blog.ts`.
9. Rode `npm run validate`.

## Frontmatter

```markdown
---
title: "Titulo"
description: "Descricao curta"
pubDate: 2024-08-12
tags: ["Tag"]
series: "Serie"
language: "pt-BR"
---
```

## Regras

- Nao manter referencias dizendo que o post foi publicado originalmente no Medium.
- Nao importar scripts/CSS do Medium.
- Nao inventar imagens que nao estejam no artigo salvo.
- Preservar blocos de codigo e links.

