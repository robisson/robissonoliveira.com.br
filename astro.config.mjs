import { defineConfig } from "astro/config";
import markdoc from "@astrojs/markdoc";
import react from "@astrojs/react";
import keystatic from "@keystatic/astro";
import sitemap from "@astrojs/sitemap";

const keystaticDigestPolyfill = String.raw`
if (!globalThis.crypto) {
  globalThis.crypto = {};
}

if (!globalThis.crypto.subtle) {
  const toArrayBuffer = (hex) => {
    const bytes = new Uint8Array(hex.length / 2);
    for (let i = 0; i < bytes.length; i++) {
      bytes[i] = parseInt(hex.slice(i * 2, i * 2 + 2), 16);
    }
    return bytes.buffer;
  };

  const sha1 = (content) => {
    const bytes = content instanceof Uint8Array ? content : new Uint8Array(content);
    const words = [];
    for (let i = 0; i < bytes.length; i++) {
      words[i >> 2] |= bytes[i] << (24 - (i % 4) * 8);
    }

    words[bytes.length >> 2] |= 0x80 << (24 - (bytes.length % 4) * 8);
    words[(((bytes.length + 8) >> 6) << 4) + 15] = bytes.length * 8;

    let h0 = 0x67452301;
    let h1 = 0xefcdab89;
    let h2 = 0x98badcfe;
    let h3 = 0x10325476;
    let h4 = 0xc3d2e1f0;

    for (let i = 0; i < words.length; i += 16) {
      const w = words.slice(i, i + 16);
      for (let j = 16; j < 80; j++) {
        const value = w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16];
        w[j] = (value << 1) | (value >>> 31);
      }

      let a = h0;
      let b = h1;
      let c = h2;
      let d = h3;
      let e = h4;

      for (let j = 0; j < 80; j++) {
        const f = j < 20 ? (b & c) | (~b & d) : j < 40 ? b ^ c ^ d : j < 60 ? (b & c) | (b & d) | (c & d) : b ^ c ^ d;
        const k = j < 20 ? 0x5a827999 : j < 40 ? 0x6ed9eba1 : j < 60 ? 0x8f1bbcdc : 0xca62c1d6;
        const temp = (((a << 5) | (a >>> 27)) + f + e + k + (w[j] >>> 0)) >>> 0;
        e = d;
        d = c;
        c = (b << 30) | (b >>> 2);
        b = a;
        a = temp;
      }

      h0 = (h0 + a) >>> 0;
      h1 = (h1 + b) >>> 0;
      h2 = (h2 + c) >>> 0;
      h3 = (h3 + d) >>> 0;
      h4 = (h4 + e) >>> 0;
    }

    return toArrayBuffer([h0, h1, h2, h3, h4].map((value) => value.toString(16).padStart(8, "0")).join(""));
  };

  globalThis.crypto.subtle = {
    digest(algorithm, content) {
      const name = String(algorithm?.name || algorithm).toUpperCase().replace("-", "");
      if (name === "SHA1") {
        return Promise.resolve(sha1(content));
      }
      return Promise.reject(new Error("Unsupported digest algorithm: " + name));
    },
  };
}
`;

function cryptoSubtlePolyfill() {
  return {
    name: "crypto-subtle-polyfill",
    hooks: {
      "astro:config:setup"({ injectScript }) {
        injectScript("before-hydration", keystaticDigestPolyfill);
      },
    },
  };
}

export default defineConfig({
  site: "https://www.robissonoliveira.com.br",
  build: {
    format: "directory",
  },
  integrations: [
    ...(process.env.SKIP_KEYSTATIC === "true" ? [] : [cryptoSubtlePolyfill()]),
    react(),
    markdoc(),
    ...(process.env.SKIP_KEYSTATIC === "true" ? [] : [keystatic()]),
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
