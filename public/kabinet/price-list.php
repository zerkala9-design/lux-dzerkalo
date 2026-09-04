<?php
// Самозахист: цей файл віддає застосунок ЛИШЕ авторизованим (сесія з index.php).
// Працює незалежно від .htaccess — прямий доступ без входу неможливий.
@ini_set('session.gc_maxlifetime', '31536000');
session_set_cookie_params([
    'lifetime' => 31536000, 'path' => '/kabinet/', 'secure' => true,
    'httponly' => true, 'samesite' => 'Lax',
]);
session_start();
// Автовхід за токеном «запамʼятати мене» (щоб не питати пароль щоразу)
$PASS_HASH_APP = '$2y$12$Cu21nx9TBkE1/6uf7i.Eo.tviz4.Q7hA/m1/zXvlkBVvorAxWCXri';
$REMEMBER_APP  = hash('sha256', $PASS_HASH_APP . '|lux-kabinet-remember-v1');
if (empty($_SESSION["kabinet_ok"])
    && isset($_COOKIE['kabinet_remember'])
    && hash_equals($REMEMBER_APP, (string) $_COOKIE['kabinet_remember'])) {
    $_SESSION["kabinet_ok"] = true;
}
if (empty($_SESSION["kabinet_ok"])) { header("Location: /kabinet/"); exit; }
header("X-Robots-Tag: noindex, nofollow");
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Прайс-лист · Lux Dzerkalo</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&display=swap">
<style>
  :root{
    --paper:#ffffff; --ink:#1c1c1c; --muted:#6b6b6b; --line:#d9d3c7;
    --gold:#b0863b; --gold-soft:#f3e9d6; --dark:#232323;
    --bg:#eef0ec;
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--ink);
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
    font-size:13px;line-height:1.5;-webkit-font-smoothing:antialiased}
  h1,h2,h3,.head-font{font-family:'Manrope',-apple-system,sans-serif}
  .wrap{max-width:900px;margin:0 auto;padding:22px 18px 60px}

  .bar{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
  a.back{color:#2f6bff;text-decoration:none;font-weight:600;font-size:14px}
  a.back:hover{text-decoration:underline}
  .btn-print{background:#2f6bff;color:#fff;border:none;border-radius:10px;padding:10px 18px;
    font-size:14px;font-weight:700;cursor:pointer}
  .btn-print:hover{background:#1f57e6}

  .docwrap{width:100%}
  .paper{
    background:var(--paper);border:1px solid var(--line);border-radius:4px;
    padding:34px 38px;box-shadow:0 10px 30px rgba(30,25,10,.08);
  }

  .letterhead{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
    padding-bottom:16px;border-bottom:2px solid var(--gold)}
  .brand{display:flex;align-items:center;gap:12px}
  .brand .mark{width:46px;height:46px;border-radius:10px;background:var(--dark);color:#fff;
    display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;letter-spacing:.5px}
  .brand .word{line-height:1.15}
  .brand .word .wm{font-size:21px;font-weight:800;font-family:'Manrope',sans-serif;letter-spacing:.2px}
  .brand .word .wm .lux{color:var(--gold)}
  .brand .tag{font-size:8.5px;letter-spacing:2px;color:var(--muted);margin-top:2px;text-transform:uppercase}
  .supplier{text-align:right;font-size:11px;line-height:1.6;color:#3a3a3a}
  .supplier .sn{font-weight:700;color:var(--ink);font-size:12px}

  .doc-title{text-align:center;margin:20px 0 4px}
  .doc-title h1{font-size:22px;font-weight:800;letter-spacing:1.5px;margin:0;text-wrap:balance}
  .doc-sub{text-align:center;color:var(--muted);font-size:11.5px;margin-bottom:22px}

  .section{margin-top:26px}
  .section:first-of-type{margin-top:8px}
  .section-h{display:flex;align-items:baseline;gap:10px;margin-bottom:10px}
  .section-h .no{font-family:'Manrope',sans-serif;font-weight:800;color:var(--gold);font-size:12px}
  .section-h h2{font-size:14.5px;font-weight:800;margin:0;letter-spacing:.2px}
  .section-h .unit{color:var(--muted);font-size:11px;font-weight:600}

  table{width:100%;border-collapse:collapse}
  .tbl th{background:var(--dark);color:#fff;font-weight:700;font-size:10.5px;text-align:center;
    padding:7px 8px;border:1px solid var(--dark)}
  .tbl td{border:1px solid var(--line);padding:6px 8px;text-align:center;font-size:12px;
    font-variant-numeric:tabular-nums}
  .tbl td.lbl{text-align:left;font-weight:600;background:#faf8f3}
  .tbl tbody tr:nth-child(even) td:not(.lbl){background:#fbfaf7}

  .grid4{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  /* Аркуш завжди має вигляд А4 (як на екрані, так і в друку) — його масштабує
     скрипт нижче, тому колонки тут НЕ повинні перелаштовуватись під вузький екран. */

  .mini-h{font-size:15px;font-weight:800;color:var(--ink);margin-bottom:6px;
    padding-bottom:4px;border-bottom:1px solid var(--gold-soft);font-family:'Manrope',sans-serif}

  .note-strip{margin-top:8px;font-size:10.5px;color:var(--muted);line-height:1.5}

  .terms{margin-top:26px;padding-top:16px;border-top:1px solid var(--line);
    font-size:11px;color:#333;line-height:1.65}
  .terms b{color:var(--ink)}
  .terms ul{margin:6px 0 0;padding-left:18px}
  .terms li{margin-bottom:3px}

  .rule-diagram{display:flex;align-items:center;gap:18px;background:#faf8f3;
    border:1px solid var(--line);border-radius:8px;padding:14px 16px;margin-top:10px}
  .rule-diagram svg{flex:0 0 auto}
  .rule-diagram p{margin:0;font-size:11px;color:#333;line-height:1.55}

  .foot{margin-top:24px;padding-top:12px;border-top:1px solid var(--line);
    font-size:10.5px;color:var(--muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:6px}
  .foot b{color:var(--ink)}
  .foot .gold{color:var(--gold);font-weight:700}

  @media print{
    body{background:#fff}
    .noprint{display:none!important}
    .wrap{max-width:none;padding:0}
    .docwrap{width:auto!important;height:auto!important;overflow:visible!important}
    .paper{border:none;box-shadow:none;border-radius:0;padding:14mm;
           width:auto!important;transform:none!important}
    @page{size:A4;margin:0}
  }
</style>
</head>
<body>

<div class="wrap">

  <div class="bar noprint">
    <a class="back" href="app.php">← До калькулятора</a>
    <button class="btn-print" id="printBtn">🖨 Друкувати</button>
  </div>

  <div class="docwrap"><div class="paper">

    <div class="letterhead">
      <div class="brand">
        <div class="mark">LD</div>
        <div class="word">
          <div class="wm"><span class="lux">Lux</span>Dzerkalo</div>
          <div class="tag">Дзеркала на замовлення</div>
        </div>
      </div>
      <div class="supplier">
        <div class="sn">ФОП Бричков Мерабі Русланович</div>
        <div>Код за ДРФО (РНОКПП): 3092914339</div>
        <div>м. Київ, вул. Вадима Гетьмана, 27</div>
        <div>тел.: 099 528 36 37 · 097 177 25 77</div>
      </div>
    </div>

    <div class="doc-title"><h1>ПРАЙС-ЛИСТ</h1></div>
    <div class="doc-sub">Дзеркала, обробка кромки, фацет, свердління — актуально на <span id="today"></span></div>

    <div class="section">
      <div class="section-h"><span class="no">01</span><h2>Дзеркало — ціна за 1 м²</h2><span class="unit">грн, роздріб</span></div>
      <div class="grid4">
        <div>
          <div class="mini-h">Срібло</div>
          <table class="tbl"><thead><tr><th>Товщ.</th><th>Ціна</th></tr></thead><tbody>
            <tr><td class="lbl">4–5 мм</td><td data-price="price_silver_4_5"></td></tr>
            <tr><td class="lbl">5 мм</td><td data-price="price_silver_5"></td></tr>
            <tr><td class="lbl">6 мм</td><td data-price="price_silver_6"></td></tr>
          </tbody></table>
        </div>
        <div>
          <div class="mini-h">Бронза</div>
          <table class="tbl"><thead><tr><th>Товщ.</th><th>Ціна</th></tr></thead><tbody>
            <tr><td class="lbl">4–5 мм</td><td data-price="price_bronze_4_5"></td></tr>
            <tr><td class="lbl">5 мм</td><td data-price="price_bronze_5"></td></tr>
            <tr><td class="lbl">6 мм</td><td data-price="price_bronze_6"></td></tr>
          </tbody></table>
        </div>
        <div>
          <div class="mini-h">Графіт</div>
          <table class="tbl"><thead><tr><th>Товщ.</th><th>Ціна</th></tr></thead><tbody>
            <tr><td class="lbl">4–5 мм</td><td data-price="price_graphite_4_5"></td></tr>
            <tr><td class="lbl">5 мм</td><td data-price="price_graphite_5"></td></tr>
            <tr><td class="lbl">6 мм</td><td data-price="price_graphite_6"></td></tr>
          </tbody></table>
        </div>
        <div>
          <div class="mini-h">Діамант</div>
          <table class="tbl"><thead><tr><th>Товщ.</th><th>Ціна</th></tr></thead><tbody>
            <tr><td class="lbl">4–5 мм</td><td data-price="price_diamond_4_5"></td></tr>
            <tr><td class="lbl">5 мм</td><td data-price="price_diamond_5"></td></tr>
            <tr><td class="lbl">6 мм</td><td data-price="price_diamond_6"></td></tr>
          </tbody></table>
        </div>
      </div>
    </div>

    <div class="section grid2">
      <div>
        <div class="section-h"><span class="no">02</span><h2>Обробка кромки (полірування)</h2></div>
        <table class="tbl"><thead><tr><th>Товщина скла</th><th>Роздріб</th><th>Опт, від 50 п.м.</th></tr></thead><tbody>
          <tr><td class="lbl">4 мм</td><td data-price="pr_4"></td><td data-price="pr_4_opt"></td></tr>
          <tr><td class="lbl">5 мм</td><td data-price="pr_5"></td><td data-price="pr_5_opt"></td></tr>
          <tr><td class="lbl">6 мм</td><td data-price="pr_6"></td><td data-price="pr_6_opt"></td></tr>
          <tr><td class="lbl">8 мм</td><td data-price="pr_8"></td><td data-price="pr_8_opt"></td></tr>
          <tr><td class="lbl">10 мм</td><td data-price="pr_10"></td><td data-price="pr_10_opt"></td></tr>
          <tr><td class="lbl">12 мм</td><td>—</td><td>—</td></tr>
        </tbody></table>
        <div class="note-strip">грн за 1 погонний метр.</div>
      </div>
      <div>
        <div class="section-h"><span class="no">03</span><h2>Фацет</h2></div>
        <table class="tbl"><thead><tr><th>Ширина</th><th>Роздріб</th><th>Опт, від 50 п.м.</th></tr></thead><tbody>
          <tr><td class="lbl">10 мм</td><td data-price="facet_10"></td><td data-price="facet_10_opt"></td></tr>
          <tr><td class="lbl">15 мм</td><td data-price="facet_15"></td><td data-price="facet_15_opt"></td></tr>
          <tr><td class="lbl">20 мм</td><td data-price="facet_20"></td><td data-price="facet_20_opt"></td></tr>
          <tr><td class="lbl">25 мм</td><td data-price="facet_25"></td><td data-price="facet_25_opt"></td></tr>
          <tr><td class="lbl">30 мм</td><td data-price="facet_30"></td><td data-price="facet_30_opt"></td></tr>
          <tr><td class="lbl">35 мм</td><td data-price="facet_35"></td><td data-price="facet_35_opt"></td></tr>
        </tbody></table>
        <div class="note-strip">грн за 1 погонний метр.</div>
      </div>
    </div>

    <div class="section grid2">
      <div>
        <div class="section-h"><span class="no">04</span><h2>Свердління отворів</h2></div>
        <table class="tbl"><thead><tr><th>Діаметр</th><th>Ціна, грн/шт</th></tr></thead><tbody>
          <tr><td class="lbl">Ø 5–8 мм</td><td data-price="hole_b1"></td></tr>
          <tr><td class="lbl">Ø 10–16 мм</td><td data-price="hole_b2"></td></tr>
          <tr><td class="lbl">Ø 20–30 мм</td><td data-price="hole_b3"></td></tr>
          <tr><td class="lbl">Ø 35–65 мм</td><td data-price="hole_b4"></td></tr>
        </tbody></table>
      </div>
      <div>
        <div class="section-h"><span class="no">—</span><h2>Допуск на зміщення променя</h2></div>
        <div class="rule-diagram">
          <svg width="182" height="162" viewBox="0 0 220 195" fill="none">
            <!-- контур деталі: пологий зріз зліва переходить у вертикальну стінку справа -->
            <path d="M20 155 L20 95 C20 75 38 60 62 58 L100 55 L155 55 L155 155 Z"
                  fill="#fbfaf7" stroke="var(--ink)" stroke-width="1.8" stroke-linejoin="round"/>
            <!-- внутрішній згин -->
            <line x1="100" y1="55" x2="100" y2="155" stroke="var(--ink)" stroke-width="1.2"/>
            <!-- лінія фацетного шва, куди «дивиться» промінь -->
            <line x1="155" y1="155" x2="100" y2="102" stroke="var(--ink)" stroke-width="1.2"/>
            <!-- промінь -->
            <text x="160" y="80" font-size="12" fill="var(--ink)" font-family="Manrope,sans-serif" font-weight="700">промінь</text>
            <line x1="160" y1="86" x2="160" y2="90" stroke="var(--ink)" stroke-width="1"/>
            <line x1="160" y1="90" x2="133" y2="107" stroke="var(--ink)" stroke-width="1"/>
            <!-- зміщення -->
            <line x1="158" y1="160" x2="192" y2="188" stroke="var(--ink)" stroke-width="2.2"/>
            <path d="M186 175 L192 188 L179 183" fill="var(--ink)"/>
            <path d="M164 173 L158 160 L171 165" fill="var(--ink)"/>
            <text x="168" y="182" font-size="13" fill="var(--ink)" font-weight="700" font-family="Manrope,sans-serif">до 2 мм</text>
          </svg>
          <p>Допустиме зміщення променя — до 2 мм.<br>За вимоги максимальної точності до кутів фацет
          рахується за вартістю криволінійного.</p>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-h"><span class="no">05</span><h2>Скло (сировина, не дзеркало)</h2></div>
      <table class="tbl">
        <thead><tr><th style="text-align:left">Назва</th><th>4 мм</th><th>5 мм</th><th>6 мм</th><th>8 мм</th><th>10 мм</th></tr></thead>
        <tbody>
          <tr><td class="lbl">Скло прозоре</td><td>550</td><td>755</td><td>950</td><td></td><td></td></tr>
          <tr><td class="lbl">Скло діамант</td><td></td><td></td><td></td><td></td><td></td></tr>
          <tr><td class="lbl">Лакобель</td><td></td><td></td><td></td><td></td><td></td></tr>
        </tbody>
      </table>
    </div>

    <div class="terms">
      <b>Умови</b>
      <ul>
        <li>Ціни вказані в гривнях, без ПДВ (ФОП не є платником ПДВ).</li>
        <li>Виготовлення за вашими розмірами, заміри та монтаж «під ключ» у Києві та області.</li>
        <li>Доставка Новою Поштою по Україні. Термін виготовлення — від 1–3 робочих днів.</li>
      </ul>
    </div>

    <div class="foot">
      <span><b>Lux Dzerkalo</b> · тел. 099 528 36 37 · 097 177 25 77</span>
      <span class="gold">lux-zerkalo.com.ua</span>
    </div>

  </div></div>
</div>

<script>
  var DEFAULTS = {
    price_silver_4_5:1100, price_silver_5:1500, price_silver_6:2000, price_silver_8:2000, price_silver_10:2200,
    price_bronze_4_5:1550, price_bronze_5:2000, price_bronze_6:2500,
    price_graphite_4_5:1550, price_graphite_5:2000, price_graphite_6:2500,
    price_diamond_4_5:1550, price_diamond_5:2000, price_diamond_6:2500,
    pr_4:80, pr_5:100, pr_6:110, pr_8:125, pr_10:180,
    pr_4_opt:45, pr_5_opt:55, pr_6_opt:60, pr_8_opt:75, pr_10_opt:95,
    facet_10:150, facet_15:175, facet_20:185, facet_25:285, facet_30:330, facet_35:365,
    facet_10_opt:130, facet_15_opt:150, facet_20_opt:160, facet_25_opt:240, facet_30_opt:280, facet_35_opt:310,
    hole_b1:45, hole_b2:65, hole_b3:85, hole_b4:120
  };
  var saved = {};
  try{ saved = JSON.parse(localStorage.getItem('reflectique_prices')||'{}') || {}; }catch(e){}

  function val(key){
    var raw = saved[key];
    var n = (raw!=null && raw!=='') ? parseFloat(raw) : NaN;
    if(!isFinite(n)) n = DEFAULTS[key];
    return n;
  }
  document.querySelectorAll('[data-price]').forEach(function(el){
    var n = val(el.getAttribute('data-price'));
    el.textContent = (n==null || !isFinite(n)) ? '—' : n.toLocaleString('uk-UA');
  });

  document.getElementById('today').textContent = new Date().toLocaleDateString('uk-UA', {day:'2-digit', month:'2-digit', year:'numeric'});

  /* ---- Мобільний перегляд: аркуш A4 масштабується цілком, а не ламається по колонках ---- */
  (function(){
    var A4 = 794;
    var wrap  = document.querySelector('.docwrap');
    var paper = document.querySelector('.paper');
    if(!wrap || !paper) return;
    var on = false;

    function reset(){
      paper.style.transform = '';
      paper.style.transformOrigin = '';
      paper.style.width = '';
      wrap.style.height = '';
      wrap.style.overflow = '';
      on = false;
    }
    function fit(){
      reset();
      var avail = wrap.clientWidth;
      if(avail >= A4) return;
      var k = avail / A4;
      paper.style.width = A4 + 'px';
      paper.style.transformOrigin = 'top left';
      paper.style.transform = 'scale(' + k + ')';
      wrap.style.height = Math.ceil(paper.offsetHeight * k) + 'px';
      wrap.style.overflow = 'hidden';
      on = true;
    }
    window.__fitOff = function(){ if(on) reset(); };
    window.__fitOn  = fit;

    window.addEventListener('resize', fit);
    window.addEventListener('orientationchange', function(){ setTimeout(fit, 150); });
    window.addEventListener('beforeprint', function(){ reset(); });
    window.addEventListener('afterprint',  function(){ setTimeout(fit, 50); });
    window.addEventListener('load', function(){ setTimeout(fit, 60); });
    fit();
  })();

  document.getElementById('printBtn').addEventListener('click', function(){
    if(window.__fitOff) window.__fitOff();
    window.print();
    if(window.__fitOn) setTimeout(window.__fitOn, 300);
  });
</script>

</body>
</html>
