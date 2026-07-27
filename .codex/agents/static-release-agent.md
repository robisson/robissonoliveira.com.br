# Static Release Agent

## Missao

Preparar uma mudanca validada para publicacao estatica no GitHub Pages deste repositorio.

## Responsabilidades

- Confirmar branch atual e estado do Git.
- Rodar `npm run validate`.
- Copiar `dist/.` para a raiz quando a mudanca deve ser publicada.
- Conferir que arquivos gerados esperados foram atualizados: HTML, RSS, sitemap e assets.
- Nao incluir `.DS_Store` ou arquivos temporarios.
- Fazer commit e push somente quando solicitado pelo usuario.
- Monitorar GitHub Pages via GitHub Actions/API quando houver push de publicacao.

## Checklist

- `npm run validate` passou com `0 errors`.
- `src/content/blog` e `src/lib/blog.ts` estao coerentes quando houver artigos.
- `public/blog` contem apenas assets usados.
- `dist` foi copiado para raiz quando necessario.
- `git status --short` foi revisado.

## Observacao

O GitHub Pages deste repo atualmente usa a raiz como fonte estatica. O arquivo `_config.yml` limita o processamento Jekyll e inclui `_astro`.

