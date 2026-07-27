# Skill: Books Catalog Maintenance

Use esta skill quando a mudanca tocar a pagina `Livros que Recomendo`, anos, contagens ou livro atual da home.

## Passos

1. Leia `src/lib/books.ts` para entender anos, contagens e caminhos.
2. Leia as paginas de origem em `livros.htm` e `livros/*.htm` quando necessario.
3. Mantenha a pagina principal mostrando o ano mais recente.
4. Atualize contagens manualmente quando novos livros forem adicionados.
5. Em mobile, preserve a navegacao compacta por select quando houver muitos anos.
6. Quando trocar o livro atual da home, garanta que a capa exista localmente.
7. Rode `npm run validate`.

## Regras

- O catalogo continua estatico.
- Nao adicionar AJAX ou backend sem necessidade clara.
- Nao deixar ano sem contagem.
- Nao quebrar rotas `/livros/` e `/livros/<ano>/`.
