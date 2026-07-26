# CLAUDE.md — Lux Dzerkalo (lux-zerkalo.com.ua)

Контекст проєкту для Claude Code. Читай перед роботою.

## Про бізнес
- **Lux Dzerkalo** — виробник дзеркал і дзеркальної плитки, **Київ, з 2011 року** (власне виробництво, заміри + монтаж «під ключ» своєю бригадою, доставка Новою Поштою по Україні).
- Продукти: дзеркала на замовлення (за розміром, ванна, спортзали, LED, фацет), дзеркальна плитка, панно/панелі, кругле, повний зріст, гримерні, для кав'ярень/HoReCa.
- **NAP (єдине джерело — `src/data/site.ts`):** Київ, вул. Вадима Гетьмана 27 · тел. 099 528 36 37 (головний), 097 177 25 77 · info-plitka@ukr.net · **Пн–Пт 10:00–18:00** · Instagram `dzerkala_kyiv` · Google placeId `ChIJ1eUjX6DO1EARr9ZVv9f26OQ` · geo 50.4399, 30.4408.
- ⚠️ **УФ-друк / фотодрук на дзеркалі — НЕ робимо** (в контент не додавати).

## Стек і команди
- **Astro**, мультимовність UA (корінь) / RU (`/ru/`). Білд: `npm run build` (у `dist/`).
- Після скидання контейнера `node_modules` може зникнути → `npm ci` (або `npm install`), потім білд.
- **Завжди білдити перед пушем.** Перевіряти згенерований HTML у `dist/`.

## Деплой і git
- Робоча/деплойна гілка: **`claude/brave-clarke-vm26lr`** — пуш у неї авто-деплоїть сайт (FTP з `dist/`).
- Комітити й пушити після кожної логічної зміни: `git push origin claude/brave-clarke-vm26lr` (ретраї при мережевих збоях).
- PR #1 (SEO-fixes) і #2 (Schema) — вже змержені. `.md`-звіти (`COMPETITOR_ANALYSIS_REPORT.md`, `SEO_AUDIT_REPORT.md`) лежать у корені (не деплояться).

## Структура
- `src/data/site.ts` — NAP, телефони, соцмережі, години, geo, foundingDate.
- `src/data/categories.ts` — 7 категорій каталогу (vanna, led, facet, plitka, panno, sportzal, interier).
- `src/content/products/{uk,ru}/*.md` — товари (frontmatter: `priceFrom`, `priceExact`, `priceUnit`, `specs[]`, `features[]`, `category`, `image`, `catalogHidden`, `order`). Пара UA/RU — **однакове ім'я файлу**.
- `src/content/articles/{uk,ru}/*.md` — статті. Noindex-легасі — у `src/data/noindex-articles.ts` (виключені з sitemap і блогу).
- **Лендінги (LLM/entity/type-сторінки):** `src/data/landings.ts` (контент UA+RU, різні слаги, опційне фото) + `src/components/pages/LandingPage.astro`. Маршрути — тонкі обгортки в `src/pages/<slug>.astro` та `src/pages/ru/<slug>.astro` (`<LandingPage id="..." />`). Наявні: vyrobnyk, sportzalPk, led, plitka, paneli, pidlohovi, arkovi, kavyarnya.
- `src/layouts/BaseLayout.astro` — глобальний `LocalBusiness` JSON-LD (`@id` `#business`, foundingDate 2011, aggregateRating з реальних відгуків); проп `hreflang` (override для різних слагів).
- `src/components/Seo.astro` — canonical, hreflang (uk/ru/x-default→uk); `hreflang` проп для явної пари.
- `src/components/Faq.astro` — рендерить FAQ + сам емітить `FAQPage` schema (переюзати для FAQ).
- `src/components/CallbackForm.astro` — форма-заявка з якорем `#zayavka`.
- `src/components/Footer.astro` — колонка «Послуги» лінкує лендінги (site-wide).
- `src/components/pages/CatalogPage.astro` — `/katalog/` з блоком «за типом» (хаб «дзеркала на замовлення»).
- **Галерея:** `src/pages/*/halereya` авто-підхоплює `src/assets/gallery/*.{jpg,jpeg,png,webp}`.
- Фото товарів — `src/assets/products/`; фото реальних робіт — `src/assets/gallery/` (лендінг тягне через `image` = ім'я файлу в gallery).

## SEO-принципи (важливо)
- **Антиканібалізація:** той самий пошуковий намір → **підсилюємо наявну** сторінку (title/H1/опис/розділи/FAQ), не плодимо дубль. Новий тип виробу → новий товар/лендінг; уточнення (велике, на замовлення, з підсвіткою) → доповнення наявної.
- **hreflang** взаємний (uk↔ru), x-default→uk. Статті/товари паруються однаковим ім'ям файлу; лендінги — через явний `hreflang`.
- **Schema на сторінку:** LocalBusiness (глобально) + Product(+Offer UAH)/Service + BreadcrumbList + FAQPage. **Не фабрикувати** AggregateRating на товарах (лише реальні відгуки; наш рейтинг — на LocalBusiness).
- **Міграція:** старі `.html`/www/http → 301 у `public/.htaccess` (канонікалізація хоста односкачкова). У GSC «Сторінка з переспрямуванням» — норма, валідацію на ній НЕ запускати. 404-«привиди» від старого футер-бага 301-нуті.
- **Тригери в title/description:** «виробник з 2011», «заміри+монтаж під ключ», «від X грн», «доставка НП».

## Робочий процес із фото (Google Drive)
1. `curl -sSL "https://drive.google.com/uc?export=download&id=<ID>" -o /tmp/x.bin` → визначити тип, скопіювати з розширенням, **переглянути (Read)**.
2. Оптимізувати: `sharp(...).rotate().resize({width: 1080–1280, withoutEnlargement:true}).jpeg({quality:82, mozjpeg:true})` → у `src/assets/gallery/robota-*.jpg` (EXIF стрипається).
3. Підставити на релевантну сторінку (товар `image:` або лендінг `image`+`imageAlt`), забезпечити унікальність фото в каталозі. Білд → коміт → пуш.
- ⚠️ Не ставити фото без дзеркала або «будівельні» кадри на комерційні картки, якщо це вводить в оману.

## Пріоритети контенту (з аналізу конкурентів + Prom)
1. **Спортзали** (Google + Prom — топ попит) — найсильніший контент/фото.
2. **Великі дзеркала на всю стіну / у повний зріст** (Prom: багато показів, слабкий CTR — тягнути заголовки).
3. **Салони краси / гримерні** (B2B).
4. 🟠 HoReCa (є кав'ярня), незвичні форми (сонце, поворотні, трюмо, дизайнерські, ЛОФТ), цінові блоки.

## Дії власника (не кодом)
- **Google Business Profile:** заповнити на 100% (категорії, фото, години Пн–Пт, лінк на сайт) — для панелі знань і карт.
- **GSC → Запит на індексування** для нових сторінок; збирати **відгуки на Google** та беклінки.
