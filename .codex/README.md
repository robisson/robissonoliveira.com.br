# Codex Project Harness

Esta pasta documenta os agentes e skills recomendados para trabalhar neste site com Codex. Ela e intencionalmente simples e versionada no repositorio para que qualquer sessao futura consiga repetir o mesmo modo de trabalho.

## Agentes

- `agents/content-migration-agent.md`: converte artigos salvos do Medium para Markdown Astro em portugues e ingles.
- `agents/editorial-blog-agent.md`: cria e revisa artigos autorais em portugues e ingles com padrao editorial, SEO e estrutura Astro.
- `agents/site-maintenance-agent.md`: cuida de arquitetura Astro, assets, rotas criticas, responsividade e manutencao geral.
- `agents/static-release-agent.md`: valida, gera build, copia `dist` para a raiz e prepara publicacao no GitHub Pages.
- `agents/responsive-seo-review-agent.md`: revisa responsividade, metadados, sitemap, RSS e qualidade visual.

## Skills

- `skills/medium-to-astro-blog.md`: passo a passo para migrar artigos do Medium.
- `skills/astro-blog-authoring.md`: passo a passo para criar ou revisar artigos novos no blog Astro.
- `skills/bilingual-content-localization.md`: padrao para manter conteudo equivalente em portugues e ingles.
- `skills/static-asset-management.md`: regras para imagens e assets funcionarem em build, localhost e producao.
- `skills/books-catalog-maintenance.md`: manutencao da pagina Livros que Recomendo e livro atual da home.
- `skills/technical-seo-audit.md`: auditoria tecnica de indexacao, canonical, hreflang, sitemap, RSS e metadados.
- `skills/on-page-seo-writing.md`: revisao editorial de titulos, descricoes, headings, links e intencao de busca.
- `skills/structured-data-seo.md`: manutencao de JSON-LD, Open Graph, Twitter cards e dados de artigos.
- `skills/seo-image-performance.md`: SEO e performance de imagens, incluindo `alt`, `og:image` e assets publicos.
- `skills/seo-release-validation.md`: checklist final de SEO antes de publicar.
- `skills/astro-static-publish.md`: passo a passo para publicar o build estatico neste repo.
- `skills/responsive-seo-check.md`: checklist de responsividade e SEO.

Use `AGENTS.md` na raiz como fonte principal de regras do projeto.
