/* ==========================================================================
   Élémentium Studio — interactions du site public
   Dépendances (CDN) : GSAP + ScrollTrigger, Lenis. La scène 3D du hero est dans hero3d.js.
   ========================================================================== */
(() => {
    'use strict';

    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
    const root = document.documentElement;
    const csrf = $('meta[name="csrf-token"]').content;
    // Adresse racine du site : fonctionne aussi dans un sous-dossier (ex. XAMPP).
    const base = $('meta[name="base-url"]').content.replace(/\/$/, '');
    const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = matchMedia('(hover: hover) and (pointer: fine)').matches;

    // Sans GSAP (CDN bloqué), on affiche simplement tout le contenu.
    if (!window.gsap || !window.ScrollTrigger) {
        root.classList.remove('js');
        $('.preloader')?.remove();
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    /* ------------------------------------------------------------ Défilement fluide */
    let lenis = null;
    if (!reduceMotion && window.Lenis) {
        lenis = new Lenis({ duration: 1.15, smoothWheel: true });
        lenis.on('scroll', ScrollTrigger.update);
        gsap.ticker.add((t) => lenis.raf(t * 1000));
        gsap.ticker.lagSmoothing(0);
        lenis.stop(); // repris après le préchargement
    }

    const scrollToTarget = (target) => {
        const offset = target === '#hero' ? 0 : -70;
        if (lenis) lenis.scrollTo(target, { offset, duration: 1.4 });
        else $(target)?.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
    };

    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href^="#"]');
        if (!link || link.getAttribute('href').length < 2) return;
        const target = link.getAttribute('href');
        if (!$(target)) return;
        e.preventDefault();
        closeMenu();
        scrollToTarget(target);
    });

    const lockScroll = (locked) => {
        document.body.classList.toggle('is-locked', locked);
        if (lenis) locked ? lenis.stop() : lenis.start();
    };

    /* ------------------------------------------------------------ Découpage du texte */
    function splitChars(el) {
        const text = el.textContent;
        el.textContent = '';
        el.setAttribute('aria-label', text);
        [...text].forEach((ch) => {
            const span = document.createElement('span');
            span.className = 'char';
            span.setAttribute('aria-hidden', 'true');
            span.textContent = ch === ' ' ? ' ' : ch;
            el.appendChild(span);
        });
        return $$('.char', el);
    }

    // Enveloppe chaque mot dans <span class="rl"><span>mot</span></span> pour une révélation « par le bas ».
    function splitWords(el) {
        const walk = (node, gradient) => {
            [...node.childNodes].forEach((child) => {
                if (child.nodeType === Node.TEXT_NODE) {
                    const frag = document.createDocumentFragment();
                    child.textContent.split(/(\s+)/).forEach((part) => {
                        if (!part) return;
                        if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
                        const outer = document.createElement('span');
                        const inner = document.createElement('span');
                        outer.className = 'rl';
                        if (gradient) inner.className = 'grad';
                        inner.textContent = part;
                        outer.appendChild(inner);
                        frag.appendChild(outer);
                    });
                    child.replaceWith(frag);
                } else if (child.nodeType === Node.ELEMENT_NODE && child.tagName !== 'BR') {
                    const isGrad = child.classList.contains('grad');
                    if (isGrad) child.classList.remove('grad'); // le dégradé passe sur chaque mot
                    walk(child, gradient || isGrad);
                }
            });
        };
        walk(el, false);
        return $$('.rl > span', el);
    }

    /* ------------------------------------------------------------ Préchargement + intro */
    const heroChars = $$('.hero__title .split').map(splitChars);

    function runIntro() {
        lenis?.start();
        const tl = gsap.timeline({ defaults: { ease: 'expo.out' } });
        heroChars.forEach((chars, i) => {
            tl.from(chars, { yPercent: 115, rotate: 8, duration: 1.4, stagger: 0.04 }, i * 0.15);
        });
        tl.from('.hero__eyebrow, .hero__lead, .hero__cta > *', { y: 30, opacity: 0, duration: 1.2, stagger: 0.08 }, 0.5)
          .from('.hero__side a, .hero__elements span, .hero__scroll', { opacity: 0, y: 20, duration: 1, stagger: 0.05 }, 0.9)
          .from('.nav', { y: -40, opacity: 0, duration: 1.2, clearProps: 'transform,opacity' }, 0.6);
        window.dispatchEvent(new CustomEvent('el:intro'));
    }

    (function preloader() {
        const el = $('.preloader');
        if (!el) return runIntro();
        const counter = $('.preloader__count span', el);
        const state = { v: 0 };
        let loaded = document.readyState === 'complete';
        window.addEventListener('load', () => { loaded = true; });

        gsap.to(state, {
            v: 90, duration: 1.4, ease: 'power2.out',
            onUpdate: () => { counter.textContent = Math.round(state.v); },
            onComplete: function finish() {
                const done = () => gsap.to(state, {
                    v: 100, duration: 0.4,
                    onUpdate: () => { counter.textContent = Math.round(state.v); },
                    onComplete: () => {
                        el.classList.add('is-done');
                        setTimeout(() => el.remove(), 1100);
                        runIntro();
                    },
                });
                // On n'attend pas indéfiniment une ressource lente (carte, vidéos…).
                if (loaded) done();
                else {
                    const fallback = setTimeout(done, 2500);
                    window.addEventListener('load', () => { clearTimeout(fallback); done(); }, { once: true });
                }
            },
        });
    })();

    /* ------------------------------------------------------------ Texte tapé */
    (function typed() {
        const el = $('.typed');
        if (!el) return;
        const words = el.dataset.words.split('|');
        let w = 0, i = words[0].length, deleting = true;
        const tick = () => {
            const word = words[w];
            i += deleting ? -1 : 1;
            el.textContent = word.slice(0, i);
            let delay = deleting ? 35 : 70;
            if (!deleting && i === word.length) { deleting = true; delay = 2200; }
            else if (deleting && i === 0) { deleting = false; w = (w + 1) % words.length; delay = 300; }
            setTimeout(tick, delay);
        };
        setTimeout(tick, 3500);
    })();

    /* ------------------------------------------------------------ Révélations au défilement */
    $$('.reveal-lines').forEach((el) => {
        const words = splitWords(el);
        gsap.fromTo(words, { yPercent: 105 }, {
            yPercent: 0, duration: 1.1, ease: 'expo.out', stagger: 0.035,
            scrollTrigger: { trigger: el, start: 'top 85%' },
        });
    });

    ScrollTrigger.batch('.reveal', {
        start: 'top 88%',
        onEnter: (batch) => gsap.to(batch, { opacity: 1, y: 0, duration: 1.1, ease: 'expo.out', stagger: 0.08, overwrite: true }),
    });

    // Compteurs
    $$('[data-count]').forEach((el) => {
        const end = +el.dataset.count;
        const start = el.hasAttribute('data-plain') ? end - 30 : 0;
        const obj = { v: start };
        ScrollTrigger.create({
            trigger: el, start: 'top 90%', once: true,
            onEnter: () => gsap.to(obj, { v: end, duration: 2, ease: 'power3.out', onUpdate: () => { el.textContent = Math.round(obj.v); } }),
        });
    });

    /* ------------------------------------------------------------ Hero : parallaxe de sortie */
    if (!reduceMotion) {
        gsap.to('.hero__content', {
            yPercent: 30, opacity: 0, ease: 'none',
            scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true },
        });
        gsap.to('.hero__bg', {
            yPercent: 20, scale: 1.1, ease: 'none',
            scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true },
        });
    }

    /* ------------------------------------------------------------ Bandeau : inclinaison selon la vitesse */
    if (lenis) {
        const skew = gsap.quickTo('.marquee__track', 'skewX', { duration: 0.5, ease: 'power3' });
        lenis.on('scroll', ({ velocity }) => skew(gsap.utils.clamp(-12, 12, velocity * -0.4)));
    }

    /* ------------------------------------------------------------ À propos */
    if (!reduceMotion) {
        gsap.from('.about__frame', {
            clipPath: 'inset(30% 30% 30% 30% round 40px)', scale: 1.1, duration: 1.6, ease: 'expo.out',
            scrollTrigger: { trigger: '.about__visual', start: 'top 80%' },
        });
        gsap.to('.about__img', {
            yPercent: -6, ease: 'none',
            scrollTrigger: { trigger: '.about', start: 'top bottom', end: 'bottom top', scrub: true },
        });
    }

    /* ------------------------------------------------------------ Univers : défilement horizontal en 3D */
    const mm = gsap.matchMedia();
    mm.add('(min-width: 901px) and (prefers-reduced-motion: no-preference)', () => {
        const track = $('.elements__track');
        const panels = $$('.element-panel');
        if (!track || panels.length < 2) return;

        const horizontal = gsap.to(track, {
            x: () => -(track.scrollWidth - window.innerWidth),
            ease: 'none',
            scrollTrigger: {
                trigger: '.elements__pin', pin: true, scrub: 1, start: 'top top',
                end: () => '+=' + (track.scrollWidth - window.innerWidth),
                invalidateOnRefresh: true,
            },
        });

        panels.forEach((panel) => {
            const inner = $('.element-panel__inner', panel);
            const img = $('.element-panel__media img', panel);
            const giant = $('.element-panel__giant', panel);
            const st = { trigger: panel, containerAnimation: horizontal, scrub: true };

            gsap.fromTo(inner,
                { rotateY: -28, rotateX: 6, z: -260, opacity: 0.35 },
                { rotateY: 0, rotateX: 0, z: 0, opacity: 1, ease: 'power2.out', scrollTrigger: { ...st, start: 'left right', end: 'center center' } });
            gsap.to(inner,
                { rotateY: 24, z: -200, opacity: 0.4, ease: 'power2.in', immediateRender: false, scrollTrigger: { ...st, start: 'center center', end: 'right left' } });
            gsap.fromTo(img, { xPercent: -10 }, { xPercent: 10, ease: 'none', scrollTrigger: { ...st, start: 'left right', end: 'right left' } });
            gsap.fromTo(giant, { xPercent: -30 }, { xPercent: -70, ease: 'none', scrollTrigger: { ...st, start: 'left right', end: 'right left' } });
        });
    });
    mm.add('(max-width: 900px)', () => {
        $$('.element-panel__inner').forEach((el) => gsap.from(el, {
            y: 60, opacity: 0, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 85%' },
        }));
    });

    /* ------------------------------------------------------------ Cartes : entrées en 3D */
    if (!reduceMotion) {
        [['.services__grid', '.service-card'], ['.team__grid', '.member']].forEach(([grid, card]) => {
            gsap.from(card, {
                y: 90, rotateX: -35, opacity: 0, transformOrigin: '50% 100%', duration: 1.3, ease: 'expo.out', stagger: 0.1,
                scrollTrigger: { trigger: grid, start: 'top 82%' },
            });
        });
        ScrollTrigger.batch('.work, .video-card', {
            start: 'top 92%',
            onEnter: (batch) => gsap.fromTo(batch,
                { clipPath: 'inset(100% 0% 0% 0% round 22px)', y: 60 },
                { clipPath: 'inset(0% 0% 0% 0% round 22px)', y: 0, duration: 1.3, ease: 'expo.out', stagger: 0.08, clearProps: 'clipPath,transform' }),
        });
        // CTA : mots qui montent, halo qui grossit
        const ctaWords = $$('.cta__title .split-words').flatMap((el) => {
            el.innerHTML = el.textContent.split(' ').map((w) => `<span class="word">${w}</span>`).join(' ');
            return $$('.word', el);
        });
        gsap.from([...ctaWords, '.cta__title > .grad'], {
            y: 100, opacity: 0, rotate: 4, duration: 1.3, ease: 'expo.out', stagger: 0.07,
            scrollTrigger: { trigger: '.cta', start: 'top 70%' },
        });
        gsap.fromTo('.cta__glow', { scale: 0.5 }, { scale: 1.2, ease: 'none', scrollTrigger: { trigger: '.cta', scrub: true } });
        gsap.from('.cta__button', { scale: 0, rotate: -90, duration: 1.4, ease: 'elastic.out(1, .6)', scrollTrigger: { trigger: '.cta__button', start: 'top 90%' } });
        gsap.from('.footer__giant', { yPercent: 60, opacity: 0, ease: 'none', scrollTrigger: { trigger: '.footer', start: 'top bottom', end: 'bottom bottom', scrub: true } });
    }

    /* ------------------------------------------------------------ Navigation */
    const nav = $('.nav');
    const toTop = $('.to-top');
    const progress = $('.scroll-progress');
    let lastY = 0;
    ScrollTrigger.create({
        start: 0, end: 'max',
        onUpdate: (self) => {
            const y = self.scroll();
            nav.classList.toggle('is-scrolled', y > 40);
            nav.classList.toggle('is-hidden', y > 400 && y > lastY && !document.body.classList.contains('menu-open'));
            toTop.classList.toggle('is-visible', y > window.innerHeight);
            progress.style.transform = `scaleX(${self.progress})`;
            lastY = y;
        },
    });
    $$('.nav__links a').forEach((link) => {
        const section = $(link.getAttribute('href'));
        if (!section) return;
        ScrollTrigger.create({
            trigger: section, start: 'top 50%', end: 'bottom 50%',
            onToggle: ({ isActive }) => link.classList.toggle('is-active', isActive),
        });
    });

    const burger = $('.burger');
    function closeMenu() {
        if (!document.body.classList.contains('menu-open')) return;
        document.body.classList.remove('menu-open');
        burger.setAttribute('aria-expanded', 'false');
        $('.menu').setAttribute('aria-hidden', 'true');
        lockScroll(false);
    }
    burger.addEventListener('click', () => {
        const open = !document.body.classList.contains('menu-open');
        if (!open) return closeMenu();
        document.body.classList.add('menu-open');
        burger.setAttribute('aria-expanded', 'true');
        $('.menu').setAttribute('aria-hidden', 'false');
        lockScroll(true);
    });

    /* ------------------------------------------------------------ Thème clair / sombre */
    const themeBtn = $('.theme-toggle');
    const syncThemeIcon = () => {
        const dark = root.dataset.theme !== 'light';
        themeBtn.innerHTML = `<i class="bi ${dark ? 'bi-sun' : 'bi-moon-stars'}"></i>`;
        $('meta[name="theme-color"]').content = dark ? '#060b10' : '#f6f1e7';
    };
    syncThemeIcon();
    themeBtn.addEventListener('click', () => {
        root.dataset.theme = root.dataset.theme === 'light' ? 'dark' : 'light';
        try { localStorage.setItem('el-theme', root.dataset.theme); } catch (e) { /* stockage indisponible */ }
        syncThemeIcon();
        window.dispatchEvent(new CustomEvent('el:theme', { detail: root.dataset.theme }));
    });

    /* ------------------------------------------------------------ Curseur, inclinaison 3D, boutons magnétiques */
    if (finePointer && !reduceMotion) {
        root.classList.add('has-cursor');
        const cursor = $('.cursor');
        const label = $('.cursor__label');
        const dotX = gsap.quickTo('.cursor__dot', 'x', { duration: 0.1 });
        const dotY = gsap.quickTo('.cursor__dot', 'y', { duration: 0.1 });
        const ringX = gsap.quickTo('.cursor__ring', 'x', { duration: 0.45, ease: 'power3' });
        const ringY = gsap.quickTo('.cursor__ring', 'y', { duration: 0.45, ease: 'power3' });
        window.addEventListener('pointermove', (e) => { cursor.classList.add('is-active'); dotX(e.clientX); dotY(e.clientY); ringX(e.clientX); ringY(e.clientY); });
        document.addEventListener('pointerover', (e) => {
            const withLabel = e.target.closest('[data-cursor]');
            const interactive = e.target.closest('a, button, input, select, textarea, label');
            cursor.classList.toggle('is-label', !!withLabel);
            cursor.classList.toggle('is-hover', !withLabel && !!interactive);
            label.textContent = withLabel ? withLabel.dataset.cursor : '';
        });

        $$('[data-tilt]').forEach((card) => {
            const max = +card.dataset.tilt || 8;
            const rx = gsap.quickTo(card, 'rotateX', { duration: 0.6, ease: 'power3' });
            const ry = gsap.quickTo(card, 'rotateY', { duration: 0.6, ease: 'power3' });
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                const px = (e.clientX - r.left) / r.width;
                const py = (e.clientY - r.top) / r.height;
                ry((px - 0.5) * max * 2);
                rx((0.5 - py) * max * 2);
                card.style.setProperty('--gx', `${px * 100}%`);
                card.style.setProperty('--gy', `${py * 100}%`);
            });
            card.addEventListener('pointerleave', () => { rx(0); ry(0); });
        });

        $$('.magnetic').forEach((btn) => {
            const mx = gsap.quickTo(btn, 'x', { duration: 0.6, ease: 'elastic.out(1, .4)' });
            const my = gsap.quickTo(btn, 'y', { duration: 0.6, ease: 'elastic.out(1, .4)' });
            btn.addEventListener('pointermove', (e) => {
                const r = btn.getBoundingClientRect();
                mx((e.clientX - r.left - r.width / 2) * 0.35);
                my((e.clientY - r.top - r.height / 2) * 0.35);
            });
            btn.addEventListener('pointerleave', () => { mx(0); my(0); });
        });
    }

    /* ------------------------------------------------------------ Filtres (portfolio + vidéos) */
    $$('[data-filters]').forEach((group) => {
        const section = group.closest('section');
        const items = $$('[data-category]', section);
        group.addEventListener('click', (e) => {
            const btn = e.target.closest('.filter');
            if (!btn || btn.classList.contains('is-active')) return;
            $$('.filter', group).forEach((b) => b.classList.toggle('is-active', b === btn));
            const value = btn.dataset.filter;
            gsap.to(items, {
                opacity: 0, scale: 0.94, duration: 0.25, ease: 'power2.in',
                onComplete: () => {
                    items.forEach((it) => it.classList.toggle('is-hidden', value !== '*' && it.dataset.category !== value));
                    const visible = items.filter((it) => !it.classList.contains('is-hidden'));
                    gsap.fromTo(visible, { opacity: 0, scale: 0.94, y: 30 }, { opacity: 1, scale: 1, y: 0, duration: 0.7, ease: 'expo.out', stagger: 0.05, clearProps: 'transform' });
                    ScrollTrigger.refresh();
                },
            });
        });
    });

    /* ------------------------------------------------------------ Modales */
    let lastFocus = null;
    function openModal(modal) {
        lastFocus = document.activeElement;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        lockScroll(true);
        setTimeout(() => $('.modal__close', modal)?.focus(), 50);
    }
    function closeModal(modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        lockScroll(false);
        if (modal.id === 'player') setTimeout(() => { $('.player__screen', modal).innerHTML = ''; }, 300);
        lastFocus?.focus?.();
    }
    $$('.modal').forEach((modal) => {
        modal.addEventListener('click', (e) => {
            if (e.target.closest('[data-close]') || e.target === modal) closeModal(modal);
        });
    });
    document.addEventListener('keydown', (e) => {
        const open = $('.modal.is-open');
        if (e.key === 'Escape') { if (open) closeModal(open); else closeMenu(); }
        if (open?.id === 'lightbox' && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) showWork(current + (e.key === 'ArrowRight' ? 1 : -1));
    });
    $$('[data-open]').forEach((btn) => btn.addEventListener('click', () => openModal($('#' + btn.dataset.open))));

    /* ------------------------------------------------------------ Visionneuse du portfolio */
    const lightbox = $('#lightbox');
    let current = 0;
    const visibleWorks = () => $$('.work').filter((w) => !w.classList.contains('is-hidden'));
    function showWork(index) {
        const works = visibleWorks();
        if (!works.length) return;
        current = (index + works.length) % works.length;
        const w = works[current].dataset;
        const img = $('img', lightbox);
        gsap.fromTo(img, { opacity: 0, scale: 0.97 }, { opacity: 1, scale: 1, duration: 0.5, ease: 'expo.out' });
        img.src = w.src;
        img.alt = w.title;
        $('h3', lightbox).textContent = w.title;
        $('figcaption p', lightbox).textContent = w.text || '';
        const link = $('figcaption a', lightbox);
        link.hidden = !w.link;
        if (w.link) link.href = w.link;
    }
    $$('.work__open').forEach((btn) => btn.addEventListener('click', () => {
        showWork(visibleWorks().indexOf(btn.closest('.work')));
        openModal(lightbox);
    }));
    $('.lightbox__nav--prev').addEventListener('click', () => showWork(current - 1));
    $('.lightbox__nav--next').addEventListener('click', () => showWork(current + 1));

    /* ------------------------------------------------------------ Notifications */
    const toastEl = $('.toast');
    let toastTimer;
    function toast(message) {
        toastEl.textContent = message;
        toastEl.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), 3500);
    }

    const api = (url, options = {}) => fetch(url, {
        ...options,
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) },
    }).then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw Object.assign(new Error(data.message || 'Erreur'), { status: res.status, data });
        return data;
    });

    /* ------------------------------------------------------------ Lecteur vidéo */
    const player = $('#player');
    let playing = null;

    function syncLike(id, liked, likes) {
        $$(`[data-like="${id}"]`).forEach((b) => {
            b.classList.toggle('is-liked', liked);
            $('[data-likes]', b).textContent = likes;
        });
        if (playing?.id === id) {
            $('.player__like', player).classList.toggle('is-liked', liked);
            $('.player__likes', player).textContent = likes;
        }
    }

    async function playVideo(id) {
        const screen = $('.player__screen', player);
        screen.innerHTML = '';
        openModal(player);
        try {
            const { video, liked } = await api(`${base}/videos/${id}`);
            playing = video;
            if (video.embed_url) {
                screen.innerHTML = `<iframe src="${video.embed_url}" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" allowfullscreen title=""></iframe>`;
                $('iframe', screen).title = video.title;
            } else {
                const v = document.createElement('video');
                v.src = video.source_url;
                v.controls = true;
                v.autoplay = true;
                v.playsInline = true;
                if (video.thumbnail) v.poster = video.thumbnail;
                screen.appendChild(v);
            }
            $('.player__title', player).textContent = video.title;
            $('.player__desc', player).textContent = video.description || '';
            $('.player__category', player).textContent = video.category_label;
            $('.player__views', player).textContent = video.views;
            $$(`[data-video="${id}"] [data-views]`).forEach((el) => { el.textContent = video.views; });
            syncLike(video.id, liked, video.likes);
        } catch (err) {
            closeModal(player);
            toast('Impossible de charger cette vidéo.');
        }
    }

    async function like(id, btn) {
        btn?.classList.remove('pop');
        void btn?.offsetWidth;
        btn?.classList.add('pop');
        try {
            const data = await api(`${base}/videos/${id}/like`, { method: 'POST' });
            syncLike(+id, data.liked, data.likes);
        } catch (err) {
            toast(err.status === 429 ? 'Doucement ! Réessayez dans un instant.' : 'Action impossible pour le moment.');
        }
    }

    async function share(id, title) {
        const url = `${location.origin}${location.pathname}#videos`;
        try {
            if (navigator.share) await navigator.share({ title, text: `${title} — Élémentium Studio`, url });
            else { await navigator.clipboard.writeText(url); toast('Lien copié dans le presse-papiers !'); }
        } catch (e) { /* partage annulé */ }
    }

    $('.videos')?.addEventListener('click', (e) => {
        const likeBtn = e.target.closest('[data-like]');
        const shareBtn = e.target.closest('[data-share]');
        const thumb = e.target.closest('.video-card__thumb');
        if (likeBtn) like(likeBtn.dataset.like, likeBtn);
        else if (shareBtn) share(shareBtn.dataset.share, shareBtn.dataset.title);
        else if (thumb) playVideo(thumb.closest('[data-video]').dataset.video);
    });
    $('.player__like', player).addEventListener('click', (e) => playing && like(playing.id, e.currentTarget));
    $('.player__share', player).addEventListener('click', () => playing && share(playing.id, playing.title));

    /* ------------------------------------------------------------ Formulaires (contact, newsletter, proposition de vidéo) */
    function clearErrors(form) {
        $$('.has-error', form).forEach((f) => f.classList.remove('has-error'));
        $$('.field__error', form).forEach((e) => e.remove());
    }
    function showErrors(form, errors) {
        Object.entries(errors || {}).forEach(([name, messages]) => {
            const input = form.elements[name];
            const field = input?.closest?.('.field, .dropzone');
            if (!field) return;
            field.classList.add('has-error');
            const msg = document.createElement('small');
            msg.className = 'field__error';
            msg.textContent = messages[0];
            field.appendChild(msg);
        });
    }
    function setStatus(form, text, ok) {
        const status = $('.form-status', form);
        status.textContent = text;
        status.className = `form-status ${ok ? 'is-success' : 'is-error'}`;
    }

    // Envoi via XHR pour suivre la progression des gros fichiers vidéo.
    function send(form, onProgress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.upload.onprogress = (e) => e.lengthComputable && onProgress?.(e.loaded / e.total);
            xhr.onload = () => {
                let data = {};
                try { data = JSON.parse(xhr.responseText); } catch (e) { /* réponse non JSON */ }
                xhr.status < 300 ? resolve(data) : reject(Object.assign(new Error(data.message), { status: xhr.status, data }));
            };
            xhr.onerror = () => reject(Object.assign(new Error('Réseau'), { status: 0, data: {} }));
            xhr.send(new FormData(form));
        });
    }

    $$('.ajax-form').forEach((form) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors(form);
            const btn = $('button[type="submit"]', form);
            const bar = $('.progress', form);
            btn.disabled = true;
            if (bar) { bar.hidden = false; $('span', bar).style.width = '0'; }
            try {
                const data = await send(form, (p) => bar && ($('span', bar).style.width = `${p * 100}%`));
                setStatus(form, data.message || 'Merci !', true);
                form.reset();
                $$('.dropzone__name', form).forEach((n) => { n.textContent = n.dataset.default || n.textContent; });
                if (form.closest('.modal')) setTimeout(() => { closeModal(form.closest('.modal')); setStatus(form, '', true); toast(data.message); }, 1600);
            } catch (err) {
                if (err.status === 422) {
                    showErrors(form, err.data.errors);
                    setStatus(form, 'Merci de vérifier les champs indiqués.', false);
                } else if (err.status === 413) {
                    setStatus(form, 'Fichier trop volumineux pour le serveur.', false);
                } else if (err.status === 429) {
                    setStatus(form, 'Trop d’envois rapprochés, réessayez dans quelques minutes.', false);
                } else {
                    setStatus(form, 'Une erreur est survenue. Veuillez réessayer.', false);
                }
            } finally {
                btn.disabled = false;
                if (bar) setTimeout(() => { bar.hidden = true; }, 600);
            }
        });
    });

    // Onglets « lien / fichier » et zone de dépôt
    const submitModal = $('#submit-modal');
    if (submitModal) {
        $$('.tab', submitModal).forEach((tab) => tab.addEventListener('click', () => {
            $$('.tab', submitModal).forEach((t) => t.classList.toggle('is-active', t === tab));
            $$('.tab-panel', submitModal).forEach((p) => {
                const active = p.dataset.panel === tab.dataset.tab;
                p.classList.toggle('is-active', active);
                // On n'envoie que le champ de l'onglet choisi.
                $$('input', p).forEach((input) => { input.disabled = !active; });
            });
        }));
        $$('.tab-panel:not(.is-active) input', submitModal).forEach((i) => { i.disabled = true; });

        const zone = $('.dropzone', submitModal);
        const input = $('input[type="file"]', zone);
        const name = $('.dropzone__name', zone);
        name.dataset.default = name.textContent;
        input.addEventListener('change', () => {
            const f = input.files[0];
            name.textContent = f ? `${f.name} — ${(f.size / 1048576).toFixed(1)} Mo` : name.dataset.default;
        });
        ['dragenter', 'dragover'].forEach((ev) => zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.add('is-over'); }));
        ['dragleave', 'drop'].forEach((ev) => zone.addEventListener(ev, () => zone.classList.remove('is-over')));
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change')); }
        });
    }

    // Recalcule les positions une fois les images chargées.
    window.addEventListener('load', () => ScrollTrigger.refresh());
})();
