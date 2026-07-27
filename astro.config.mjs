import { defineConfig } from "astro/config";
import sitemap from "@astrojs/sitemap";

export default defineConfig({
  site: "https://www.robissonoliveira.com.br",
  build: {
    format: "directory",
  },
  integrations: [
    sitemap({
      lastmod: new Date(),
      serialize(item) {
        const path = new URL(item.url).pathname;

        if (path === "/" || path === "/en/") {
          return { ...item, changefreq: "weekly", priority: 1 };
        }

        if (path.includes("/blog/")) {
          return { ...item, changefreq: "monthly", priority: path.endsWith("/blog/") ? 0.9 : 0.8 };
        }

        return { ...item, changefreq: "monthly", priority: 0.7 };
      },
    }),
  ],
});
