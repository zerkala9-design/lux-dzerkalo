import { getCollection, type CollectionEntry } from 'astro:content';
import type { Lang } from '../i18n/ui';

export type Product = CollectionEntry<'products'>;

/** slug товару без мовного префікса ('uk/dzerkalo-z-led' → 'dzerkalo-z-led') */
export function productSlug(entry: Product): string {
  const parts = entry.id.split('/');
  return parts[parts.length - 1];
}

/** мова товару з id ('ru/...' → 'ru') */
export function productLang(entry: Product): Lang {
  return entry.id.startsWith('ru/') ? 'ru' : 'uk';
}

/** Усі товари заданої мови, відсортовані за order */
export async function getProductsByLang(lang: Lang): Promise<Product[]> {
  const all = await getCollection('products');
  return all
    .filter((p) => productLang(p) === lang)
    .sort((a, b) => a.data.order - b.data.order);
}
