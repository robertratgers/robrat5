(() => {
  const doc = document;
  const root = doc.documentElement;
  let lang = localStorage.getItem('abr-lang') === 'en' ? 'en' : 'nl';

  const qs = (sel, el = doc) => el.querySelector(sel);
  const qsa = (sel, el = doc) => [...el.querySelectorAll(sel)];

  function applyLang(next) {
    lang = next;
    localStorage.setItem('abr-lang', lang);
    root.lang = lang;
    root.dataset.lang = lang;

    qsa('[data-i18n-nl]').forEach((el) => {
      const value = el.getAttribute(`data-i18n-${lang}`);
      if (value != null) el.textContent = value;
    });

    qsa('[data-lang-toggle]').forEach((btn) => {
      btn.textContent = lang === 'nl' ? 'EN' : 'NL';
    });

    const formCopy = window.ABR_I18N?.form?.[lang];
    if (formCopy) {
      const submit = qs('[data-submit]');
      if (submit && !submit.disabled) submit.textContent = formCopy.submit;
    }
  }

  function closeMenu() {
    const menu = qs('#mobile-menu');
    const toggle = qs('[data-menu-toggle]');
    if (!menu || !toggle) return;
    menu.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.classList.remove('is-open');
    doc.body.style.overflow = '';
  }

  function openMenu() {
    const menu = qs('#mobile-menu');
    const toggle = qs('[data-menu-toggle]');
    if (!menu || !toggle) return;
    menu.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    toggle.classList.add('is-open');
    doc.body.style.overflow = 'hidden';
  }

  function initMenu() {
    const toggle = qs('[data-menu-toggle]');
    const menu = qs('#mobile-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
      if (menu.hidden) openMenu();
      else closeMenu();
    });

    qsa('a', menu).forEach((link) => {
      link.addEventListener('click', closeMenu);
    });
  }

  function initLang() {
    qsa('[data-lang-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => applyLang(lang === 'nl' ? 'en' : 'nl'));
    });
    applyLang(lang);
  }

  function initReveal() {
    const items = qsa('.reveal');
    if (!items.length) return;
    if (!('IntersectionObserver' in window)) {
      items.forEach((el) => el.classList.add('is-visible'));
      return;
    }
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );
    items.forEach((el) => io.observe(el));
  }

  function initLightbox() {
    const box = qs('#lightbox');
    if (!box) return;
    const img = qs('img', box);
    const category = qs('[data-lightbox-category]', box);
    const title = qs('[data-lightbox-title]', box);
    const meta = qs('[data-lightbox-meta]', box);

    function close() {
      box.hidden = true;
      doc.body.style.overflow = '';
      img.src = '/favicon.svg';
      img.alt = '';
    }

    function open(btn) {
      img.src = btn.dataset.image || '';
      img.alt = btn.getAttribute(`data-title-${lang}`) || '';
      category.textContent = btn.getAttribute(`data-category-${lang}`) || '';
      title.textContent = btn.getAttribute(`data-title-${lang}`) || '';
      const status = btn.getAttribute(`data-status-${lang}`) || '';
      const price = btn.getAttribute(`data-price-${lang}`) || '';
      meta.textContent = [status, price].filter(Boolean).join(' · ');
      box.hidden = false;
      doc.body.style.overflow = 'hidden';
    }

    qsa('[data-lightbox]').forEach((btn) => {
      btn.addEventListener('click', () => open(btn));
    });

    qs('[data-lightbox-close]', box)?.addEventListener('click', close);
    box.addEventListener('click', (e) => {
      if (e.target === box) close();
    });
    doc.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !box.hidden) close();
    });
  }

  function showAlert(type, message) {
    const alert = qs('#contact-alert');
    if (!alert) return;
    alert.hidden = !message;
    alert.className = `contact-alert${type ? ` contact-alert--${type}` : ''}`;
    alert.textContent = message || '';
  }

  async function loadSeals(refresh = false) {
    const wrap = qs('[data-zegels]');
    const loading = qs('[data-captcha-loading]');
    if (!wrap) return;
    wrap.innerHTML = '';
    if (loading) loading.hidden = false;

    try {
      const url = refresh ? '/api/challenge?refresh=1' : '/api/challenge';
      const res = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!data.ok || !Array.isArray(data.seals)) throw new Error('bad');

      if (loading) loading.hidden = true;
      data.seals.forEach((seal, index) => {
        const label = doc.createElement('label');
        label.className = 'contact-zegel';
        label.innerHTML = `<input type="radio" name="zegel" value="${seal.token}" ${index === 0 ? '' : ''} required>
          <img src="${seal.src}" alt="" width="120" height="120">`;
        wrap.appendChild(label);
      });
    } catch {
      if (loading) loading.hidden = true;
      const copy = window.ABR_I18N?.form?.[lang];
      showAlert('error', copy?.load_error || 'Error');
    }
  }

  function initContact() {
    const form = qs('#contact-form');
    if (!form) return;
    const submit = qs('[data-submit]', form);

    loadSeals();

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const copy = window.ABR_I18N?.form?.[lang] || {};
      const chosen = form.querySelector('input[name="zegel"]:checked');
      if (!chosen) {
        showAlert('error', copy.pick_seal || '');
        return;
      }

      const fd = new FormData(form);
      if (submit) {
        submit.disabled = true;
        submit.textContent = copy.sending || '…';
      }
      showAlert('', '');

      try {
        const res = await fetch('/api/contact', {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.ok) {
          showAlert('success', data.message || 'OK');
          form.reset();
          await loadSeals(true);
        } else {
          showAlert('error', data.message || copy.load_error || 'Error');
          if (data.seals) {
            const wrap = qs('[data-zegels]');
            if (wrap) {
              wrap.innerHTML = '';
              data.seals.forEach((seal) => {
                const label = doc.createElement('label');
                label.className = 'contact-zegel';
                label.innerHTML = `<input type="radio" name="zegel" value="${seal.token}" required>
                  <img src="${seal.src}" alt="" width="120" height="120">`;
                wrap.appendChild(label);
              });
            }
          } else {
            await loadSeals(true);
          }
        }
      } catch {
        showAlert('error', copy.load_error || 'Error');
        await loadSeals(true);
      } finally {
        if (submit) {
          submit.disabled = false;
          submit.textContent = copy.submit || 'Send';
        }
      }
    });
  }

  function initHeaderScroll() {
    const header = qs('.site-header');
    if (!header) return;
    const onScroll = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 12);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  initMenu();
  initLang();
  initReveal();
  initLightbox();
  initContact();
  initHeaderScroll();
  initMailAt();
})();

function initMailAt() {
  const doc = document;
  doc.querySelectorAll('.abr-mail-at[data-mail-local]').forEach((el) => {
    const open = () => {
      const local = el.getAttribute('data-mail-local');
      const domain = el.getAttribute('data-mail-domain');
      if (local && domain) window.location.href = `mailto:${local}@${domain}`;
    };
    el.addEventListener('click', open);
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        open();
      }
    });
  });
}
