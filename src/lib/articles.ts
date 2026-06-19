import { getCollection, type CollectionEntry } from 'astro:content';
import type { Lang } from '../i18n/ui';

export type Article = CollectionEntry<'articles'>;

export function articleSlug(entry: Article): string {
  const parts = entry.id.split('/');
  return parts[parts.length - 1];
}

export function articleLang(entry: Article): Lang {
  return entry.id.startsWith('ru/') ? 'ru' : 'uk';
}

export async function getArticlesByLang(lang: Lang): Promise<Article[]> {
  const all = await getCollection('articles');
  return all
    .filter((a) => articleLang(a) === lang)
    .sort((a, b) => a.data.title.localeCompare(b.data.title, lang === 'ru' ? 'ru' : 'uk'));
}
