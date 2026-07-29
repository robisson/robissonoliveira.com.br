import type { CollectionEntry } from "astro:content";

export type BlogPost = CollectionEntry<"blog">;
export type Locale = "pt-BR" | "en";

export function postSlug(id: string) {
  return id.replace(/\.md$/, "");
}

const translationSlugs: Record<string, string> = {
  "2026-07-28-como-o-principio-constant-work-aumenta-a-resiliencia-das-aplicacoes":
    "2026-07-28-how-the-constant-work-principle-increases-application-resilience",
  "2026-07-28-how-the-constant-work-principle-increases-application-resilience":
    "2026-07-28-como-o-principio-constant-work-aumenta-a-resiliencia-das-aplicacoes",
  "2024-08-05-como-a-estabilidade-estatica-aumenta-a-resiliencia-da-sua-aplicacao":
    "2024-08-05-how-static-stability-increases-application-resilience",
  "2024-08-05-how-static-stability-increases-application-resilience":
    "2024-08-05-como-a-estabilidade-estatica-aumenta-a-resiliencia-da-sua-aplicacao",
  "2024-08-12-entendendo-availability-zone-independence-azi":
    "2024-08-12-understanding-availability-zone-independence-azi",
  "2024-08-12-understanding-availability-zone-independence-azi":
    "2024-08-12-entendendo-availability-zone-independence-azi",
  "2019-01-26-voce-ou-o-seu-time-nao-vao-aprender-reactjs-em-7-dias-ou-em-um-hello-world":
    "2019-01-26-you-or-your-team-will-not-learn-reactjs-in-7-days-or-in-a-hello-world",
  "2019-01-26-you-or-your-team-will-not-learn-reactjs-in-7-days-or-in-a-hello-world":
    "2019-01-26-voce-ou-o-seu-time-nao-vao-aprender-reactjs-em-7-dias-ou-em-um-hello-world",
  "2019-03-11-como-o-javascript-funciona-entendendo-as-funcoes-e-suas-formas-de-uso":
    "2019-03-11-how-javascript-works-understanding-functions-and-how-to-use-them",
  "2019-03-11-how-javascript-works-understanding-functions-and-how-to-use-them":
    "2019-03-11-como-o-javascript-funciona-entendendo-as-funcoes-e-suas-formas-de-uso",
};

export function translatedPostSlug(id: string) {
  return translationSlugs[postSlug(id)];
}

export function postImage(id: string) {
  const slug = postSlug(id);

  if (slug.includes("how-the-constant-work")) {
    return "/blog/constant-work/en/image-01.png";
  }

  if (slug.includes("constant-work")) {
    return "/blog/constant-work/pt/image-01.png";
  }

  if (slug.includes("availability-zone-independence-azi")) {
    return "/blog/availability-zone-independence-azi/image-01.png";
  }

  if (slug.includes("estabilidade-estatica") || slug.includes("static-stability")) {
    return "/blog/estabilidade-estatica/image-01.jpg";
  }

  if (slug.includes("reactjs-em-7-dias") || slug.includes("learn-reactjs-in-7-days")) {
    return "/blog/reactjs-7-dias-hello-world/image-01.png";
  }

  if (slug.includes("javascript-funciona") || slug.includes("javascript-works")) {
    return "/blog/javascript-funcoes/image-01.png";
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
