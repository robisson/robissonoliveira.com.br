# Skill: Bilingual Content Localization

Use esta skill quando uma pagina ou artigo precisar existir em portugues e ingles.

## Passos

1. Identifique a versao fonte e o idioma alvo.
2. Preserve significado, estrutura, exemplos, links, imagens e blocos de codigo.
3. Adapte expressao idiomatica sem alterar a tese tecnica.
4. Garanta que frontmatter use `language: "pt-BR"` ou `language: "en"`.
5. Para posts, registre os pares em `translationSlugs` em `src/lib/blog.ts`.
6. Confira links de idioma no HTML gerado.
7. Rode `npm run validate`.

## Regras

- Nao misturar idiomas no mesmo arquivo, exceto nomes proprios, termos tecnicos ou nomes de produtos.
- Nao traduzir comandos, codigo, APIs ou nomes de servicos.
- Nao inventar secoes novas quando a tarefa pede equivalencia.
- Mantenha URLs equivalentes quando o usuario pedir apenas troca de idioma.
