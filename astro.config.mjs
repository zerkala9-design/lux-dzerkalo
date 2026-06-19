// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

// Адреса продакшн-сайту (для sitemap, canonical, og:url)
export default defineConfig({
  site: 'https://lux-zerkalo.com.ua',
  i18n: {
    locales: ['uk', 'ru'],
    defaultLocale: 'uk',
    routing: { prefixDefaultLocale: false },
  },
  integrations: [sitemap({ i18n: { defaultLocale: 'uk', locales: { uk: 'uk-UA', ru: 'ru-UA' } } })],
  build: {
    format: 'directory',
  },
  image: {
    // Дозволяємо оптимізацію локальних зображень (WebP/AVIF, responsive)
    responsiveStyles: true,
  },
});
