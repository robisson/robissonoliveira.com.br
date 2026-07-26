import rss from "@astrojs/rss";
import { getCollection } from "astro:content";
import { postSlug, postsByLocale } from "../lib/blog";

export async function GET(context: { site: URL }) {
  const posts = postsByLocale(await getCollection("blog"), "pt-BR");

  return rss({
    title: "Blog - Robisson Oliveira",
    description: "Artigos sobre arquitetura, cloud, engenharia de software e JavaScript.",
    site: context.site,
    items: posts.map((post) => ({
      title: post.data.title,
      description: post.data.description,
      pubDate: post.data.pubDate,
      link: `/blog/${postSlug(post.id)}/`,
      categories: post.data.tags,
    })),
    customData: "<language>pt-BR</language>",
  });
}
