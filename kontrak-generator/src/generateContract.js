/**
 * generateContract.js
 *
 * PDF overlay generator untuk Perjanjian Sewa Menyewa Kendaraan.
 *
 * PRINSIP:
 *   - PDF template asli (assets/template-kontrak.pdf) digunakan sebagai dasar.
 *   - Seluruh teks statis, tipografi, pemenggalan baris, dan nomor halaman
 *     tetap berasal dari template — tidak pernah disentuh.
 *   - Hanya placeholder bertitik yang ditimpa dengan data dinamis melalui
 *     overlay koordinat tetap (white box + drawText).
 *   - Tidak ada re-layout, reflow, atau pembuatan ulang konten.
 *
 * PENGGUNAAN CLI:
 *   node src/generateContract.js '<json>' <output-path>
 *
 * PENGGUNAAN SEBAGAI MODULE:
 *   import { generateContract } from './src/generateContract.js';
 *   await generateContract(data, '/path/to/output.pdf');
 */

import { PDFDocument, rgb, StandardFonts } from 'pdf-lib';
import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'fs';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';
import { ContractSchema } from './schema.js';
import { OVERLAYS } from './coordinates.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname  = dirname(__filename);

const TEMPLATE_PATH = resolve(__dirname, '../assets/template-kontrak.pdf');

// ─────────────────────────────────────────────────────────────────────────────
// Font map
// pdf-lib standard fonts sedekat mungkin dengan font asli dokumen.
// Arial  → Helvetica  (metrik sedikit berbeda, tapi cukup untuk overlay singkat)
// Times  → Times-Roman
// ─────────────────────────────────────────────────────────────────────────────
const FONT_KEYS = {
  arial:     StandardFonts.Helvetica,
  arialBold: StandardFonts.HelveticaBold,
  times:     StandardFonts.TimesRoman,
  timesBold: StandardFonts.TimesRomanBold,
};

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Ambil nilai dari data menggunakan dot-path.
 * Mendukung array index, e.g. "secondPartyNoticeLines.0"
 * @param {object} data
 * @param {string} key
 * @returns {string|null}
 */
function resolveKey(data, key) {
  if (!key) return null;
  return key.split('.').reduce((obj, k) => {
    if (obj == null) return null;
    return obj[k] ?? null;
  }, data);
}

/**
 * Potong teks jika terlalu panjang untuk lebar kotak.
 * Kurangi font size maksimal 0.5pt; jika masih tidak muat, potong dengan "…".
 * @param {string} text
 * @param {object} font - embedded pdf-lib font
 * @param {number} size - font size
 * @param {number} maxWidth - lebar maksimal (pt)
 * @returns {{ text: string, size: number }}
 */
function fitText(text, font, size, maxWidth) {
  if (!maxWidth) return { text, size };

  let w = font.widthOfTextAtSize(text, size);
  if (w <= maxWidth) return { text, size };

  // Kurangi size maksimal 0.5pt
  const minSize = size - 0.5;
  const wAtMin  = font.widthOfTextAtSize(text, minSize);
  if (wAtMin <= maxWidth) return { text, size: minSize };

  // Potong karakter dari kanan
  let truncated = text;
  while (truncated.length > 1) {
    truncated = truncated.slice(0, -1);
    if (font.widthOfTextAtSize(truncated + '\u2026', size) <= maxWidth) {
      return { text: truncated + '\u2026', size };
    }
  }
  return { text: truncated, size };
}

// ─────────────────────────────────────────────────────────────────────────────
// Core generator
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Generate contract PDF dengan overlay data dinamis di atas template.
 *
 * @param {object} rawData   - data kontrak (akan divalidasi dengan Zod)
 * @param {string} outputPath - path lengkap untuk menyimpan PDF output
 * @returns {Promise<string>} outputPath jika berhasil
 * @throws {Error} jika validasi gagal atau template tidak ditemukan
 */
export async function generateContract(rawData, outputPath) {
  // ── 1. Validasi input ────────────────────────────────────────────
  const data = ContractSchema.parse(rawData);

  // ── 2. Load template ─────────────────────────────────────────────
  if (!existsSync(TEMPLATE_PATH)) {
    throw new Error(
      `Template tidak ditemukan: ${TEMPLATE_PATH}\n` +
      `Salin file asli ke kontrak-generator/assets/template-kontrak.pdf`
    );
  }
  const templateBytes = readFileSync(TEMPLATE_PATH);
  const pdfDoc = await PDFDocument.load(templateBytes, {
    ignoreEncryption: true,
    updateMetadata: false,
  });

  // ── 3. Embed fonts ───────────────────────────────────────────────
  const fontMap = {};
  for (const [key, stdFont] of Object.entries(FONT_KEYS)) {
    fontMap[key] = await pdfDoc.embedFont(stdFont);
  }

  // ── 4. Get pages ─────────────────────────────────────────────────
  const pages = pdfDoc.getPages();

  // ── 5. Apply overlays ────────────────────────────────────────────
  for (const [pageIdxStr, overlayList] of Object.entries(OVERLAYS)) {
    const pageIdx = parseInt(pageIdxStr, 10);
    const page    = pages[pageIdx];
    if (!page) {
      console.warn(`[warn] page index ${pageIdx} tidak ada (template punya ${pages.length} halaman)`);
      continue;
    }

    for (const overlay of overlayList) {
      // Resolve value
      let value;
      if (typeof overlay.valueFn === 'function') {
        value = overlay.valueFn(data);
      } else {
        value = resolveKey(data, overlay.dataKey);
      }

      // Skip jika kosong (jangan timpa placeholder dengan string kosong)
      if (value == null || value === '') continue;

      const textStr = String(value);

      // ── 5a. Tutup placeholder lama dengan kotak putih ──────────
      if (overlay.whiteBox) {
        const { x, y, width, height } = overlay.whiteBox;
        page.drawRectangle({
          x, y, width, height,
          color: rgb(1, 1, 1),
          borderWidth: 0,
          opacity: 1,
        });
      }

      // ── 5b. Gambar teks baru ───────────────────────────────────
      const { x, y, font: fontKey, size, align, centerAnchorX, maxWidth } = overlay.text;
      const font = fontMap[fontKey] ?? fontMap.arial;

      // Fit text ke maxWidth jika ada
      const fitted = fitText(textStr, font, size, maxWidth);

      let drawX = x;
      if (align === 'center' && maxWidth != null && centerAnchorX != null) {
        const textWidth = font.widthOfTextAtSize(fitted.text, fitted.size);
        drawX = centerAnchorX + (maxWidth - textWidth) / 2;
      }

      page.drawText(fitted.text, {
        x: drawX,
        y,
        size: fitted.size,
        font,
        color: rgb(0, 0, 0),
        lineHeight: 12.6,
        opacity: 1,
      });
    }
  }

  // ── 6. Simpan output ─────────────────────────────────────────────
  const outputDir = dirname(outputPath);
  if (!existsSync(outputDir)) {
    mkdirSync(outputDir, { recursive: true });
  }

  const pdfBytes = await pdfDoc.save({
    useObjectStreams: false, // kompatibilitas lebih baik
  });
  writeFileSync(outputPath, pdfBytes);

  return outputPath;
}

// ─────────────────────────────────────────────────────────────────────────────
// ─────────────────────────────────────────────────────────────────────────────
// CLI entry point
//
// Mode 1 — inline JSON:
//   node src/generateContract.js '<json-string>' <output-path>
//
// Mode 2 — file JSON (diawali "@", menghindari masalah escapeshellarg Windows):
//   node src/generateContract.js @/path/to/data.json <output-path>
// ─────────────────────────────────────────────────────────────────────────────

const isCLI = process.argv[1] &&
  resolve(process.argv[1]) === resolve(__filename);

if (isCLI) {
  const jsonArg   = process.argv[2];
  const outputArg = process.argv[3];

  if (!jsonArg || !outputArg) {
    console.error(
      'Usage:\n' +
      "  node src/generateContract.js '<json-data>' <output-path>\n" +
      '  node src/generateContract.js @/path/to/data.json <output-path>'
    );
    process.exit(1);
  }

  let rawData;
  try {
    if (jsonArg.startsWith('@')) {
      // Mode file: "@/path/to/data.json"
      const filePath = jsonArg.slice(1);
      const { readFileSync } = await import('fs');
      rawData = JSON.parse(readFileSync(filePath, 'utf8'));
    } else {
      // Mode inline JSON
      rawData = JSON.parse(jsonArg);
    }
  } catch (e) {
    console.error('ERROR:JSON_PARSE:' + e.message);
    process.exit(1);
  }

  generateContract(rawData, outputArg)
    .then((p) => {
      console.log('OK:' + p);
      process.exit(0);
    })
    .catch((e) => {
      const msg = e.errors
        ? 'VALIDATION:' + JSON.stringify(e.errors)
        : 'ERROR:' + e.message;
      console.error(msg);
      process.exit(1);
    });
}
