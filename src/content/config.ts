import { defineCollection, z } from "astro:content";

const blog = defineCollection({
  type: "content",
  schema: z.object({
    title: z.string(),
    description: z.string(),
    pubDate: z.coerce.date(),
    updatedDate: z.coerce.date().optional(),
    tags: z.array(z.string()).default([]),
    series: z.string().optional(),
    originalUrl: z.string().url().optional(),
    language: z.enum(["pt-BR", "en"]).default("pt-BR"),
  }),
});

export const collections = { blog };
