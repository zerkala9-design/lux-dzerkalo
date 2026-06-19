// Категорії каталогу. Slug використовується в URL: /katalog/[slug]/
// Поле `key` має збігатися з полем `category` у файлах товарів (src/content/products/*.md).

export interface Category {
  key: string;
  slug: string;
  title: string;
  short: string;
  description: string;
}

export const categories: Category[] = [
  {
    key: 'vanna',
    slug: 'dzerkala-dlya-vannoyi',
    title: 'Дзеркала для ванної кімнати',
    short: 'Вологостійкі дзеркала з підсвіткою та поличками',
    description:
      'Дзеркала для ванної кімнати на замовлення: з LED-підсвіткою, антизапотіванням, поличками та підігрівом. Виготовляємо за вашими розмірами під будь-який інтер’єр.',
  },
  {
    key: 'led',
    slug: 'dzerkala-z-led-pidsvitkoyu',
    title: 'Дзеркала з LED-підсвіткою',
    short: 'Сучасні дзеркала з вбудованим освітленням',
    description:
      'Дзеркала з LED-підсвіткою для ванної, спальні та передпокою. Рівномірне світло, сенсорне вмикання, функція антизапотівання та регулювання яскравості.',
  },
  {
    key: 'facet',
    slug: 'dzerkala-z-fatsetom',
    title: 'Дзеркала з фацетом',
    short: 'Шліфована грань — елегантність і блиск',
    description:
      'Дзеркала з фацетом (шліфованою гранню) надають інтер’єру вишуканості. Виготовляємо фацет від 5 до 30 мм будь-якої форми та розміру.',
  },
  {
    key: 'plitka',
    slug: 'dzerkalna-plitka',
    title: 'Дзеркальна плитка',
    short: 'Дзеркальні панелі для стін і стель',
    description:
      'Дзеркальна плитка для оздоблення стін, стель і колон. Різні форми, розміри та способи кріплення. Візуально розширює простір і додає світла.',
  },
  {
    key: 'panno',
    slug: 'dzerkalni-panno',
    title: 'Дзеркальні панно',
    short: 'Декоративні композиції з дзеркала',
    description:
      'Дзеркальні панно — стильний акцент у вітальні, передпокої чи ресторані. Розробляємо індивідуальний дизайн, виготовляємо та монтуємо «під ключ».',
  },
  {
    key: 'sportzal',
    slug: 'dzerkala-dlya-sportzaliv',
    title: 'Дзеркала для спортзалів',
    short: 'Великі дзеркала та хореографічні станки',
    description:
      'Дзеркала для спортзалів, фітнес-клубів і танцювальних студій. Безпечний монтаж великих полотен, хореографічні станки (балетні станки) на стіну та підлогу.',
  },
  {
    key: 'interier',
    slug: 'dzerkala-v-interyeri',
    title: 'Дзеркала в інтер’єрі',
    short: 'Для спальні, вітальні, передпокою, стелі',
    description:
      'Дзеркала для будь-якої кімнати: спальні, вітальні, передпокою, дитячої та стельові дзеркала. Підбираємо форму, розмір і обрамлення під ваш інтер’єр.',
  },
];

export function getCategory(key: string): Category | undefined {
  return categories.find((c) => c.key === key);
}

export function getCategoryBySlug(slug: string): Category | undefined {
  return categories.find((c) => c.slug === slug);
}
