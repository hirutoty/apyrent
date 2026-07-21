/**
 * ============================================================
 *  FORMAT RUPIAH — Global Currency Formatter
 * ============================================================
 *  Cara pakai:
 *  1. Tambahkan class "format-rupiah" pada input
 *  2. Ubah type="number" menjadi type="text" inputmode="numeric"
 *
 *  Contoh:
 *  <input type="text" inputmode="numeric" name="biaya" class="format-rupiah ...">
 *
 *  Otomatis:
 *  - Saat user mengetik  → tampil 100.000
 *  - Saat form disubmit  → kirim 100000 (angka murni)
 *  - Saat page load       → format nilai yang sudah ada
 * ============================================================
 */

(function () {
    'use strict';

    // ── Helper: angka → "1.000.000" ──
    function formatRupiah(value) {
        var num = parseRupiah(value);
        if (isNaN(num) || num === 0) return '';
        var isNeg = num < 0;
        var abs = Math.abs(Math.round(num));
        var str = abs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return (isNeg ? '-' : '') + str;
    }

    // ── Helper: "1.000.000" → 1000000 ──
    function parseRupiah(value) {
        if (typeof value === 'number') return value;
        if (!value) return 0;
        var str = value.toString().trim();

        // Hapus prefix Rp dan spasi
        str = str.replace(/^-?\s*Rp\s*/i, '');

        // Format murni dari DB: "9000000" atau "9000000.00" (titik = desimal, bukan ribuan)
        if (/^-?\d+(\.\d{1,2})?$/.test(str)) {
            return Math.round(parseFloat(str)) || 0;
        }

        // Format ribuan Indonesia: "9.000.000" atau "9.000.000,50"
        // Hapus titik pemisah ribuan, ganti koma desimal jadi titik
        var cleaned = str.replace(/\./g, '').replace(',', '.').replace(/[^\d.\-]/g, '');
        var result = parseFloat(cleaned);
        return isNaN(result) ? 0 : Math.round(result);
    }

    // ── Format semua input .format-rupiah saat DOM ready ──
    function formatAllOnLoad() {
        var inputs = document.querySelectorAll('.format-rupiah');
        for (var i = 0; i < inputs.length; i++) {
            var val = inputs[i].value;
            if (val && val !== '' && val !== '0') {
                inputs[i].value = formatRupiah(val);
            }
        }
    }

    // ── Event: format saat mengetik ──
    document.addEventListener('input', function (e) {
        if (!e.target.classList || !e.target.classList.contains('format-rupiah')) return;

        var input = e.target;
        var raw = parseRupiah(input.value);

        // Simpan posisi kursor relatif dari kanan
        var caretFromEnd = input.value.length - (input.selectionStart || 0);

        if (raw === 0) {
            input.value = '';
        } else {
            input.value = formatRupiah(raw);
        }

        // Restore posisi kursor
        var newPos = input.value.length - caretFromEnd;
        if (newPos < 0) newPos = 0;
        try {
            input.setSelectionRange(newPos, newPos);
        } catch (ex) {
            // Beberapa browser tidak support setSelectionRange pada type tertentu
        }
    });

    // ── Event: strip format sebelum submit ──
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.querySelectorAll) return;

        var inputs = form.querySelectorAll('.format-rupiah');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = parseRupiah(inputs[i].value);
        }
    });

    // ── Event: format saat fokus keluar (blur) untuk rapikan tampilan ──
    document.addEventListener('blur', function (e) {
        if (!e.target.classList || !e.target.classList.contains('format-rupiah')) return;
        var raw = parseRupiah(e.target.value);
        if (raw === 0) {
            e.target.value = '';
        } else {
            e.target.value = formatRupiah(raw);
        }
    }, true);

    // ── Event: select all saat fokus untuk kemudahan edit ──
    document.addEventListener('focus', function (e) {
        if (!e.target.classList || !e.target.classList.contains('format-rupiah')) return;
        // Timeout kecil supaya select berjalan setelah browser selesai set focus
        setTimeout(function () {
            try {
                e.target.select();
            } catch (ex) { }
        }, 50);
    }, true);

    // ── Init ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', formatAllOnLoad);
    } else {
        formatAllOnLoad();
    }

    // Expose ke global scope (untuk dipanggil manual jika perlu)
    window.formatRupiah = formatRupiah;
    window.parseRupiah = parseRupiah;

})();
