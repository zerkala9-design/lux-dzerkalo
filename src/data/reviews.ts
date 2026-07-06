// Відгуки клієнтів для блоку на сайті + мікророзмітка Review/AggregateRating.
// ВАЖЛИВО: тут мають бути ТІЛЬКИ реальні відгуки (з Google-профілю тощо).
// Фейкові відгуки/рейтинг заборонені правилами Google.

export interface Review {
  author: string;   // ім'я автора
  rating: number;   // 1..5
  text: string;     // текст відгуку
  date?: string;    // 'YYYY-MM-DD' (необов'язково)
}

// Загальний рейтинг із Google-профілю (оновлюйте за фактом)
export const reviewStats = {
  ratingValue: 4.3,
  reviewCount: 9,
};

// Реальні відгуки. Заповніть масив — і блок з'явиться на сайті автоматично.
export const reviews: Review[] = [
  // Приклад формату (замініть на справжні відгуки):
  // { author: 'Ірина', rating: 5, text: 'Замовляли дзеркало у ванну — усе ідеально...', date: '2025-11-20' },
];
