import rss from "@astrojs/rss";
import { getCollection } from "astro:content";
import { postSlug, postsByLocale } from "../../lib/blog";

export async function GET(context: { site: URL }) {
  const posts = postsByLocale(await getCollection("blog"), "en");

  return rss({
    title: "Blog - Robisson Oliveira",
    description: "Articles about architecture, cloud and software engineering.",
    site: context.site,
    items: posts.map((post) => ({
      title: post.data.title,
      description: post.data.description,
      pubDate: post.data.pubDate,
      link: `/en/blog/${postSlug(post.id)}/`,
      categories: post.data.tags,
    })),
    customData: "<language>en</language>",
  });
}
