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

// Колекція статей (SEO-блог). Файли у src/content/articles/{uk,ru}/*.md
const articles = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/articles' }),
  schema: z.object({
    title: z.string(),
    description: z.string().default(''),
    summary: z.string().default(''),
    date: z.string().optional(),
    oldUrl: z.string().optional(),
  }),
});

export const collections = { products, articles };
