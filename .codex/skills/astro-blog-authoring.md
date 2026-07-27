# Skill: Astro Blog Authoring

Use esta skill quando o usuario quiser escrever um artigo novo, transformar uma ideia em post ou revisar um artigo autoral do blog.

## Passos

1. Entenda o objetivo do artigo, publico e tese principal.
2. Defina uma estrutura com introducao, contexto, desenvolvimento, exemplos e fechamento.
3. Escreva em Markdown dentro de `src/content/blog`.
4. Use frontmatter completo:

```markdown
---
title: "Titulo do artigo"
description: "Descricao curta para SEO e cards"
pubDate: 2026-07-26
tags: ["Cloud", "Architecture"]
series: "Architecture"
language: "pt-BR"
---
```

5. Crie a versao equivalente em ingles quando o blog precisar continuar bilingue.
6. Atualize `translationSlugs` em `src/lib/blog.ts`.
7. Atualize `postImage()` se houver imagem principal.
8. Rode `npm run validate`.

## Qualidade

- O titulo deve ser especifico e pesquisavel.
- A descricao deve explicar o valor do artigo em uma frase.
- O texto deve ter uma linha argumentativa clara.
- Use headings para escaneabilidade.
- Use exemplos praticos quando o assunto for arquitetura, cloud ou desenvolvimento.
- Evite frases genericas e conclusoes vazias.
