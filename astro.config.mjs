import { defineConfig } from "astro/config";
import sitemap from "@astrojs/sitemap";

export default defineConfig({
  site: "https://www.robissonoliveira.com.br",
  build: {
    format: "directory",
  },
  integrations: [sitemap()],
});
