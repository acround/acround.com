/* ==========================================================================
   Резбайка — движение.

   Один жест на весь сайт: точка идёт по контуру. Товар здесь — не картинка,
   а траектория, по которой пойдёт голова станка, поэтому и знак движения
   такой: бегунок проходит путь, пройденное темнеет, непройденное ждёт.
   Тот же жест на глифе в 18 пикселей и на панели в 340 — это и делает
   стиль узнаваемым.

   Три мотива:
     «Рез»     — бегунок по контуру, бесконечно.
     «Слой»    — контуры всплывают друг за другом, как слои печати.
     «Раскрой» — плитки вокруг героя качаются, будто детали на столе станка.

   Всё выключается системной настройкой «уменьшить движение» и останавливается,
   когда уходит из кадра: браузер не должен греться ради того, чего не видно.
   ========================================================================== */
(function () {
  'use strict';

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ── Разворачиваем <use> ──────────────────────────────────────────────
     Контуры лежат в общей библиотеке символов и подключаются через <use>.
     Внутрь <use> ни CSS, ни скрипт не достают — узлы живут в теневом дереве.
     Поэтому перед тем, как пускать бегунок, копируем содержимое символа
     в саму плитку. Без скрипта <use> остаётся на месте и всё так же рисует. */
  function hydrate(box) {
    $$('use', box).forEach(function (use) {
      var id = use.getAttribute('href') || use.getAttribute('xlink:href');
      var sym = id && document.querySelector(id);
      if (!sym) return;
      var host = use.parentNode;
      Array.prototype.forEach.call(sym.childNodes, function (node) {
        host.insertBefore(node.cloneNode(true), use);
      });
      host.removeChild(use);
    });
  }

  /* ── «Рез» ────────────────────────────────────────────────────────────
     Клонируем несущие пути и пускаем по ним короткий штрих. Клон, а не
     анимация оригинала: оригинал должен остаться целым контуром, иначе
     рисунок распадётся на пунктир. */
  function armTrace(box) {
    // Контейнером может быть и сам <svg> — так размечен знак в шапке
    var svg = box.tagName.toLowerCase() === 'svg' ? box : box.querySelector('svg');
    if (!svg || box.dataset.armed) return;
    hydrate(box);

    var count = parseInt(box.getAttribute('data-trace'), 10) || 1;
    var speed = parseFloat(box.getAttribute('data-trace-speed')) || 190; // px пути в секунду
    var dash  = parseFloat(box.getAttribute('data-trace-dash'))  || 26;
    var minLen = parseFloat(box.getAttribute('data-trace-min')) || 60;

    // Линия реза назначена в разметке классом .cut. Если её нет — берём самые
    // длинные контуры, но это запасной вариант: угаданная линия часто оказывается
    // служебной решёткой, и искра рассыпается на отдельные штрихи.
    var marked = $$('.cut', svg);
    var pool = marked.length ? marked : $$('path, ellipse, circle, rect', svg);
    var paths = pool
      .map(function (el) {
        var len = 0;
        try { len = el.getTotalLength ? el.getTotalLength() : 0; } catch (e) { len = 0; }
        return { el: el, len: len };
      })
      .filter(function (p) { return p.len > minLen && isFinite(p.len); })
      .sort(function (a, b) { return b.len - a.len; })
      .slice(0, marked.length ? Math.max(count, marked.length) : count);

    if (!paths.length) return;

    paths.forEach(function (p, i) {
      var run = p.el.cloneNode(false);
      run.setAttribute('class', 'trace-run');
      run.removeAttribute('stroke-opacity');
      run.style.setProperty('--len', Math.ceil(p.len));
      run.style.setProperty('--dash', dash);
      run.style.setProperty('--dur', (p.len / speed).toFixed(2) + 's');
      // Сдвиг фазы: два бегунка, стартующие вместе, читаются как один толстый
      run.style.animationDelay = (-i * (p.len / speed) / count).toFixed(2) + 's';
      p.el.parentNode.appendChild(run);
    });

    box.dataset.armed = '1';
  }

  /* ── «Слой» ───────────────────────────────────────────────────────────
     Двойник «Реза» для второй половины дела. Печать растёт снизу вверх слой
     за слоем — поэтому поверх чертежа ложится его же копия горячим цветом,
     и её открывает поднимающийся срез. Ниже среза вещь уже напечатана, выше
     ещё нет.

     Мотивы разведены по материалу, а не по вкусу: по панелям под лазер идёт
     искра, по вещам под принтер — слой. Движение говорит, какой станок делает
     вещь, ещё до того, как человек прочтёт подпись. */
  function armLayer(box) {
    var svg = box.tagName.toLowerCase() === 'svg' ? box : box.querySelector('svg');
    if (!svg || box.dataset.armed) return;
    hydrate(box);

    // Копируем весь svg, а не группу внутри: копия становится обычным блоком
    // поверх оригинала, наследует те же поля и потому совпадает пиксель в
    // пиксель. Маску по группе внутри svg браузеры тянут неохотно.
    var built = svg.cloneNode(true);
    built.setAttribute('class', 'layer-built');
    built.setAttribute('aria-hidden', 'true');
    built.removeAttribute('data-layer');
    $$('*', built).forEach(function (el) { el.removeAttribute('stroke-opacity'); });
    svg.parentNode.appendChild(built);

    var dur = parseFloat(box.getAttribute('data-layer-dur')) || 5.4;
    box.style.setProperty('--build-dur', dur + 's');
    box.dataset.armed = '1';
  }

  /* ── Кто и когда бежит ────────────────────────────────────────────────
     Бегунок живёт только пока плитка в кадре. Двенадцать одновременных
     штрихов по контурам — это перерисовка всего экрана каждый кадр. */
  var boxes = $$('[data-trace], [data-layer]');
  function arm(el) {
    if (el.hasAttribute('data-layer')) armLayer(el);
    else armTrace(el);
  }
  if (boxes.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) arm(e.target);
          e.target.classList.toggle('is-running', e.isIntersecting);
        });
      }, { rootMargin: '10% 0px', threshold: 0.15 });
      boxes.forEach(function (b) { io.observe(b); });
    } else {
      boxes.forEach(function (b) { arm(b); b.classList.add('is-running'); });
    }
  }


  /* ── Тёмная полоса ────────────────────────────────────────────────────
     Пунктирные разделители едут — станок не стоит. Но только пока полоса в
     кадре: бесконечная анимация за экраном греет процессор впустую. */
  var band = document.querySelector('.void');
  if (band && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { band.classList.toggle('is-running', e.isIntersecting); });
    }, { threshold: 0 }).observe(band);
  } else if (band) {
    band.classList.add('is-running');
  }

  /* ── «Раскрой» ────────────────────────────────────────────────────────
     Плитки героя качаются, как детали, лежащие на столе после реза. Периоды
     нарочно несоразмерны — совпадающий ритм читается как карусель. */
  $$('.scatter__tile').forEach(function (tile, i) {
    tile.style.setProperty('--sway', (13 + i * 2.7).toFixed(1) + 's');
    tile.style.setProperty('--sway-delay', (-i * 1.9).toFixed(1) + 's');
  });

})();

/* ==========================================================================
   Плёнка.
   Отдельно от остального движения: съёмка может не проиграться — старый
   браузер, экономия трафика, запрет автоплея, — и тогда под ней остаётся
   живой чертёж. Показываем видео строго по факту готовности, а не заранее.
   ========================================================================== */
(function () {
  'use strict';

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var SEAM = 0.34;   // секунды затемнения на стыке петли
  var films = Array.prototype.slice.call(document.querySelectorAll('.film'));
  if (!films.length) return;

  films.forEach(function (film) {
    var slot = film.parentNode;

    film.addEventListener('playing', function () {
      film.classList.add('is-live');
      slot.classList.add('has-film');
    }, { once: true });

    function play() {
      var q = film.play();
      if (q && q.catch) q.catch(function () { /* автоплей запрещён — остаётся чертёж */ });
    }

    film.addEventListener('canplay', play, { once: true });

    // Стык петли: гасим и зажигаем, чтобы склейка не читалась рывком
    film.addEventListener('timeupdate', function () {
      if (!film.duration) return;
      var left = film.duration - film.currentTime;
      film.classList.toggle('at-seam', left < SEAM || film.currentTime < SEAM * 0.5);
    });

    // За кадром плёнка не крутится
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (e) { if (e.isIntersecting) play(); else film.pause(); });
      }, { threshold: 0.1 }).observe(slot);
    }

    film.load();
  });
})();
