// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { noindexArticleSlugs } from './src/data/noindex-articles';

// Тонкі легасі-статті RU виводимо з sitemap (вони мають <meta noindex>).
const noindexPaths = noindexArticleSlugs.map((slug) => `/ru/statti/${slug}/`);

// Адреса продакшн-сайту (для sitemap, canonical, og:url)
export default defineConfig({
  site: 'https://lux-zerkalo.com.ua',
  i18n: {
    locales: ['uk', 'ru'],
    defaultLocale: 'uk',
    routing: { prefixDefaultLocale: false },
  },
  integrations: [
    sitemap({
      i18n: { defaultLocale: 'uk', locales: { uk: 'uk-UA', ru: 'ru-UA' } },
      filter: (page) => !noindexPaths.some((p) => page.endsWith(p)),
      // lastmod = дата білду: сигнал Google про свіжість → швидша переіндексація змінених сторінок.
      serialize: (item) => ({ ...item, lastmod: new Date().toISOString() }),
    }),
  ],
  build: {
    format: 'directory',
  },
  image: {
    // Дозволяємо оптимізацію локальних зображень (WebP/AVIF, responsive)
    responsiveStyles: true,
  },
});
