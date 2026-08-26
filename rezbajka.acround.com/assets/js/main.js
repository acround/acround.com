/* ==========================================================================
   Резбайка — поведение страницы.
   Ванильный JS, без сборки: положили файлы на хостинг — работает.
   Всё движение выключается системной настройкой «уменьшить движение».
   ========================================================================== */
(function () {
  'use strict';

  /* ─────────────────────────────────────────────────────────────────────
     НАСТРОЙКА. Впишите свои контакты — подставятся в список для прямой
     переписки. Пока поле пустое, соответствующий пункт списка просто не
     показывается (не «укажите адрес» — так выглядит недоделанный сайт).
     ───────────────────────────────────────────────────────────────── */
  var CONTACTS = {
    email:    '',                  // например 'pochta@example.com'
    telegram: '',                  // например 'rezbajka' — без @
    whatsapp: ''                   // например '79990000000' — только цифры
  };

  // Заявки с формы уходят сюда — POST с JSON-телом. Пустая строка — только
  // для локального открытия index.html без Docker. В контейнере подставляется
  // сама, из N8N_WEBHOOK_URL при старте (webhook-url.sh, см. Dockerfile) —
  // руками эту строку в собранном образе лучше не трогать.
  var WEBHOOK_URL = '';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var yearEl = $('#year');

  /* ─────────────────────────────────────────────────────────────────────
     ТРИ ЯЗЫКА, ОДНА СТРАНИЦА. Каждый переводимый узел несёт три соседних
     .lang-sr/.lang-ru/.lang-en (сербский, русский, английский) — видимость
     решает CSS по атрибуту [lang] на <html>, здесь только этот атрибут и
     меняется. Атрибуты (placeholder, aria-label, content меты) и <title>/
     <option> — им нельзя иметь детей — держат все три варианта в
     data-ph-…, data-al-…, data-ct-… и data-… (по языку) и переписываются явно.
     Выбор запоминается в localStorage; язык по умолчанию — сербский.
     ───────────────────────────────────────────────────────────────── */
  var LANGS = ['sr', 'ru', 'en'];
  var DEFAULT_LANG = 'sr';
  var HTML_LANG = { sr: 'sr-Latn', ru: 'ru', en: 'en' };
  var lang = DEFAULT_LANG;

  var MESSAGES = {
    ru: {
      incomplete: 'Заполните имя, способ связи и описание — этого хватит.',
      noWebhook:  'Приём заявок ещё не подключён — впишите адрес вебхука в assets/js/main.js.',
      sending:    'Отправляю…',
      sendError:  'Не получилось отправить. Попробуйте ещё раз или напишите напрямую.'
    },
    sr: {
      incomplete: 'Popunite ime, kontakt i opis — to je dovoljno.',
      noWebhook:  'Prijem porudžbina još nije povezan — upišite adresu vebhuka u assets/js/main.js.',
      sending:    'Šaljem…',
      sendError:  'Nije uspelo slanje. Pokušajte ponovo ili pišite direktno.'
    },
    en: {
      incomplete: 'Fill in your name, contact and description — that is enough.',
      noWebhook:  'Order intake is not wired up yet — add a webhook address in assets/js/main.js.',
      sending:    'Sending…',
      sendError:  'Could not send it. Try again, or write to me directly.'
    }
  };
  function t(name) {
    return (MESSAGES[lang] && MESSAGES[lang][name]) || MESSAGES[DEFAULT_LANG][name] || '';
  }

  function readStoredLang() {
    try {
      var v = window.localStorage.getItem('lang');
      return LANGS.indexOf(v) !== -1 ? v : null;
    } catch (e) { return null; }
  }
  function storeLang(next) {
    try { window.localStorage.setItem('lang', next); } catch (e) { /* приватный режим — переживём */ }
  }

  var ATTR_TARGETS = [
    ['data-ph-', 'placeholder'],
    ['data-al-', 'aria-label'],
    ['data-ct-', 'content']
  ];

  function applyLang(next) {
    lang = next;
    document.documentElement.lang = HTML_LANG[next] || next;
    storeLang(next);

    ATTR_TARGETS.forEach(function (pair) {
      $$('[' + pair[0] + next + ']').forEach(function (el) {
        el.setAttribute(pair[1], el.getAttribute(pair[0] + next) || '');
      });
    });

    // <title> и <option> не могут держать детей — текст лежит в data-*
    var titleEl = document.querySelector('title[data-' + next + ']');
    if (titleEl) document.title = titleEl.getAttribute('data-' + next);
    $$('option[data-' + next + ']').forEach(function (el) {
      el.textContent = el.getAttribute('data-' + next);
    });

    $$('.lang__btn').forEach(function (btn) {
      btn.setAttribute('aria-pressed', String(btn.getAttribute('data-lang') === next));
    });

    // Язык сменился: год, контакты и высота открытых ответов считаются заново
    if (yearEl) yearEl.textContent = new Date().getFullYear();
    applyContacts();
    $$('.faq__item.is-open').forEach(function (item) {
      var a = $('.faq__a', item);
      if (a) a.style.height = a.firstElementChild.offsetHeight + 'px';
    });
  }

  $$('.lang__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      applyLang(btn.getAttribute('data-lang'));
    });
  });

  // Пересчёт по resize событий приходит десятками подряд; считаем один раз,
  // когда человек отпустил рамку окна.
  function onResize(fn) {
    var t;
    window.addEventListener('resize', function () {
      window.clearTimeout(t);
      t = window.setTimeout(fn, 120);
    });
  }

  /* ── Год в подвале ──────────────────────────────────────────────── */
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* ── Меню на телефоне ───────────────────────────────────────────── */
  var nav = $('#nav');
  var burger = $('#burger');
  if (burger && nav) {
    burger.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', String(open));
    });
    $$('.nav__link, .nav .btn', nav).forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ── Пилюля навигации темнеет над тёмной полосой ────────────────── */
  var darkZones = $$('[data-nav-dark]');
  if (nav && darkZones.length) {
    var syncNav = function () {
      var probe = 34 + 21; // верх пилюли + половина её высоты
      var over = darkZones.some(function (z) {
        var r = z.getBoundingClientRect();
        return r.top <= probe && r.bottom >= probe;
      });
      nav.classList.toggle('is-dark', over);
    };
    syncNav();
    window.addEventListener('scroll', syncNav, { passive: true });
    onResize(syncNav);
  }

  /* ── Появление блоков при прокрутке ─────────────────────────────── */
  var rises = $$('.rise');
  if (reduced || !('IntersectionObserver' in window)) {
    rises.forEach(function (el) { el.classList.add('is-in'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.classList.add('is-in');
        io.unobserve(e.target);
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.08 });
    rises.forEach(function (el) { io.observe(el); });
  }

  /* ── Тёмная полоса: объект встаёт из «вида сверху» в 3/4 ───────────
     Это тот самый разворот из ORYZO: пока секция идёт по экрану,
     панель поднимается с плоскости и слегка доворачивается. */
  var stage = $('#metalObject');
  var voidBand = $('#metal');
  if (stage && voidBand) {
    if (reduced) {
      stage.style.setProperty('--rx', '14deg');
      stage.style.setProperty('--rz', '-3deg');
    } else {
      var ticking = false;
      var tiltObject = function () {
        var r = voidBand.getBoundingClientRect();
        var vh = window.innerHeight || 800;
        // 0 — секция только показалась снизу, 1 — уже ушла вверх
        var p = (vh - r.top) / (vh + r.height);
        p = Math.max(0, Math.min(1, p));
        var eased = p * p * (3 - 2 * p);
        stage.style.setProperty('--rx', (52 - eased * 46).toFixed(2) + 'deg');
        stage.style.setProperty('--rz', (-12 + eased * 10).toFixed(2) + 'deg');
        ticking = false;
      };
      var onScrollTilt = function () {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(tiltObject);
      };
      tiltObject();
      window.addEventListener('scroll', onScrollTilt, { passive: true });
      onResize(onScrollTilt);

    }
  }

  /* ── Разлетевшиеся плитки героя тянутся за курсором ──────────────── */
  var tiles = $$('.scatter__tile');
  if (tiles.length && !reduced && window.matchMedia('(hover: hover)').matches) {
    var raf = null;
    window.addEventListener('mousemove', function (ev) {
      if (raf) return;
      raf = window.requestAnimationFrame(function () {
        var cx = (ev.clientX / window.innerWidth - 0.5);
        var cy = (ev.clientY / window.innerHeight - 0.5);
        tiles.forEach(function (t) {
          var d = parseFloat(t.getAttribute('data-depth')) || 0;
          t.style.setProperty('--tx', (cx * d).toFixed(2) + 'px');
          t.style.setProperty('--ty', (cy * d).toFixed(2) + 'px');
        });
        raf = null;
      });
    }, { passive: true });
  }

  /* ── Смена кадров: и внутри плитки, и между плитками ─────────────────
     Одиннадцать снимков работ на шесть мест — часть плиток держит не один
     кадр, а несколько внахлёст, и сама переключает их по кругу, вразнобой,
     независимо от того, видна плитка сейчас или нет. Отдельно, только
     ниже 1360px, где шести карточкам в ряд не хватает ширины без обрезки
     или прокрутки (style.css), тем же приёмом переключается, какая из
     шести плиток видна целиком. У «уменьшить движение» смены нет: остаётся
     первый кадр из перемешанных на каждом уровне. */
  function shuffledOrder(n) {
    var order = [];
    for (var k = 0; k < n; k++) order[k] = k;
    for (var i = order.length - 1; i > 0; i--) {
      var j = Math.floor(Math.random() * (i + 1));
      var tmp = order[i]; order[i] = order[j]; order[j] = tmp;
    }
    return order;
  }

  // Видимость шапки — общая для всех каруселей ниже, чтобы не заводить
  // по наблюдателю на каждую плитку.
  var heroVisible = true;
  var heroEl = $('.hero');
  var heroWatchers = [];
  if (heroEl && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { heroVisible = e.isIntersecting; });
      heroWatchers.forEach(function (fn) { fn(); });
    }, { threshold: 0 }).observe(heroEl);
  }

  // items — соседи внахлёст (один активен через класс is-active), extraGate —
  // дополнительное условие сверх видимости шапки. Возвращает sync для тех,
  // кому нужно дёрнуть пересчёт по своему событию (смена ширины экрана).
  function cycle(items, intervalMs, extraGate) {
    if (items.length < 2) return null;
    var order = shuffledOrder(items.length);
    items.forEach(function (el) { el.classList.remove('is-active'); });
    var cur = 0;
    items[order[0]].classList.add('is-active');
    if (reduced) return null;
    var timer = null;
    function tick() {
      items[order[cur]].classList.remove('is-active');
      cur = (cur + 1) % order.length;
      items[order[cur]].classList.add('is-active');
    }
    function sync() {
      var on = heroVisible && (!extraGate || extraGate());
      if (on && !timer) timer = window.setInterval(tick, intervalMs);
      else if (!on && timer) { window.clearInterval(timer); timer = null; }
    }
    heroWatchers.push(sync);
    sync();
    return sync;
  }

  // Внутри плитки — снимки одной вещи сменяют друг друга, на любой ширине.
  tiles.forEach(function (t) { cycle($$('img', t), 3400 + Math.random() * 800); });

  // Между плитками — какая из шести видна целиком, только на узких экранах.
  if (tiles.length > 1) {
    var mqSmall = window.matchMedia('(max-width: 1360px)');
    var outerSync = cycle(tiles, 2600, function () { return mqSmall.matches; });
    if (outerSync) mqSmall.addEventListener('change', outerSync);
  }

  /* ── Фотографии работ ────────────────────────────────────────────────
     Снимок показываем только по факту загрузки. Файла ещё нет или он не
     дошёл — в плитке остаётся чертёж, и посетитель не видит ни битой
     картинки, ни пустого места. */
  $$('.work__photo').forEach(function (img) {
    var frame = img.parentNode;
    function show() { frame.classList.add('has-photo'); }
    if (img.complete && img.naturalWidth > 0) show();
    else img.addEventListener('load', show, { once: true });
    img.addEventListener('error', function () { img.remove(); }, { once: true });
  });

  /* ── Фильтр стены работ ──────────────────────────────────────────────
     Раньше плитки пропадали мгновенно: display none — и колонки прыгали.
     Теперь лишние сначала гаснут и оседают, и только потом уходят из потока,
     а вернувшиеся проявляются. Полсекунды — но щелчок перестаёт читаться
     поломкой. */
  var chips = $$('.chip');
  var works = $$('.work');
  if (chips.length && works.length) {
    var FADE = 280;    // столько гаснет уходящая плитка
    var ENTER = 520;   // столько длится пружина появления
    var hideTimer = null;
    var enterTimer = null;

    function filterTo(f) {
      window.clearTimeout(hideTimer);
      window.clearTimeout(enterTimer);

      works.forEach(function (w) {
        var show = f === 'all' || w.getAttribute('data-cat') === f;
        w.classList.toggle('is-leaving', !show);
        if (show && w.classList.contains('is-hidden')) {
          w.classList.remove('is-hidden');
          w.classList.add('is-entering');
        }
      });

      // Из потока убираем, когда уходящая догасла
      hideTimer = window.setTimeout(function () {
        works.forEach(function (w) {
          if (w.classList.contains('is-leaving')) w.classList.add('is-hidden');
        });
      }, FADE);

      // Метку появления снимаем только после того, как пружина отработала
      enterTimer = window.setTimeout(function () {
        works.forEach(function (w) { w.classList.remove('is-entering'); });
      }, ENTER);
    }

    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        chips.forEach(function (c) { c.setAttribute('aria-pressed', String(c === chip)); });
        filterTo(chip.getAttribute('data-filter'));
      });
    });
  }

  /* ── Вопросы ─────────────────────────────────────────────────────
     Высота анимируется от реальной, иначе на длинных ответах дёргает. */
  $$('.faq__item').forEach(function (item) {
    var q = $('.faq__q', item);
    var a = $('.faq__a', item);
    if (!q || !a) return;
    q.addEventListener('click', function () {
      var open = item.classList.toggle('is-open');
      q.setAttribute('aria-expanded', String(open));
      a.style.height = open ? a.firstElementChild.offsetHeight + 'px' : '0px';
    });
    onResize(function () {
      if (item.classList.contains('is-open')) a.style.height = a.firstElementChild.offsetHeight + 'px';
    });
  });

  /* ── Контакты из настройки выше ─────────────────────────────────────
     Пункт списка показывается, только если для него есть значение —
     иначе «укажите адрес» читается как недоделанный сайт, а не как
     приглашение написать. */
  function applyContacts() {
    var list = $('#contactList');
    var items = [
      { key: 'email', value: CONTACTS.email,
        text: function (v) { return v; }, href: function (v) { return 'mailto:' + v; } },
      { key: 'tg', value: CONTACTS.telegram,
        text: function (v) { return '@' + v; }, href: function (v) { return 'https://t.me/' + v; } },
      { key: 'wa', value: CONTACTS.whatsapp,
        text: function (v) { return '+' + v; }, href: function (v) { return 'https://wa.me/' + v; } }
    ];
    var anyVisible = false;
    items.forEach(function (it) {
      var li = list && $('[data-contact-item="' + it.key + '"]', list);
      // Один и тот же контакт повторён в трёх .lang-* — обновляем все копии,
      // видна из них всегда только одна (решает CSS по [lang] на <html>).
      var links = li ? $$('[data-contact="' + it.key + '"]', li) : [];
      if (!li || !links.length) return;
      if (it.value) {
        links.forEach(function (a) {
          a.textContent = it.text(it.value);
          a.href = it.href(it.value);
        });
        li.classList.add('is-visible');
        anyVisible = true;
      } else {
        li.classList.remove('is-visible');
      }
    });
    if (list) list.classList.toggle('is-visible', anyVisible);
  }
  applyContacts();

  /* ── Форма ────────────────────────────────────────────────────────
     Заявка уходит вебхуком в n8n (WEBHOOK_URL выше). no-cors — намеренно:
     большинство вебхуков n8n не шлют CORS-заголовки, и в обычном режиме
     fetch считал бы успешно принятую заявку ошибкой. Плата за это —
     подтверждение «спасибо» покажется, даже если адрес вебхука в
     WEBHOOK_URL неверный: проверьте один раз тестовой заявкой после
     подключения. */
  var form = $('#orderForm');
  var note = $('#formNote');
  var slot = $('.form__slot');
  if (form) {
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var data = new FormData(form);
      var name = (data.get('name') || '').toString().trim();
      var contact = (data.get('contact') || '').toString().trim();
      var brief = (data.get('brief') || '').toString().trim();

      if (!name || !contact || !brief) {
        if (note) note.textContent = t('incomplete');
        return;
      }
      if (!WEBHOOK_URL) {
        if (note) note.textContent = t('noWebhook');
        return;
      }

      var submitBtn = $('button[type="submit"]', form);
      if (submitBtn) submitBtn.disabled = true;
      if (note) note.textContent = t('sending');

      fetch(WEBHOOK_URL, {
        method: 'POST',
//        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          source: 'rezbajka',
          name: name,
          contact: contact,
          kind: (data.get('kind') || '').toString(),
          brief: brief,
          lang: lang,
          page: window.location.href,
          sentAt: new Date().toISOString()
        })
      }).then(function () {
        if (slot) slot.classList.add('is-sent');
      }).catch(function () {
        if (note) note.textContent = t('sendError');
        if (submitBtn) submitBtn.disabled = false;
      });
    });
  }

  applyLang(readStoredLang() || DEFAULT_LANG);
})();
