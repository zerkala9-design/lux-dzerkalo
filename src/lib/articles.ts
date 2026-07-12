import { getCollection, type CollectionEntry } from 'astro:content';
import type { Lang } from '../i18n/ui';
import { noindexArticleSet } from '../data/noindex-articles';

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

/** Легасі-стаття, яку виводимо з індексу (noindex + без sitemap/лістингу). */
export function isNoindexArticle(entry: Article): boolean {
  return articleLang(entry) === 'ru' && noindexArticleSet.has(articleSlug(entry));
}

/** Множини slug-ів статей по мовах — для перевірки наявності мовного двійника. */
export async function getArticleSlugSets(): Promise<{ uk: Set<string>; ru: Set<string> }> {
  const all = await getCollection('articles');
  const uk = new Set<string>();
  const ru = new Set<string>();
  for (const a of all) {
    (articleLang(a) === 'ru' ? ru : uk).add(articleSlug(a));
  }
  return { uk, ru };
}

/** Чи існує стаття з тим самим slug у протилежній мові. */
export async function articleHasTwin(entry: Article): Promise<boolean> {
  const { uk, ru } = await getArticleSlugSets();
  const slug = articleSlug(entry);
  return articleLang(entry) === 'ru' ? uk.has(slug) : ru.has(slug);
}
