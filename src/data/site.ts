// Єдине джерело правди для контактів і базової інформації про компанію.
import type { Lang, UIKey } from '../i18n/ui';

export const site = {
  name: 'Lux Zerkalo',
  legalName: 'Lux Zerkalo',
  url: 'https://lux-zerkalo.com.ua',
  email: 'zerkala-@ukr.net',
  address: 'вул. Вадима Гетьмана, 27',
  // Телефони у форматі для відображення та для tel:
  phones: [
    { display: '097 177 25 77', tel: '+380971772577' },
    { display: '099 528 36 37', tel: '+380995283637' },
  ],
  social: {
    // Додайте посилання на профілі — і вони з'являться у шапці, футері та контактах.
    instagram: 'https://www.instagram.com/dzerkala_kyiv/',
    facebook: '',
    telegram: '',
    viber: 'viber://chat?number=+380971772577',
  },
  geo: {
    lat: 50.4399,
    lng: 30.4408,
  },
  analytics: {
    // Вставте ID — і аналітика увімкнеться автоматично.
    ga4: '',                    // напр. 'G-XXXXXXXXXX' (Google Analytics 4)
    googleSiteVerification: '', // код підтвердження Google Search Console (meta-тег)
  },
};

// Локалізовані рядки про компанію
export const siteI18n = {
  uk: {
    addressFull: 'м. Київ, вул. Вадима Гетьмана, 27',
    workingHours: 'Пн–Сб: 10:00 – 18:00',
    description:
      'Виготовлення та монтаж дзеркал на замовлення у Києві: дзеркала для ванної, з фацетом, з LED-підсвіткою, дзеркальна плитка, панно, дзеркала для спортзалів і хореографічні станки. Заміри, доставка, професійний монтаж.',
  },
  ru: {
    addressFull: 'г. Киев, ул. Вадима Гетьмана, 27',
    workingHours: 'Пн–Сб: 10:00 – 18:00',
    description:
      'Изготовление и монтаж зеркал на заказ в Киеве: зеркала для ванной, с фацетом, с LED-подсветкой, зеркальная плитка, панно, зеркала для спортзалов и хореографические станки. Замеры, доставка, профессиональный монтаж.',
  },
} as const;

// Навігація: ключ перекладу + базовий шлях (без мовного префікса)
export const navItems: { key: UIKey; path: string }[] = [
  { key: 'nav.home', path: '/' },
  { key: 'nav.catalog', path: '/katalog/' },
  { key: 'nav.services', path: '/poslugy/' },
  { key: 'nav.gallery', path: '/halereya/' },
  { key: 'nav.articles', path: '/statti/' },
  { key: 'nav.contacts', path: '/kontakty/' },
];

export function siteText(lang: Lang) {
  return siteI18n[lang] ?? siteI18n.uk;
}

export type Site = typeof site;
