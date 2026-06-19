import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

// Колекція товарів. Щоб додати товар — створіть новий .md файл у src/content/products/
// з відповідним заголовком (frontmatter) і описом у тілі.
const products = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/products' }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      // Має збігатися з `key` у src/data/categories.ts
      category: z.string(),
      image: image(),
      gallery: z.array(image()).optional(),
      summary: z.string(),
      features: z.array(z.string()).default([]),
      priceFrom: z.number().optional(),
      priceUnit: z.string().default('грн'),
      order: z.number().default(100),
      featured: z.boolean().default(false),
    }),
});

export const collections = { products };
