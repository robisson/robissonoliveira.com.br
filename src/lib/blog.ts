import type { CollectionEntry } from "astro:content";

export type BlogPost = CollectionEntry<"blog">;
export type Locale = "pt-BR" | "en";

export function postSlug(id: string) {
  return id.replace(/\.md$/, "");
}

export function sortPosts(posts: BlogPost[]) {
  return posts.sort((a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf());
}

export function postsByLocale(posts: BlogPost[], locale: Locale) {
  return sortPosts(posts.filter((post) => post.data.language === locale));
}

export function formatPostDate(date: Date, locale: string) {
  return date.toLocaleDateString(locale, {
    day: "2-digit",
    month: "long",
    year: "numeric",
    timeZone: "UTC",
  });
}
