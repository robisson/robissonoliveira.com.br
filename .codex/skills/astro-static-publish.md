# Skill: Astro Static Publish

Use esta skill quando o usuario pedir build, commit, push ou publicacao do site.

## Passos

1. Rode `git status --short --branch`.
2. Rode `npm run validate`.
3. Se for publicar no GitHub Pages, rode `cp -R dist/. .`.
4. Revise `git status --short`.
5. Stage somente arquivos relevantes.
6. Commit com mensagem objetiva.
7. Push para a branch solicitada.
8. Monitore o run `pages build and deployment` ate sucesso.

## Regras

- Nunca incluir `.DS_Store`.
- Nunca fazer merge em `master` sem pedido explicito.
- Nao editar `dist/` manualmente.
- Se o build remoto falhar, olhar jobs/check annotations e corrigir a causa.

