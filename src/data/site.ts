// Єдине джерело правди для контактів і базової інформації про компанію.
// Зміни тут — і вони підхопляться на всіх сторінках.

export const site = {
  name: 'Lux Zerkalo',
  legalName: 'Lux Zerkalo',
  tagline: 'Дзеркала на замовлення у Києві',
  description:
    'Виготовлення та монтаж дзеркал на замовлення у Києві: дзеркала для ванної, з фацетом, з LED-підсвіткою, дзеркальна плитка, панно, дзеркала для спортзалів і хореографічні станки. Заміри, доставка, професійний монтаж.',
  url: 'https://lux-zerkalo.com.ua',
  city: 'Київ',
  address: 'вул. Вадима Гетьмана, 27',
  addressFull: 'м. Київ, вул. Вадима Гетьмана, 27',
  email: 'zerkala-@ukr.net',
  // Телефони у форматі для відображення та для tel:
  phones: [
    { display: '097 177 25 77', tel: '+380971772577' },
    { display: '099 528 36 37', tel: '+380995283637' },
  ],
  workingHours: 'Пн–Сб: 9:00 – 19:00',
  social: {
    // За потреби додайте посилання на соцмережі
    instagram: '',
    facebook: '',
    telegram: '',
    viber: 'viber://chat?number=+380971772577',
  },
  geo: {
    // Приблизні координати вул. Вадима Гетьмана, Київ
    lat: 50.4399,
    lng: 30.4408,
  },
};

export const nav = [
  { label: 'Головна', href: '/' },
  { label: 'Каталог', href: '/katalog/' },
  { label: 'Послуги', href: '/poslugy/' },
  { label: 'Галерея', href: '/halereya/' },
  { label: 'Контакти', href: '/kontakty/' },
];

export type Site = typeof site;
