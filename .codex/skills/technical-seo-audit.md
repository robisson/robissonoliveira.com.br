# Skill: Technical SEO Audit

Use esta skill quando o usuario pedir revisao geral de SEO tecnico, indexacao, sitemap, RSS, canonicals, hreflang ou validacao pre-publicacao.

## Checklist

- Cada pagina tem exatamente um `title` unico e descritivo.
- Cada pagina indexavel tem `meta description` clara.
- `canonical` aponta para a URL publica correta.
- Paginas traduzidas tem `hreflang` para `pt-BR`, `en` e `x-default`.
- Posts tem `og:type=article`; paginas comuns usam `website` ou `profile`.
- `og:title`, `og:description`, `og:url`, `og:image` e Twitter card estao presentes.
- `robots.txt`, `sitemap-index.xml`, `sitemap-0.xml`, `rss.xml` e `en/rss.xml` existem depois do build.
- Rotas importantes retornam `200` em servidor local.
- Assets referenciados por metadados e conteudo retornam `200` e tipo correto.

## Comandos uteis

```bash
npm run validate
curl -s -o /dev/null -w '%{http_code} %{content_type}\n' http://localhost:8080/
curl -s -o /dev/null -w '%{http_code} %{content_type}\n' http://localhost:8080/sitemap-index.xml
curl -s -o /dev/null -w '%{http_code} %{content_type}\n' http://localhost:8080/rss.xml
```

## Regras

- Nao bloquear publicacao por hints antigos se `npm run validate` tiver `0 errors`.
- Nao remover paginas estaticas legadas sem avaliar impacto de URLs antigas.
- Preferir correcoes em `src/` e `public/`; regenerar `dist` com build.
