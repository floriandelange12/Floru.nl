/**
 * Floru Main JavaScript
 * Scroll animations, stats counter, smooth scrolling
 *
 * @package Astra-Child-Floru
 */

(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var languageScrollRestoreKey = 'floruLanguageScrollRestore';

    function getLanguageScrollRestorePageKey(urlValue) {
        try {
            var url = new URL(urlValue, window.location.origin);
            url.searchParams.delete('lang');
            return url.pathname + url.search;
        } catch (error) {
            return window.location.pathname + window.location.search;
        }
    }

    function readLanguageScrollRestoreState() {
        try {
            var rawValue = window.sessionStorage.getItem(languageScrollRestoreKey);
            if (!rawValue) return null;

            var state = JSON.parse(rawValue);
            if (!state || typeof state !== 'object') return null;

            return state;
        } catch (error) {
            return null;
        }
    }

    function clearLanguageScrollRestoreState() {
        try {
            window.sessionStorage.removeItem(languageScrollRestoreKey);
        } catch (error) {
            // Ignore storage failures and fall back to default browser behavior.
        }
    }

    function initLanguageSwitchScrollPersistence() {
        document.addEventListener('click', function (event) {
            var link = event.target.closest('.floru-footer__language-link');
            if (!link) return;

            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            try {
                window.sessionStorage.setItem(
                    languageScrollRestoreKey,
                    JSON.stringify({
                        page: getLanguageScrollRestorePageKey(link.href),
                        x: window.scrollX || 0,
                        y: window.scrollY || 0,
                        createdAt: Date.now(),
                    })
                );
            } catch (error) {
                // Ignore storage failures and keep the existing language switch behavior.
            }
        });
    }

    function initLanguageSwitchScrollRestore() {
        var state = readLanguageScrollRestoreState();
        if (!state) return;

        var currentPage = getLanguageScrollRestorePageKey(window.location.href);
        var isFreshState = typeof state.createdAt === 'number' && Date.now() - state.createdAt < 120000;
        var targetX = typeof state.x === 'number' ? state.x : 0;
        var targetY = typeof state.y === 'number' ? state.y : 0;

        if (!isFreshState || state.page !== currentPage) {
            clearLanguageScrollRestoreState();
            return;
        }

        clearLanguageScrollRestoreState();

        if ('scrollRestoration' in window.history) {
            window.history.scrollRestoration = 'manual';
        }

        var restoreScrollPosition = function () {
            window.scrollTo(targetX, targetY);
        };

        restoreScrollPosition();
        window.requestAnimationFrame(restoreScrollPosition);
        window.setTimeout(restoreScrollPosition, 80);
        window.setTimeout(restoreScrollPosition, 220);
        window.addEventListener('load', restoreScrollPosition, { once: true });

        window.setTimeout(function () {
            if ('scrollRestoration' in window.history) {
                window.history.scrollRestoration = 'auto';
            }
        }, 300);
    }

    /* ======================================================================
       SCROLL-TRIGGERED ANIMATIONS (Intersection Observer)
       ====================================================================== */

    function initScrollAnimations() {
        var elements = document.querySelectorAll('[data-animate]');
        if (!elements.length) return;

        if (prefersReducedMotion) {
            elements.forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0,
                rootMargin: '0px 0px -60px 0px',
            }
        );

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ======================================================================
       STAGGERED CHILDREN
       ====================================================================== */

    function initStaggerChildren() {
        var containers = document.querySelectorAll('[data-animate-stagger]');
        containers.forEach(function (container) {
            var children = container.children;
            for (var i = 0; i < children.length; i++) {
                children[i].style.setProperty('--stagger-index', i);
            }
        });
    }

    /* ======================================================================
       STATS COUNTER ANIMATION
       ====================================================================== */

    function animateCounter(el, target, suffix) {
        if (prefersReducedMotion) {
            el.textContent = target + suffix;
            return;
        }

        var duration = 2000;
        var startTime = null;

        function easeOutQuart(t) {
            return 1 - Math.pow(1 - t, 4);
        }

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var easedProgress = easeOutQuart(progress);
            var current = Math.round(easedProgress * target);
            el.textContent = current + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function initStatsCounter() {
        var statNumbers = document.querySelectorAll('.floru-stats-band__number');
        if (!statNumbers.length) return;

        var observed = false;

        function triggerCounters() {
            if (observed) return;
            observed = true;

            statNumbers.forEach(function (el) {
                var text = el.textContent.trim();
                var match = text.match(/^(\d+)(.*)$/);
                if (match) {
                    var target = parseInt(match[1], 10);
                    var suffix = match[2] || '';
                    el.textContent = '0' + suffix;
                    animateCounter(el, target, suffix);
                }
            });
        }

        if (prefersReducedMotion) return;

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        triggerCounters();
                        observer.disconnect();
                    }
                });
            },
            { threshold: 0.3 }
        );

        var band = document.querySelector('.floru-stats-band');
        if (band) {
            observer.observe(band);
        }
    }

    /* ======================================================================
       SMOOTH SCROLLING FOR ANCHOR LINKS
       ====================================================================== */

    function initSmoothScroll() {
        if (prefersReducedMotion) return;

        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href^="#"]');
            if (!link) return;

            var hash = link.getAttribute('href');
            if (hash === '#' || hash.length < 2) return;

            var target = document.querySelector(hash);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    /* ======================================================================
       CONTACT FORM — prevent double submit
       ====================================================================== */

    function initContactFormGuard() {
        var form = document.querySelector('form.floru-form');
        if (!form) return;

        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (!btn || btn.disabled) return;

            // Capture original label so we can restore on validation failure (browser keeps the page).
            var labelEl = btn.querySelector('span.floru-btn__label');
            var original = btn.innerHTML;

            // Native validation: if the form is invalid, the browser cancels submit.
            // We listen for invalid as a signal to NOT lock the button.
            requestAnimationFrame(function () {
                if (!form.checkValidity || form.checkValidity()) {
                    btn.disabled = true;
                    btn.setAttribute('aria-busy', 'true');
                    btn.innerHTML = '<span>Sending...</span>';
                }
            });

            // Safety net: if for any reason the page does not navigate within 8s, restore.
            setTimeout(function () {
                if (btn.disabled && document.body.contains(btn)) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.innerHTML = original;
                    if (labelEl) { /* noop, kept for ref */ }
                }
            }, 8000);
        });
    }

    /* ======================================================================
       INITIALIZE
       ====================================================================== */

    document.addEventListener('DOMContentLoaded', function () {
        initLanguageSwitchScrollRestore();
        initLanguageSwitchScrollPersistence();
        initStaggerChildren();
        initScrollAnimations();
        initStatsCounter();
        initSmoothScroll();
        initContactFormGuard();
    });
})();
