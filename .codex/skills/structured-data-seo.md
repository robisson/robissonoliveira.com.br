# Skill: Structured Data SEO

Use esta skill quando a mudanca envolver JSON-LD, Open Graph, Twitter cards, dados de artigo, pessoa, website ou breadcrumbs.

## Padrao atual

- `BaseLayout.astro` gera metadados comuns.
- Posts usam `BlogPosting` em `src/pages/blog/[slug].astro` e `src/pages/en/blog/[slug].astro`.
- Paginas comuns usam `Person` e `WebSite` como dados estruturados padrao.

## Checklist para posts

- `headline` igual ao titulo do post.
- `description` igual ao frontmatter.
- `image` absoluto e acessivel.
- `datePublished` vem de `pubDate`.
- `dateModified` vem de `updatedDate` quando existir, senao `pubDate`.
- `inLanguage` correto: `pt-BR` ou `en`.
- `author` e `publisher` identificam Robisson Oliveira.
- `mainEntityOfPage` aponta para canonical.
- `keywords` vem de `tags`.

## Validação

- Rodar `npm run build`.
- Inspecionar HTML gerado em `dist`.
- Testar imagens com `curl` quando forem usadas em `og:image` ou JSON-LD.
