import type { CollectionEntry } from "astro:content";

export type BlogPost = CollectionEntry<"blog">;
export type Locale = "pt-BR" | "en";
export type PostImageMetadata = {
  width: number;
  height: number;
  type: string;
};

export function postSlug(id: string) {
  return id.replace(/\.md$/, "");
}

const translationSlugs: Record<string, string> = {
  "2026-07-29-como-o-principio-trabalho-constante-aumenta-a-resiliencia-das-aplicacoes":
    "2026-07-29-how-the-constant-work-principle-increases-application-resilience",
  "2026-07-29-how-the-constant-work-principle-increases-application-resilience":
    "2026-07-29-como-o-principio-trabalho-constante-aumenta-a-resiliencia-das-aplicacoes",
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

  if (slug.includes("hedging-pattern")) {
    return "/blog/2026-07-29-resiliencia-alem-do-obvio-hedging-pattern/hedging-pattern/pt/image-01.png";
  }

  if (slug.includes("trabalho-constante")) {
    return "/blog/trabalho-constante/pt/image-01.png";
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

export function postImageMetadata(id: string): PostImageMetadata {
  const slug = postSlug(id);

  if (slug.includes("constant-work") || slug.includes("trabalho-constante")) {
    return { width: 2085, height: 900, type: "image/png" };
  }

  if (slug.includes("hedging-pattern")) {
    return { width: 1496, height: 850, type: "image/png" };
  }

  if (slug.includes("availability-zone-independence-azi")) {
    return { width: 977, height: 541, type: "image/png" };
  }

  if (slug.includes("estabilidade-estatica") || slug.includes("static-stability")) {
    return { width: 1400, height: 379, type: "image/jpeg" };
  }

  if (slug.includes("reactjs-em-7-dias") || slug.includes("learn-reactjs-in-7-days")) {
    return { width: 2000, height: 392, type: "image/png" };
  }

  if (slug.includes("javascript-funciona") || slug.includes("javascript-works")) {
    return { width: 1920, height: 1080, type: "image/png" };
  }

  return { width: 330, height: 492, type: "image/png" };
}

export function sortPosts(posts: BlogPost[]) {
  return posts.sort((a, b) => {
    const dateOrder = b.data.pubDate.valueOf() - a.data.pubDate.valueOf();

    if (dateOrder !== 0) {
      return dateOrder;
    }

    return postSlug(b.id).localeCompare(postSlug(a.id));
  });
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
