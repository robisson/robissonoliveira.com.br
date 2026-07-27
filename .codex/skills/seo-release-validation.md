# Skill: SEO Release Validation

Use esta skill antes de commit, push ou publicacao de mudancas que toquem SEO, blog, home, sobre ou livros.

## Rotas para conferir

- `/`
- `/en/`
- `/blog/`
- `/en/blog/`
- Ultimo post em portugues.
- Ultimo post em ingles.
- `/sobre-mim/`
- `/en/about/`
- `/livros/`
- `/rss.xml`
- `/en/rss.xml`
- `/sitemap-index.xml`

## Passos

1. Rode `npm run validate`.
2. Sirva `dist/` com `npm run preview:static` ou servidor estatico equivalente.
3. Use `curl` para confirmar `200` nas rotas criticas.
4. Extraia do HTML gerado: `title`, `description`, `canonical`, `hreflang`, `og:image` e JSON-LD.
5. Teste cada asset importante com `curl`.
6. Confirme que `dist/.` foi copiado para a raiz quando a publicacao depende da raiz do GitHub Pages.

## Falhas comuns

- Asset em `assets/` referenciado como URL publica.
- Servidor local apontando para diretorio errado.
- `dist` recriado enquanto servidor estatico antigo continua aberto.
- Slug de traducao ausente em `translationSlugs`.
- `og:image` apontando para arquivo inexistente.
