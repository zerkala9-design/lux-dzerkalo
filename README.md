# Lux Zerkalo — сайт

Сучасний сайт компанії з виготовлення дзеркал на замовлення (Київ).
Побудований на [Astro](https://astro.build): статичний HTML на виході (швидко + добре для SEO),
автоматична оптимізація зображень (WebP), мікророзмітка Schema.org, sitemap.

## Запуск

```bash
npm install      # встановити залежності (один раз)
npm run dev      # локальний сервер розробки → http://localhost:4321
npm run build    # зібрати сайт у папку dist/
npm run preview  # переглянути зібраний сайт
```

Для деплою достатньо залити вміст папки `dist/` на хостинг (як звичайну статику).

## Структура

```
src/
├── content/products/   ← ТОВАРИ (кожен товар = один .md файл)
├── data/
│   ├── site.ts          ← контакти, телефони, адреса, меню
│   └── categories.ts    ← категорії каталогу
├── assets/
│   ├── products/        ← фото товарів
│   └── gallery/         ← фото для галереї
├── components/          ← Header, Footer, ProductCard, форма, SEO
├── layouts/             ← загальний шаблон сторінки
└── pages/               ← сторінки сайту
public/                  ← robots.txt, favicon, og-default.jpg
```

## Як додати новий товар

1. Покладіть фото товару в `src/assets/products/` (напр. `nove-foto.jpg`).
2. Створіть файл `src/content/products/nazva-tovaru.md`. Ім'я файлу стане адресою:
   `/tovar/nazva-tovaru/`.
3. Заповніть заголовок (frontmatter) і опис:

```markdown
---
title: "Назва товару"
category: "vanna"            # ключ категорії з src/data/categories.ts
image: "../../assets/products/nove-foto.jpg"
gallery:                      # необов'язково — додаткові фото
  - "../../assets/products/foto-2.jpg"
summary: "Короткий опис для картки та SEO."
features:
  - "Перевага 1"
  - "Перевага 2"
priceFrom: 1500               # необов'язково; ціна "від"
priceUnit: "грн"             # грн / грн/м² / грн/м.п.
order: 10                     # менше число — вище у списку
featured: true                # true → показати на головній
---

Повний опис товару. Підтримує **жирний**, списки, абзаци тощо.
```

4. `npm run build` — товар автоматично з'явиться в каталозі, у своїй категорії,
   у блоці «схожі товари» та (якщо `featured: true`) на головній.

## Як змінити контакти

Усі контакти — в одному файлі `src/data/site.ts` (телефони, email, адреса, графік).
Зміни підхопляться на всіх сторінках і в мікророзмітці.

## Категорії

Категорії описані в `src/data/categories.ts`. Поле `key` має збігатися з полем
`category` у файлах товарів. `slug` використовується в адресі: `/katalog/{slug}/`.

## Форма заявки

Зараз форма відкриває поштовий клієнт із заповненою заявкою (працює без бекенду).
Щоб приймати заявки автоматично — підключіть сервіс форм (Web3Forms / Formspree)
і замініть обробник у `src/components/CallbackForm.astro` на `fetch(endpoint)`.
