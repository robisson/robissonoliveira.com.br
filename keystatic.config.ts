import { collection, config, fields } from "@keystatic/core";

export default config({
  storage: {
    kind: "local",
  },
  ui: {
    brand: {
      name: "Robisson Oliveira",
    },
  },
  collections: {
    blog: collection({
      label: "Blog",
      path: "src/content/blog/*",
      slugField: "title",
      entryLayout: "content",
      format: {
        contentField: "content",
      },
      columns: ["pubDate", "language"],
      schema: {
        title: fields.slug({
          name: {
            label: "Title",
            validation: { isRequired: true },
          },
          slug: {
            label: "Slug",
          },
        }),
        seoTitle: fields.text({
          label: "SEO title",
          description: "Optional page title used for search and social previews.",
        }),
        description: fields.text({
          label: "Description",
          multiline: true,
          validation: { isRequired: true },
        }),
        pubDate: fields.date({
          label: "Published date",
          validation: { isRequired: true },
        }),
        updatedDate: fields.date({
          label: "Updated date",
        }),
        tags: fields.array(fields.text({ label: "Tag" }), {
          label: "Tags",
        }),
        series: fields.text({
          label: "Series",
        }),
        originalUrl: fields.url({
          label: "Original URL",
        }),
        language: fields.select({
          label: "Language",
          options: [
            { label: "Portuguese", value: "pt-BR" },
            { label: "English", value: "en" },
          ],
          defaultValue: "pt-BR",
        }),
        content: fields.mdx({
          label: "Content",
          extension: "md",
          options: {
            image: {
              directory: "public/blog",
              publicPath: "/blog/",
            },
          },
        }),
      },
    }),
  },
});
