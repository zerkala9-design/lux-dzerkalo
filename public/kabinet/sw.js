/* Service worker кабінету Lux Dzerkalo — офлайн-режим (рахувати без інтернету). */
const CACHE = 'lux-kabinet-v1';
const CORE = [
  '/kabinet/vendor/html2canvas.min.js',
  '/kabinet/vendor/JsBarcode.all.min.js',
  '/kabinet/apple-touch-icon.png'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE).then((c) => c.addAll(CORE)).catch(()=>{}).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;
  if (req.method !== 'GET') return; // логін (POST) — завжди в мережу
  const url = new URL(req.url);
  if (url.origin !== location.origin || !url.pathname.startsWith('/kabinet/')) return;
  if (url.pathname.endsWith('/sync.php')) return; // синхронізацію не кешуємо

  // Сторінка застосунку: мережа-перша, офлайн → кешований калькулятор
  if (req.mode === 'navigate') {
    e.respondWith((async () => {
      try {
        const net = await fetch(req.url, { credentials: 'same-origin', redirect: 'follow' });
        // Кешуємо ЛИШЕ справжній калькулятор (app.php без редіректу на форму входу)
        if (net && net.ok && net.url && net.url.indexOf('/kabinet/app.php') >= 0) {
          const clone = net.clone();
          caches.open(CACHE).then((c) => c.put('/kabinet/app.php', clone)).catch(()=>{});
        }
        return net;
      } catch (err) {
        const cached = await caches.match('/kabinet/app.php');
        return cached || new Response(
          '<!doctype html><meta charset="utf-8"><body style="font-family:sans-serif;background:#05060a;color:#e5e7eb;padding:28px;line-height:1.6">Немає інтернету. Відкрий кабінет один раз при звʼязку — далі калькулятор працюватиме офлайн.</body>',
          { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
      }
    })());
    return;
  }

  // Статика (бібліотеки, іконка): кеш-перша
  e.respondWith((async () => {
    const cached = await caches.match(req);
    if (cached) return cached;
    try {
      const net = await fetch(req);
      if (net && net.ok) { const clone = net.clone(); caches.open(CACHE).then((c) => c.put(req, clone)).catch(()=>{}); }
      return net;
    } catch (err) {
      return cached || Response.error();
    }
  })());
});
