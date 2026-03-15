/**
 * Floru Main JavaScript
 * Scroll animations, stats counter, smooth scrolling
 *
 * @package Astra-Child-Floru
 */

(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
       INITIALIZE
       ====================================================================== */

    document.addEventListener('DOMContentLoaded', function () {
        initStaggerChildren();
        initScrollAnimations();
        initStatsCounter();
        initSmoothScroll();
    });
})();
