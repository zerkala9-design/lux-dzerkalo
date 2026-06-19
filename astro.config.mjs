// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

// Адреса продакшн-сайту (для sitemap, canonical, og:url)
export default defineConfig({
  site: 'https://lux-zerkalo.com.ua',
  integrations: [sitemap()],
  build: {
    format: 'directory',
  },
  image: {
    // Дозволяємо оптимізацію локальних зображень (WebP/AVIF, responsive)
    responsiveStyles: true,
  },
});
