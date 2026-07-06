// Відгуки клієнтів для блоку на сайті + мікророзмітка Review/AggregateRating.
// ВАЖЛИВО: тут мають бути ТІЛЬКИ реальні відгуки (з Google-профілю тощо).
// Фейкові відгуки/рейтинг заборонені правилами Google.

export interface Review {
  author: string;   // ім'я автора
  rating: number;   // 1..5
  text: string;     // текст відгуку
  date?: string;    // 'YYYY-MM-DD' (необов'язково)
  url?: string;     // пряме посилання на відгук/профіль автора в Google (необов'язково)
}

// Загальний рейтинг із Google-профілю (оновлюйте за фактом)
export const reviewStats = {
  ratingValue: 4.3,
  reviewCount: 9,
};

// Реальні відгуки. Заповніть масив — і блок з'явиться на сайті автоматично.
export const reviews: Review[] = [
  {
    author: 'Руслан Сичевський',
    rating: 5,
    text: 'Робили дзеркала на замовлення в Студію краси. Якість шикарна — всі дівчата задоволені. Зробили під наші заміри.',
    date: '2024-02-17',
  },
  {
    author: 'Vladimir Shevchenko',
    rating: 5,
    text: 'Пришёл, заказал, забрал. Мне понравилось)',
    date: '2017-11-26',
  },
  {
    author: 'Liana Amorim',
    rating: 5,
    text: 'Все сподобалось',
    date: '2026-03-09',
  },
  {
    author: 'Luciana',
    rating: 5,
    text: 'Все сподобалось',
    date: '2026-03-09',
  },
];
