import type { CollectionEntry } from "astro:content";

export type BlogPost = CollectionEntry<"blog">;
export type Locale = "pt-BR" | "en";

export function postSlug(id: string) {
  return id.replace(/\.md$/, "");
}

const translationSlugs: Record<string, string> = {
  "2024-08-05-como-a-estabilidade-estatica-aumenta-a-resiliencia-da-sua-aplicacao":
    "2024-08-05-how-static-stability-increases-application-resilience",
  "2024-08-05-how-static-stability-increases-application-resilience":
    "2024-08-05-como-a-estabilidade-estatica-aumenta-a-resiliencia-da-sua-aplicacao",
  "2024-08-12-entendendo-availability-zone-independence-azi":
    "2024-08-12-understanding-availability-zone-independence-azi",
  "2024-08-12-understanding-availability-zone-independence-azi":
    "2024-08-12-entendendo-availability-zone-independence-azi",
};

export function translatedPostSlug(id: string) {
  return translationSlugs[postSlug(id)];
}

export function postImage(id: string) {
  const slug = postSlug(id);

  if (slug.includes("availability-zone-independence-azi")) {
    return "/blog/availability-zone-independence-azi/image-01.png";
  }

  if (slug.includes("estabilidade-estatica") || slug.includes("static-stability")) {
    return "/blog/estabilidade-estatica/image-01.jpg";
  }

  return "/assets/images/robisson-home.png";
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
