(function(){
  var root = document.documentElement;
  var STORAGE_KEY = 'akround-lang';
  var TITLES = {
    sr: 'Akround — automatizacija poslovnih procesa za MSP',
    ru: 'Akround — автоматизация бизнес-процессов для МСБ',
    en: 'Akround — Business Process Automation for SMBs'
  };
  var DESCRIPTIONS = {
    sr: 'Akround automatizuje radne tokove, e-fakture i izveštaje za mala i srednja preduzeća u Srbiji. Bez ručnog unosa podataka. Zakažite besplatnu konsultaciju.',
    ru: 'Akround автоматизирует рабочие процессы, электронные счета и отчёты для малого и среднего бизнеса в Сербии. Без ручного ввода данных. Запишитесь на бесплатную консультацию.',
    en: 'Akround automates workflows, e-invoicing and reporting for small and medium businesses in Serbia. No manual data entry. Book a free consultation.'
  };
  function applyLang(lang){
    root.setAttribute('data-lang', lang);
    document.querySelectorAll('.lang-switch button').forEach(function(btn){
      btn.setAttribute('aria-pressed', btn.getAttribute('data-set-lang') === lang ? 'true' : 'false');
    });
    if(TITLES[lang]){ document.title = TITLES[lang]; }
    var metaDesc = document.querySelector('meta[name="description"]');
    if(metaDesc && DESCRIPTIONS[lang]){ metaDesc.setAttribute('content', DESCRIPTIONS[lang]); }
    try{ localStorage.setItem(STORAGE_KEY, lang); }catch(e){}
  }
  var saved = 'sr';
  try{ saved = localStorage.getItem(STORAGE_KEY) || 'sr'; }catch(e){}
  applyLang(saved);
  document.querySelectorAll('[data-set-lang]').forEach(function(btn){
    btn.addEventListener('click', function(){ applyLang(btn.getAttribute('data-set-lang')); });
  });

  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){ entry.target.classList.add('in'); io.unobserve(entry.target); }
      });
    }, { threshold: 0.14 });
    document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
  } else {
    document.querySelectorAll('.reveal').forEach(function(el){ el.classList.add('in'); });
  }

  var navShell = document.querySelector('.nav-shell');
  var nav = document.querySelector('.nav');
  window.addEventListener('scroll', function(){
    if(window.scrollY > 12){ nav.style.padding = '6px 10px 6px 16px'; }
    else { nav.style.padding = '10px 14px 10px 20px'; }
  }, { passive:true });

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // departure-board digit flip for stat numbers
  function flipDigit(el){
    var target = el.getAttribute('data-target');
    if(reduceMotion){ el.textContent = target; return; }
    var steps = 9, i = 0;
    (function tick(){
      if(i < steps - 1){
        el.textContent = String(Math.floor(Math.random() * 10));
        i++;
        setTimeout(tick, 45 + i * 16);
      } else {
        el.textContent = target;
      }
    })();
  }
  var statEls = document.querySelectorAll('.stat-value');
  if('IntersectionObserver' in window && statEls.length){
    var statIo = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){ flipDigit(entry.target); statIo.unobserve(entry.target); }
      });
    }, { threshold: 0.6 });
    statEls.forEach(function(el){ statIo.observe(el); });
  } else {
    statEls.forEach(function(el){ el.textContent = el.getAttribute('data-target'); });
  }

  // scroll-tied progress rail through the process steps
  var processFill = document.getElementById('processTrackFill');
  var processList = document.querySelector('.process-list');
  if(processFill && processList){
    var tickingProcess = false;
    function updateProcessFill(){
      tickingProcess = false;
      var r = processList.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      var progress = (vh * 0.7 - r.top) / r.height;
      progress = Math.max(0, Math.min(1, progress));
      processFill.style.transform = 'scaleY(' + progress + ')';
    }
    window.addEventListener('scroll', function(){
      if(!tickingProcess){ tickingProcess = true; requestAnimationFrame(updateProcessFill); }
    }, { passive:true });
    window.addEventListener('resize', updateProcessFill);
    updateProcessFill();
  }

  // subtle cursor parallax on the orbit fields (fine pointers only)
  if(!reduceMotion && window.matchMedia('(pointer: fine)').matches){
    document.querySelectorAll('.hero, .cta-band').forEach(function(section){
      var orbit = section.querySelector('.orbit-field');
      if(!orbit) return;
      section.addEventListener('mousemove', function(e){
        var r = section.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width - 0.5;
        var py = (e.clientY - r.top) / r.height - 0.5;
        orbit.style.transform = 'translate(calc(-50% + ' + (px * 18) + 'px), calc(-50% + ' + (py * 14) + 'px))';
      });
      section.addEventListener('mouseleave', function(){
        orbit.style.transform = 'translate(-50%, -50%)';
      });
    });
  }

  // booking modal
  var modal = document.getElementById('bookingModal');
  var openBtn = document.getElementById('bookCallBtn');
  var closeBtn = document.getElementById('bookingModalClose');
  var form = document.getElementById('bookingForm');
  var formStep = document.getElementById('bookingFormStep');
  var successStep = document.getElementById('bookingSuccessStep');
  var errorBox = document.getElementById('bookingError');
  var submitBtn = document.getElementById('bookingSubmitBtn');

  if(modal && openBtn){
    var lastFocused = null;
    var autoCloseTimer = null;

    function openModal(){
      lastFocused = document.activeElement;
      form.reset();
      errorBox.hidden = true;
      formStep.hidden = false;
      successStep.hidden = true;
      modal.hidden = false;
      requestAnimationFrame(function(){ modal.classList.add('is-open'); });
      document.addEventListener('keydown', onKeydown);
      var firstInput = form.querySelector('input');
      if(firstInput){ setTimeout(function(){ firstInput.focus(); }, 50); }
    }

    function closeModal(){
      modal.classList.remove('is-open');
      document.removeEventListener('keydown', onKeydown);
      if(autoCloseTimer){ clearTimeout(autoCloseTimer); autoCloseTimer = null; }
      setTimeout(function(){ modal.hidden = true; }, reduceMotion ? 0 : 220);
      if(lastFocused && lastFocused.focus){ lastFocused.focus(); }
    }

    function onKeydown(e){
      if(e.key === 'Escape'){ closeModal(); }
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){
      if(e.target === modal){ closeModal(); }
    });

    form.addEventListener('submit', function(e){
      e.preventDefault();
      if(!form.reportValidity()){ return; }

      var webhookUrl = (window.AKROUND_SETTINGS || {}).n8nWebhookUrl;
      var lang = root.getAttribute('data-lang') || 'sr';
      var payload = {
        name: form.name.value.trim(),
        phone: form.phone.value.trim(),
        email: form.email.value.trim(),
        lang: lang,
        source: 'acround',
        submittedAt: new Date().toISOString()
      };

      errorBox.hidden = true;
      submitBtn.disabled = true;

      fetch(webhookUrl, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      }).then(function(res){
        submitBtn.disabled = false;
        if(!res.ok){ throw new Error('bad status ' + res.status); }
        formStep.hidden = true;
        successStep.hidden = false;
        autoCloseTimer = setTimeout(closeModal, 3000);
      }).catch(function(){
        submitBtn.disabled = false;
        errorBox.hidden = false;
      });
    });
  }
})();
