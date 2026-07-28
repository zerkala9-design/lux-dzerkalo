import type { APIRoute } from 'astro';
import { getImage } from 'astro:assets';
import { site } from '../data/site';
import { getProductsByLang, productSlug } from '../lib/products';
import { getCategory, catTitle } from '../data/categories';

export const prerender = true;

const esc = (s: string) =>
  s.replace(/[<>&'"]/g, (c) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;', "'": '&apos;', '"': '&quot;' }[c]!));

// Google Merchant product feed (RSS 2.0). Джерело — товари UA з priceFrom.
// Дзеркала на замовлення не мають GTIN → identifier_exists=no (або g:mpn з sku).
export const GET: APIRoute = async () => {
  const products = (await getProductsByLang('uk')).filter((p) => p.data.priceFrom != null);

  const items = await Promise.all(products
    .map(async (p) => {
      const d = p.data;
      const slug = productSlug(p);
      const link = new URL(`/tovar/${slug}/`, site.url).href;
      // getImage гарантує, що JPEG-файл реально згенерується в dist (на відміну
      // від data.image.src, який може вказувати на неемітований оригінал → 404).
      const optimized = await getImage({ src: d.image, format: 'jpeg', width: 1200 });
      const image = new URL(optimized.src, site.url).href;
      const cat = getCategory(d.category);
      const productType = cat ? catTitle(cat, 'uk') : 'Дзеркала';
      const idBlock = d.sku
        ? `<g:mpn>${esc(d.sku)}</g:mpn>`
        : `<g:identifier_exists>no</g:identifier_exists>`;
      return `  <item>
    <g:id>${esc(slug)}</g:id>
    <title>${esc(d.title)}</title>
    <description>${esc(d.summary)}</description>
    <link>${link}</link>
    <g:image_link>${image}</g:image_link>
    <g:availability>in_stock</g:availability>
    <g:price>${d.priceFrom!.toFixed(2)} UAH</g:price>
    <g:brand>${esc(site.name)}</g:brand>
    <g:condition>new</g:condition>
    ${idBlock}
    <g:google_product_category>Home &amp; Garden &gt; Decor &gt; Mirrors</g:google_product_category>
    <g:product_type>${esc(productType)}</g:product_type>
    <g:excluded_destination>Local_inventory_ads</g:excluded_destination>
    <g:excluded_destination>Free_local_listings</g:excluded_destination>
  </item>`;
    }));
  const itemsXml = items.join('\n');

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
  <title>${esc(site.name)} — каталог</title>
  <link>${site.url}</link>
  <description>Дзеркала на замовлення, дзеркальна плитка, панно — виробник, Київ</description>
${itemsXml}
</channel>
</rss>
`;

  return new Response(xml, {
    headers: { 'Content-Type': 'application/xml; charset=utf-8' },
  });
};
