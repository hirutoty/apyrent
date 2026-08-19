{{-- ══════════════════════════════════════════════════════════════════════════
     GLOBAL IMAGE SLIDESHOW MODAL
     Cara pakai dari halaman mana pun:
       openSlideshow(images, startIndex)
     
     Contoh:
       // Array string URL
       openSlideshow(['/storage/bukti1.jpg', '/storage/bukti2.jpg'], 0)
       
       // Array object {path, name}
       openSlideshow([{path: '/storage/bukti1.jpg', name: 'bukti1.jpg'}], 0)
       
       // Campuran
       openSlideshow(['/storage/img.jpg', {path: '/storage/img2.jpg', name: 'foto'}], 0)
══════════════════════════════════════════════════════════════════════════ --}}

{{-- Modal Overlay --}}
<div id="slideshowModal"
     class="fixed inset-0 z-[9999] flex items-center justify-center"
     style="display:none !important;"
     role="dialog"
     aria-modal="true"
     aria-label="Slideshow gambar">

    {{-- Backdrop --}}
    <div id="slideshowBackdrop"
         class="absolute inset-0 bg-black/90 backdrop-blur-sm"
         onclick="closeSlideshow()"></div>

    {{-- Modal Container --}}
    <div class="relative z-10 flex flex-col items-center justify-center w-full h-full px-4 py-6 select-none">

        {{-- ── TOP BAR ── --}}
        <div class="absolute top-0 left-0 right-0 flex items-center justify-between px-4 py-3 z-20">
            {{-- Counter --}}
            <div class="flex items-center gap-2 bg-black/40 rounded-xl px-3 py-1.5">
                <i class="bi bi-images text-white/70 text-sm"></i>
                <span id="slideshowCounter" class="text-white text-sm font-semibold tabular-nums">1 / 1</span>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Open in new tab --}}
                <a id="slideshowOpenLink"
                   href="#"
                   target="_blank"
                   rel="noopener"
                   class="flex items-center gap-1.5 bg-black/40 hover:bg-white/20 text-white/80 hover:text-white rounded-xl px-3 py-1.5 text-xs font-medium transition-all"
                   title="Buka di tab baru">
                    <i class="bi bi-box-arrow-up-right text-sm"></i>
                    <span class="hidden sm:inline">Buka</span>
                </a>
                {{-- Close --}}
                <button onclick="closeSlideshow()"
                        class="flex items-center justify-center w-9 h-9 bg-black/40 hover:bg-red-500/80 text-white/80 hover:text-white rounded-xl transition-all"
                        title="Tutup (ESC)">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>
        </div>

        {{-- ── IMAGE AREA ── --}}
        <div class="relative flex items-center justify-center w-full"
             style="max-height: calc(100vh - 140px); height: calc(100vh - 140px);">

            {{-- Prev Button --}}
            <button id="slideshowPrev"
                    onclick="slideshowNav(-1)"
                    class="absolute left-0 sm:left-4 z-10 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12
                           bg-black/50 hover:bg-white/20 text-white rounded-xl
                           transition-all duration-200 hover:scale-105
                           disabled:opacity-20 disabled:cursor-not-allowed"
                    style="display:none;">
                <i class="bi bi-chevron-left text-xl font-bold"></i>
            </button>

            {{-- Image Wrapper --}}
            <div class="flex items-center justify-center w-full h-full px-12 sm:px-16"
                 id="slideshowImgWrap">
                <img id="slideshowImg"
                     src=""
                     alt=""
                     class="max-w-full max-h-full object-contain rounded-lg shadow-2xl transition-opacity duration-200"
                     style="max-height: calc(100vh - 140px);"
                     draggable="false"
                     loading="lazy">

                {{-- Loading spinner --}}
                <div id="slideshowSpinner"
                     class="absolute inset-0 flex items-center justify-center"
                     style="display:none !important;">
                    <div class="w-10 h-10 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </div>
            </div>

            {{-- Next Button --}}
            <button id="slideshowNext"
                    onclick="slideshowNav(1)"
                    class="absolute right-0 sm:right-4 z-10 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12
                           bg-black/50 hover:bg-white/20 text-white rounded-xl
                           transition-all duration-200 hover:scale-105
                           disabled:opacity-20 disabled:cursor-not-allowed"
                    style="display:none;">
                <i class="bi bi-chevron-right text-xl font-bold"></i>
            </button>
        </div>

        {{-- ── BOTTOM BAR ── --}}
        <div class="absolute bottom-0 left-0 right-0 flex flex-col items-center gap-2 px-4 py-3 z-20">
            {{-- Filename --}}
            <p id="slideshowFilename"
               class="text-white/70 text-xs truncate max-w-xs sm:max-w-md text-center"></p>

            {{-- Dot Indicators --}}
            <div id="slideshowDots" class="flex items-center gap-1.5"></div>
        </div>
    </div>
</div>

<style>
    #slideshowModal {
        /* Override inline style="display:none" saat aktif */
    }
    #slideshowModal.ss-open {
        display: flex !important;
    }
    /* Prevent body scroll saat modal terbuka */
    body.ss-modal-open {
        overflow: hidden !important;
    }
    /* Animasi masuk */
    @keyframes ss-fade-in {
        from { opacity: 0; transform: scale(0.96); }
        to   { opacity: 1; transform: scale(1); }
    }
    #slideshowModal.ss-open > div:not(#slideshowBackdrop) {
        animation: ss-fade-in 200ms ease forwards;
    }
    /* Transisi gambar */
    #slideshowImg.ss-fade {
        opacity: 0 !important;
    }
</style>

<script>
(function () {
    'use strict';

    /* ── State ── */
    var _images  = [];   // array of {path, name}
    var _current = 0;
    var _touchStartX = 0;
    var _touchStartY = 0;

    /* ── Normalise image item ke {path, name} ── */
    function normalise(item) {
        if (typeof item === 'string') {
            return { path: item, name: item.split('/').pop().split('?')[0] };
        }
        return {
            path: item.path || item.url || item.src || '',
            name: item.name || item.filename || (item.path || '').split('/').pop().split('?')[0]
        };
    }

    /* ── Open ── */
    window.openSlideshow = function (images, startIndex) {
        if (!images || !images.length) return;

        _images  = images.map(normalise).filter(function (i) { return !!i.path; });
        _current = Math.max(0, Math.min(parseInt(startIndex, 10) || 0, _images.length - 1));

        if (!_images.length) return;

        var modal = document.getElementById('slideshowModal');
        modal.classList.add('ss-open');
        document.body.classList.add('ss-modal-open');

        buildDots();
        renderSlide(_current, false);
    };

    /* ── Close ── */
    window.closeSlideshow = function () {
        var modal = document.getElementById('slideshowModal');
        modal.classList.remove('ss-open');
        document.body.classList.remove('ss-modal-open');
        _images  = [];
        _current = 0;
    };

    /* ── Navigate ── */
    window.slideshowNav = function (dir) {
        var next = _current + dir;
        if (next < 0) next = _images.length - 1;
        if (next >= _images.length) next = 0;
        renderSlide(next, true);
    };

    /* ── Go to index (dots) ── */
    window.slideshowGoTo = function (idx) {
        renderSlide(idx, true);
    };

    /* ── Render slide ── */
    function renderSlide(idx, animate) {
        _current = idx;
        var item = _images[idx];

        var img      = document.getElementById('slideshowImg');
        var counter  = document.getElementById('slideshowCounter');
        var fname    = document.getElementById('slideshowFilename');
        var openLink = document.getElementById('slideshowOpenLink');
        var prevBtn  = document.getElementById('slideshowPrev');
        var nextBtn  = document.getElementById('slideshowNext');
        var spinner  = document.getElementById('slideshowSpinner');

        /* Update counter & filename */
        counter.textContent  = (idx + 1) + ' / ' + _images.length;
        fname.textContent    = item.name || '';
        openLink.href        = item.path;

        /* Show/hide nav buttons */
        if (_images.length > 1) {
            prevBtn.style.display = '';
            nextBtn.style.display = '';
        } else {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }

        /* Update dots */
        updateDots(idx);

        /* Load image with fade transition */
        if (animate) {
            img.classList.add('ss-fade');
        }

        /* Show spinner */
        spinner.style.removeProperty('display');

        var newImg = new Image();
        newImg.onload = function () {
            spinner.style.setProperty('display', 'none', 'important');
            img.src = item.path;
            img.alt = item.name || '';
            if (animate) {
                setTimeout(function () { img.classList.remove('ss-fade'); }, 20);
            }
        };
        newImg.onerror = function () {
            spinner.style.setProperty('display', 'none', 'important');
            img.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="150" viewBox="0 0 200 150"><rect width="200" height="150" fill="%23374151"/><text x="100" y="70" fill="%236b7280" font-family="sans-serif" font-size="14" text-anchor="middle">Gagal memuat gambar</text><text x="100" y="92" fill="%236b7280" font-family="sans-serif" font-size="11" text-anchor="middle">' + (item.name || '') + '</text></svg>';
            img.alt = 'Gagal memuat: ' + (item.name || '');
            if (animate) {
                setTimeout(function () { img.classList.remove('ss-fade'); }, 20);
            }
        };
        newImg.src = item.path;

        /* Jika sudah cached, langsung tampil */
        if (newImg.complete && newImg.naturalWidth) {
            spinner.style.setProperty('display', 'none', 'important');
            img.src = item.path;
            img.alt = item.name || '';
            if (animate) {
                setTimeout(function () { img.classList.remove('ss-fade'); }, 20);
            }
        }
    }

    /* ── Build dot indicators ── */
    function buildDots() {
        var wrap = document.getElementById('slideshowDots');
        wrap.innerHTML = '';
        if (_images.length <= 1) return;

        _images.forEach(function (_, i) {
            var dot = document.createElement('button');
            dot.className = 'w-2 h-2 rounded-full transition-all duration-200 ' +
                            (i === _current ? 'bg-white scale-125' : 'bg-white/40 hover:bg-white/70');
            dot.setAttribute('aria-label', 'Gambar ' + (i + 1));
            dot.onclick = function () { window.slideshowGoTo(i); };
            wrap.appendChild(dot);
        });
    }

    /* ── Update dots active state ── */
    function updateDots(idx) {
        var dots = document.querySelectorAll('#slideshowDots button');
        dots.forEach(function (dot, i) {
            if (i === idx) {
                dot.className = 'w-2 h-2 rounded-full transition-all duration-200 bg-white scale-125';
            } else {
                dot.className = 'w-2 h-2 rounded-full transition-all duration-200 bg-white/40 hover:bg-white/70';
            }
        });
    }

    /* ── Keyboard navigation ── */
    document.addEventListener('keydown', function (e) {
        if (!document.getElementById('slideshowModal').classList.contains('ss-open')) return;
        if (e.key === 'ArrowLeft')  { e.preventDefault(); window.slideshowNav(-1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); window.slideshowNav(1); }
        if (e.key === 'Escape')     { e.preventDefault(); window.closeSlideshow(); }
    });

    /* ── Touch/swipe support ── */
    var imgWrap = null;
    document.addEventListener('DOMContentLoaded', function () {
        imgWrap = document.getElementById('slideshowImgWrap');
        if (!imgWrap) return;

        imgWrap.addEventListener('touchstart', function (e) {
            _touchStartX = e.touches[0].clientX;
            _touchStartY = e.touches[0].clientY;
        }, { passive: true });

        imgWrap.addEventListener('touchend', function (e) {
            if (!document.getElementById('slideshowModal').classList.contains('ss-open')) return;
            var dx = e.changedTouches[0].clientX - _touchStartX;
            var dy = e.changedTouches[0].clientY - _touchStartY;
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
                window.slideshowNav(dx < 0 ? 1 : -1);
            }
        }, { passive: true });
    });

})();
</script>
