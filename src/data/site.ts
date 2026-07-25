// Єдине джерело правди для контактів і базової інформації про компанію.
import type { Lang, UIKey } from '../i18n/ui';

export const site = {
  name: 'Lux Dzerkalo',
  legalName: 'Lux Dzerkalo',
  url: 'https://lux-zerkalo.com.ua',
  email: 'info-plitka@ukr.net',
  address: 'вул. Вадима Гетьмана, 27',
  // Телефони у форматі для відображення та для tel:
  phones: [
    { display: '099 528 36 37', tel: '+380995283637' },
    { display: '097 177 25 77', tel: '+380971772577' },
  ],
  social: {
    // Додайте посилання на профілі — і вони з'являться у шапці, футері та контактах.
    instagram: 'https://www.instagram.com/dzerkala_kyiv/',
    facebook: 'https://www.facebook.com/people/%D0%94%D0%B7%D0%B5%D1%80%D0%BA%D0%B0%D0%BB%D1%8C%D0%BD%D0%B0-%D0%BF%D0%BB%D0%B8%D1%82%D0%BA%D0%B0-%D0%9A%D0%B8%D1%97%D0%B2/100070678067112/',
    telegram: '',
    viber: 'viber://chat?number=+380995283637',
  },
  // Профіль Google Business (Карти) — для мікророзмітки sameAs і кнопки на сайті
  googleMaps: 'https://maps.app.goo.gl/XrrjbaHz5a4jw8dr5',
  // Google Place ID (з ftid профілю) — для прямих посилань на відгуки
  googlePlaceId: 'ChIJ1eUjX6DO1EARr9ZVv9f26OQ',
  // Пряме посилання «залишити відгук» (одразу відкриває форму з зірочками)
  googleReviewUrl: 'https://search.google.com/local/writereview?placeid=ChIJ1eUjX6DO1EARr9ZVv9f26OQ',
  // Усі відгуки профілю (список)
  googleReviewsUrl: 'https://search.google.com/local/reviews?placeid=ChIJ1eUjX6DO1EARr9ZVv9f26OQ',
  geo: {
    lat: 50.4399,
    lng: 30.4408,
  },
  analytics: {
    // Вставте ID — і аналітика увімкнеться автоматично.
    ga4: 'G-R01V9DK2RZ',        // напр. 'G-XXXXXXXXXX' (Google Analytics 4)
    googleSiteVerification: '', // код підтвердження Google Search Console (meta-тег)
  },
};

// Локалізовані рядки про компанію
export const siteI18n = {
  uk: {
    addressFull: 'м. Київ, вул. Вадима Гетьмана, 27',
    workingHours: 'Пн–Пт: 10:00 – 18:00',
    description:
      'Виробник дзеркал та дзеркальної плитки преміумкласу в Києві. Дзеркала на замовлення будь-яких розмірів і форм: для ванної, з фацетом, з LED-підсвіткою, панно, для спортзалів. Власне виробництво, заміри, монтаж, доставка Новою Поштою по всій Україні.',
  },
  ru: {
    addressFull: 'г. Киев, ул. Вадима Гетьмана, 27',
    workingHours: 'Пн–Пт: 10:00 – 18:00',
    description:
      'Производитель зеркал и зеркальной плитки премиум-класса в Киеве. Зеркала на заказ любых размеров и форм: для ванной, с фацетом, с LED-подсветкой, панно, для спортзалов. Собственное производство, замеры, монтаж, доставка Новой Почтой по всей Украине.',
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
