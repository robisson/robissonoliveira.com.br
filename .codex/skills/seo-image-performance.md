# Skill: SEO Image Performance

Use esta skill quando trabalhar com imagens de home, blog, livros, projetos, Open Graph ou screenshots.

## Checklist

- Imagens publicas ficam em `public/` quando referenciadas por URL string.
- Caminhos devem funcionar localmente e em producao.
- Cada imagem no conteudo tem `alt` descritivo.
- Imagem principal do post esta mapeada em `postImage()`.
- `og:image` deve apontar para imagem relevante, nao placeholder generico quando houver imagem do artigo.
- Testar `content_type` e `size_download` com `curl`.

## Performance

- Preferir imagens com dimensoes razoaveis para o uso.
- Evitar imagens enormes quando forem apenas thumbnails.
- Manter formatos originais quando a fidelidade importa.
- Se otimizar imagem, conferir visualmente antes de substituir.

## Cuidado Astro

Arquivos em `assets/` nao sao servidos publicamente quando usados como string HTML. Para `/assets/images/foo.png`, o arquivo precisa existir em `public/assets/images/foo.png`.
