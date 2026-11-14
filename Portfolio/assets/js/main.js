// Portfolio UI: persistent theme, loading intro, tab navigation, carousel, and hero parallax.
(() => {
  const body = document.body;
  const themeKey = 'portfolio-theme';
  try {
    const storedTheme = localStorage.getItem(themeKey);
    body.dataset.theme = storedTheme === 'light' ? 'light' : 'dark';
  } catch (_) {
    body.dataset.theme = 'dark';
  }

  const header = document.querySelector('.topbar');
  const nav = document.querySelector('.nav');
  const menuToggle = document.querySelector('.menu-toggle');

  // Add a visible light/dark switch without changing the existing PHP markup.
  if (header && !header.querySelector('.theme-toggle')) {
    const themeButton = document.createElement('button');
    themeButton.type = 'button';
    themeButton.className = 'theme-toggle';
    const syncThemeButton = () => {
      const light = body.dataset.theme === 'light';
      themeButton.textContent = light ? '☾' : '☼';
      themeButton.setAttribute('aria-pressed', String(light));
      themeButton.setAttribute('aria-label', light ? 'Switch to dark theme' : 'Switch to light theme');
      themeButton.title = themeButton.getAttribute('aria-label');
    };
    syncThemeButton();
    themeButton.addEventListener('click', () => {
      body.dataset.theme = body.dataset.theme === 'light' ? 'dark' : 'light';
      syncThemeButton();
      try { localStorage.setItem(themeKey, body.dataset.theme); } catch (_) { /* Keep theme active for this page view. */ }
    });
    header.insertBefore(themeButton, menuToggle || nav || null);
  }

  // Loading screen, with a skip control and a timeout failsafe.
  const loader = document.createElement('div');
  loader.className = 'loading-screen';
  loader.setAttribute('role', 'status');
  loader.setAttribute('aria-label', 'Loading portfolio');
  loader.innerHTML = `
    <div class="loading-content">
      <div class="loading-brand"><span class="loading-mark" aria-hidden="true">P</span><span>PERSONAL ARCHIVE / SYSTEM BOOT</span></div>
      <div class="loading-kicker">WELCOME TO THE OPERATIONS BASE</div>
      <div class="loading-title">INITIALIZING<span>.</span></div>
      <div class="loading-progress-meta"><span>LOADING PROFILE DATA</span><span class="loading-percent">00%</span></div>
      <div class="loading-track" aria-hidden="true"><span></span></div>
      <button class="loading-skip" type="button">SKIP INTRO ↗</button>
    </div>`;
  body.classList.add('is-loading');
  body.setAttribute('aria-busy', 'true');
  body.append(loader);
  const progressBar = loader.querySelector('.loading-track span');
  const percentLabel = loader.querySelector('.loading-percent');
  const startTime = performance.now();
  let progress = 0;
  let loadingFinished = false;
  const progressTimer = window.setInterval(() => {
    if (loadingFinished) return;
    progress = Math.min(93, progress + Math.max(1, Math.ceil((93 - progress) / 14)));
    progressBar.style.width = `${progress}%`;
    percentLabel.textContent = `${String(progress).padStart(2, '0')}%`;
  }, 45);
  function dismissLoader() {
    if (loadingFinished) return;
    loadingFinished = true;
    window.clearInterval(progressTimer);
    progressBar.style.width = '100%';
    percentLabel.textContent = '100%';
    window.setTimeout(() => {
      loader.classList.add('is-hidden');
      body.classList.remove('is-loading');
      body.removeAttribute('aria-busy');
      window.setTimeout(() => loader.remove(), 600);
    }, Math.max(0, 550 - (performance.now() - startTime)));
  }
  loader.querySelector('.loading-skip').addEventListener('click', dismissLoader);
  if (document.readyState === 'complete') dismissLoader();
  else window.addEventListener('load', dismissLoader, { once: true });
  window.setTimeout(dismissLoader, 2400);

  // Mobile navigation menu.
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      menuToggle.setAttribute('aria-expanded', String(open));
    });
  }

  const projectDetails = [...document.querySelectorAll('.project-detail-view')];
  const projectDetailBySlug = new Map(projectDetails.map(detail => [detail.dataset.projectDetail, detail]));
  if (projectDetailBySlug.has('volunteer')) {
    projectDetailBySlug.set('volunteer-website', projectDetailBySlug.get('volunteer'));
  }
  const projectGalleryControllers = new Map();
  const reduceProjectMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function markImageOrientation(image, container = image) {
    const applyOrientation = () => container.classList.toggle('is-portrait', image.naturalHeight > image.naturalWidth);
    if (image.complete) applyOrientation();
    else image.addEventListener('load', applyOrientation, { once: true });
  }

  document.querySelectorAll('[data-project-preview]').forEach(image => {
    markImageOrientation(image);
  });

  document.querySelectorAll('[data-project-gallery]').forEach(gallery => {
    const detail = gallery.closest('.project-detail-view');
    const viewport = gallery.querySelector('.project-gallery-viewport');
    const track = gallery.querySelector('.project-gallery-track');
    const slides = [...gallery.querySelectorAll('.project-gallery-slide')];
    if (!detail || !viewport || !track || slides.length < 2) return;

    const dots = [...gallery.querySelectorAll('[data-gallery-slide-to]')];
    const counter = gallery.querySelector('.project-gallery-count');
    slides.forEach(slide => {
      const image = slide.querySelector('img');
      if (image) markImageOrientation(image, slide);
    });
    let index = 0;
    let timer = 0;
    let active = false;
    let hovered = false;
    let focused = false;
    let swipeStart = null;

    function stopAutoplay() {
      if (timer) window.clearInterval(timer);
      timer = 0;
    }

    function startAutoplay() {
      if (!active || hovered || focused || document.hidden || reduceProjectMotion || timer) return;
      timer = window.setInterval(() => showSlide(index + 1), 5000);
    }

    function showSlide(nextIndex) {
      index = (nextIndex + slides.length) % slides.length;
      track.style.setProperty('--slide-index', String(index));
      slides.forEach((slide, slideIndex) => {
        const selected = slideIndex === index;
        slide.classList.toggle('is-active', selected);
        slide.setAttribute('aria-hidden', String(!selected));
      });
      dots.forEach((dot, dotIndex) => dot.setAttribute('aria-current', String(dotIndex === index)));
      if (counter) counter.textContent = `${String(index + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    }

    function moveTo(nextIndex) {
      showSlide(nextIndex);
      stopAutoplay();
      startAutoplay();
    }

    gallery.querySelector('[data-gallery-previous]')?.addEventListener('click', () => moveTo(index - 1));
    gallery.querySelector('[data-gallery-next]')?.addEventListener('click', () => moveTo(index + 1));
    dots.forEach(dot => dot.addEventListener('click', () => moveTo(Number(dot.dataset.gallerySlideTo))));
    viewport.addEventListener('keydown', event => {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        moveTo(index + (event.key === 'ArrowRight' ? 1 : -1));
      }
    });
    viewport.addEventListener('pointerdown', event => {
      if (event.pointerType === 'mouse') return;
      swipeStart = event.clientX;
      stopAutoplay();
    });
    viewport.addEventListener('pointerup', event => {
      if (swipeStart === null) return;
      const delta = event.clientX - swipeStart;
      swipeStart = null;
      if (Math.abs(delta) > 40) moveTo(index + (delta < 0 ? 1 : -1));
      else startAutoplay();
    });
    viewport.addEventListener('pointercancel', () => {
      swipeStart = null;
      startAutoplay();
    });
    gallery.addEventListener('mouseenter', () => {
      hovered = true;
      stopAutoplay();
    });
    gallery.addEventListener('mouseleave', () => {
      hovered = false;
      startAutoplay();
    });
    gallery.addEventListener('focusin', () => {
      focused = true;
      stopAutoplay();
    });
    gallery.addEventListener('focusout', event => {
      if (gallery.contains(event.relatedTarget)) return;
      focused = false;
      startAutoplay();
    });

    projectGalleryControllers.set(detail.dataset.projectDetail, {
      setActive(isActive) {
        active = isActive;
        if (active) startAutoplay();
        else stopAutoplay();
      },
      refresh() {
        if (document.hidden) stopAutoplay();
        else startAutoplay();
      },
    });
  });

  document.addEventListener('visibilitychange', () => {
    projectGalleryControllers.forEach(controller => controller.refresh());
  });

  // Tabbed portfolio navigation, including shareable project detail routes.
  const tabButtons = document.querySelectorAll('[data-tab]');
  const tabPanels = document.querySelectorAll('[data-tab-panel]');
  const tabPanelByName = new Map([...tabPanels].map(panel => [panel.dataset.tabPanel, panel]));
  const worksList = document.querySelector('.works-list-view');
  const routeStatus = document.querySelector('.project-route-status');
  let updateLanguageCarousel = () => {};

  function activateTab(name, options = {}) {
    const target = tabPanelByName.get(name);
    if (!target) return;
    const detail = options.projectSlug ? projectDetailBySlug.get(options.projectSlug) : null;
    const activeProjectSlug = name === 'works' && detail ? detail.dataset.projectDetail : null;
    tabPanels.forEach(panel => panel.classList.toggle('is-active', panel === target));
    tabButtons.forEach(button => {
      const active = button.dataset.tab === name;
      button.classList.toggle('active', active);
      if (button.classList.contains('tab-link')) button.setAttribute('aria-current', active ? 'page' : 'false');
    });
    if (worksList) worksList.hidden = Boolean(activeProjectSlug);
    projectDetails.forEach(projectDetail => {
      const active = projectDetail.dataset.projectDetail === activeProjectSlug;
      projectDetail.hidden = !active;
      const controller = projectGalleryControllers.get(projectDetail.dataset.projectDetail);
      if (controller) controller.setActive(active);
    });
    if (routeStatus) routeStatus.hidden = true;
    if (nav) nav.classList.remove('open');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    window.requestAnimationFrame(updateLanguageCarousel);
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const hash = activeProjectSlug ? `#works/${encodeURIComponent(activeProjectSlug)}` : `#${name}`;
    const historyMode = options.historyMode || 'replace';
    if (historyMode !== 'none' && location.hash !== hash) {
      history[historyMode === 'push' ? 'pushState' : 'replaceState'](null, '', hash);
    }
  }

  function activateCurrentRoute() {
    const [tabName, projectSlug] = location.hash.slice(1).split('/');
    if (tabName === 'works' && projectSlug) {
      if (projectDetailBySlug.has(projectSlug)) {
        activateTab('works', { projectSlug, historyMode: 'none' });
        return;
      }
      activateTab('works', { historyMode: 'none' });
      if (routeStatus) {
        routeStatus.textContent = 'Project record not found. Choose a project from the list below.';
        routeStatus.hidden = false;
      }
      return;
    }
    if (tabName && tabPanelByName.has(tabName)) {
      activateTab(tabName, { historyMode: 'none' });
      return;
    }
    activateTab('home', { historyMode: 'none' });
  }

  tabButtons.forEach(button => button.addEventListener('click', () => activateTab(button.dataset.tab)));
  document.querySelectorAll('[data-project-route]').forEach(link => {
    link.addEventListener('click', event => {
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      event.preventDefault();
      activateTab('works', { projectSlug: link.dataset.projectRoute, historyMode: 'push' });
    });
  });
  window.addEventListener('hashchange', activateCurrentRoute);
  window.addEventListener('popstate', activateCurrentRoute);
  activateCurrentRoute();

  // Filter project records without shifting the page into a carousel.
  const filters = [...document.querySelectorAll('.filter')];
  const cards = [...document.querySelectorAll('.project-card')];
  filters.forEach(button => button.addEventListener('click', () => {
    filters.forEach(filter => filter.classList.toggle('active', filter === button));
    const category = button.dataset.filter;
    cards.forEach(card => { card.hidden = category !== 'all' && card.dataset.category !== category; });
  }));

  const languageCarousel = document.querySelector('.language-carousel');
  if (languageCarousel) {
    const languageTrack = languageCarousel.querySelector('.language-track');
    const languageCards = [...languageTrack.querySelectorAll('.language-card')];
    const languageCount = document.querySelector('.language-count');
    let languageStep = 0;

    function getLanguageStep() {
      if (!languageStep && languageCards.length) {
        const cardWidth = languageCards[0].getBoundingClientRect().width;
        const gap = parseFloat(window.getComputedStyle(languageTrack).columnGap) || 0;
        languageStep = cardWidth + gap;
      }
      return languageStep;
    }

    function renderLanguageCarousel() {
      if (!languageCards.length) return;
      const step = getLanguageStep();
      const index = step ? Math.min(languageCards.length - 1, Math.floor((languageCarousel.scrollLeft + 1) / step)) : 0;
      if (languageCount) {
        languageCount.textContent = `${String(index + 1).padStart(2, '0')} / ${String(languageCards.length).padStart(2, '0')}`;
      }
    }

    function scrollLanguages(direction) {
      const step = getLanguageStep();
      if (!step) return;
      languageCarousel.scrollBy({
        left: direction * step,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
      });
    }

    const reduceMotion = reduceProjectMotion;
    const hoverDeadZone = 0.1;
    const maximumHoverSpeed = 420;
    let hoverDirection = 0;
    let hoverSpeed = 0;
    let hoverFrame = 0;
    let previousHoverFrame = 0;

    function stopHoverScrolling() {
      hoverDirection = 0;
      hoverSpeed = 0;
      previousHoverFrame = 0;
      if (hoverFrame) window.cancelAnimationFrame(hoverFrame);
      hoverFrame = 0;
    }

    function animateHoverScroll(timestamp) {
      if (!hoverDirection || !hoverSpeed) {
        hoverFrame = 0;
        return;
      }
      const elapsed = previousHoverFrame ? Math.min(timestamp - previousHoverFrame, 32) : 16;
      previousHoverFrame = timestamp;
      const maxScroll = languageCarousel.scrollWidth - languageCarousel.clientWidth;
      const nextScroll = languageCarousel.scrollLeft + hoverDirection * hoverSpeed * elapsed / 1000;
      if (nextScroll <= 0 || nextScroll >= maxScroll) {
        languageCarousel.scrollLeft = Math.max(0, Math.min(maxScroll, nextScroll));
        stopHoverScrolling();
        return;
      }
      languageCarousel.scrollLeft = nextScroll;
      hoverFrame = window.requestAnimationFrame(animateHoverScroll);
    }

    languageCarousel.addEventListener('pointermove', event => {
      if (event.pointerType !== 'mouse' || reduceMotion) return;
      const bounds = languageCarousel.getBoundingClientRect();
      if (!bounds.width) {
        stopHoverScrolling();
        return;
      }
      const pointerX = event.clientX - bounds.left;
      const position = (pointerX / bounds.width - 0.5) * 2;
      const offset = Math.abs(position);
      if (offset <= hoverDeadZone) {
        stopHoverScrolling();
        return;
      }
      hoverDirection = Math.sign(position);
      const intensity = (offset - hoverDeadZone) / (1 - hoverDeadZone);
      hoverSpeed = maximumHoverSpeed * intensity * intensity;
      if (!hoverFrame) hoverFrame = window.requestAnimationFrame(animateHoverScroll);
    });
    languageCarousel.addEventListener('pointerleave', stopHoverScrolling);
    languageCarousel.addEventListener('scroll', renderLanguageCarousel, { passive: true });
    languageCarousel.addEventListener('keydown', event => {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        scrollLanguages(event.key === 'ArrowRight' ? 1 : -1);
      }
    });
    window.addEventListener('resize', () => {
      languageStep = 0;
      renderLanguageCarousel();
    });
    updateLanguageCarousel = renderLanguageCarousel;
    window.requestAnimationFrame(renderLanguageCarousel);
  }

  // Small pointer and scroll parallax on the hero. Disabled for touch/reduced motion.
  const hero = document.querySelector('.hero');
  const heroGrid = hero && hero.querySelector('.hero-grid');
  const heroSide = hero && hero.querySelector('.hero-side');
  const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (hero && heroGrid && !motionQuery.matches && window.matchMedia('(pointer: fine)').matches) {
    hero.classList.add('has-parallax');
    let x = 0;
    let y = 0;
    let frame = null;
    const update = () => {
      frame = null;
      const scrollRatio = Math.min(window.scrollY, window.innerHeight) / Math.max(window.innerHeight, 1);
      heroGrid.style.setProperty('--parallax-x', `${x * 8}px`);
      heroGrid.style.setProperty('--parallax-y', `${y * 7 - scrollRatio * 12}px`);
      if (heroSide) heroSide.style.setProperty('--parallax-y', `${y * -5 + scrollRatio * 18}px`);
    };
    const queueUpdate = () => { if (frame === null) frame = requestAnimationFrame(update); };
    hero.addEventListener('pointermove', event => {
      const rect = hero.getBoundingClientRect();
      x = (event.clientX - rect.left) / rect.width - 0.5;
      y = (event.clientY - rect.top) / rect.height - 0.5;
      queueUpdate();
    }, { passive: true });
    hero.addEventListener('pointerleave', () => { x = y = 0; queueUpdate(); });
    window.addEventListener('scroll', queueUpdate, { passive: true });
  }
})();
