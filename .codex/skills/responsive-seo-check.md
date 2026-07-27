# Skill: Responsive SEO Check

Use esta skill quando a mudanca tocar layout, navegacao, blog, livros ou SEO.

## Checklist

- Desktop acima de 1200px.
- Tablet portrait e landscape.
- Mobile portrait e landscape.
- Menu hamburger abre sidebar no mobile.
- Rodape nao deixa scroll vazio abaixo.
- Conteudo nao fica com largura de meia tela em mobile.
- Artigos usam largura confortavel de leitura.
- Blog e listas usam bem o espaco disponivel.
- `npm run validate` passa.
- RSS e sitemap sao gerados.

## SEO

- Cada pagina tem titulo e descricao claros.
- Posts tem `title`, `description`, `pubDate`, `tags` e `language`.
- Posts traduzidos tem par de slugs em `src/lib/blog.ts`.
- Imagem principal do post esta mapeada em `postImage()`.

