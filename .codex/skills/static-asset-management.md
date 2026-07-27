# Skill: Static Asset Management

Use esta skill quando a mudanca envolver imagens, icones, capas de livros, screenshots, OG images ou assets referenciados por paginas Astro.

## Principio

Qualquer arquivo referenciado por URL publica absoluta, como `/assets/images/foto.png` ou `/blog/post/image-01.png`, precisa existir em `public/` antes do build.

## Passos

1. Localize todos os `src`, `href`, `poster` e URLs de imagem afetados.
2. Coloque assets publicos em `public/assets/...` ou `public/blog/...`.
3. Use caminhos absolutos a partir da raiz do site.
4. Rode `npm run build`.
5. Teste com `curl` no servidor local:

```bash
curl -s -o /dev/null -w '%{http_code} %{content_type} %{size_download}\n' http://localhost:8080/assets/images/arquivo.png
```

6. Se usar servidor estatico local, sirva `dist/` depois do build.

## Cuidados

- `assets/` nao e servido diretamente pelo Astro quando usado como string em HTML.
- `public/` e copiado para `dist/`.
- Evite URLs absolutas de producao para assets que devem funcionar localmente.
- Confira tamanho e tipo de conteudo para detectar HTML de erro servido como imagem.
