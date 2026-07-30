/**
 * visualDiff.js
 *
 * Pixel-diff antara template asli dan output yang sudah di-overlay.
 * Area overlay (zona data dinamis) di-exclude dari penghitungan diff.
 *
 * Kebutuhan: ghostscript di PATH untuk rasterisasi PDF ke PNG.
 *
 * Usage:
 *   node scripts/visualDiff.js [template.pdf] [output.pdf] [dpi]
 *
 * Default:
 *   template = assets/template-kontrak.pdf
 *   output   = output/sample-output.pdf
 *   dpi      = 200
 *
 * Exit code:
 *   0 — semua halaman di luar zona overlay identik (diff = 0%)
 *   1 — ada perbedaan pixel di luar zona overlay
 *   2 — error (file tidak ditemukan, ghostscript tidak tersedia, dll.)
 */

import { execSync }             from 'child_process';
import { existsSync, mkdirSync,
         readdirSync, readFileSync,
         rmSync }               from 'fs';
import { resolve, dirname, join } from 'path';
import { fileURLToPath }         from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT      = resolve(__dirname, '..');

// ── Argumen CLI ──────────────────────────────────────────────────────────────
const templatePdf = resolve(ROOT, process.argv[2] ?? 'assets/template-kontrak.pdf');
const outputPdf   = resolve(ROOT, process.argv[3] ?? 'output/sample-output.pdf');
const DPI         = parseInt(process.argv[4] ?? '200', 10);

// ── Zona overlay per halaman yang di-exclude dari diff ──────────────────────
// Format: { page: number (1-based), x, y, w, h } dalam satuan pt (595×841.68)
// Dikonversi ke pixel saat runtime berdasarkan DPI.
const PT_PER_INCH = 72;
const EXCLUDE_ZONES = [
  // Halaman 1 — nomor kontrak, hari/tanggal, nama pihak kedua
  { page: 1, x: 130,    y:  73,  w: 335, h: 20  },  // nomor kontrak
  { page: 1, x:  86,    y:  98,  w: 420, h: 40  },  // hari + tanggal (2 baris)
  { page: 1, x:  86,    y: 310,  w: 430, h: 80  },  // identitas pihak kedua

  // Halaman 2 — masa sewa
  { page: 2, x:  86,    y:  88,  w: 430, h: 26  },  // masa sewa kedua kolom

  // Halaman 7 — blok alamat + ttd pihak kedua
  { page: 7, x:  81,    y:  54,  w: 430, h: 80  },  // blok alamat
  { page: 7, x:  81,    y: 508,  w: 430, h: 26  },  // tanggal ttd
  { page: 7, x: 298,    y: 635,  w: 212, h: 26  },  // nama penanda tangan
];

// ─────────────────────────────────────────────────────────────────────────────

function ptToPx(pt) {
  return Math.round((pt / PT_PER_INCH) * DPI);
}

function rasterize(pdfPath, outDir, label) {
  if (!existsSync(pdfPath)) {
    console.error(`[error] File tidak ditemukan: ${pdfPath}`);
    process.exit(2);
  }
  mkdirSync(outDir, { recursive: true });

  // Coba ghostscript
  const gsCmd = `gs -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pngmono -r${DPI} -sOutputFile="${outDir}/${label}_%02d.png" "${pdfPath}"`;
  try {
    execSync(gsCmd, { stdio: 'pipe' });
  } catch {
    // Fallback: ghostscript mungkin dipanggil 'gswin64c' di Windows
    try {
      const gsWinCmd = gsCmd.replace(/^gs /, 'gswin64c ');
      execSync(gsWinCmd, { stdio: 'pipe' });
    } catch (e2) {
      console.error('[error] Ghostscript tidak tersedia. Install dari https://www.ghostscript.com/');
      console.error(e2.message);
      process.exit(2);
    }
  }

  const files = readdirSync(outDir)
    .filter(f => f.startsWith(label + '_') && f.endsWith('.png'))
    .sort();
  return files.map(f => join(outDir, f));
}

function loadPng(path) {
  // Dynamic import pixelmatch & pngjs
  return import('pngjs').then(({ PNG }) => {
    return new Promise((res, rej) => {
      const png = new PNG();
      png.parse(readFileSync(path), (err, data) => {
        if (err) rej(err); else res(data);
      });
    });
  });
}

async function diffPage(templatePng, outputPng, pageNumber) {
  const { default: pixelmatch } = await import('pixelmatch');

  const [tmpl, out] = await Promise.all([
    loadPng(templatePng),
    loadPng(outputPng),
  ]);

  const { width, height } = tmpl;
  const diff = new (await import('pngjs')).PNG({ width, height });

  // Buat mask: pixel yang termasuk exclude zone → warna putih di kedua image
  const zones = EXCLUDE_ZONES.filter(z => z.page === pageNumber);
  for (const zone of zones) {
    const x0 = ptToPx(zone.x);
    const y0 = ptToPx(zone.y);
    const x1 = Math.min(width,  x0 + ptToPx(zone.w));
    const y1 = Math.min(height, y0 + ptToPx(zone.h));
    for (let py = y0; py < y1; py++) {
      for (let px = x0; px < x1; px++) {
        const idx = (py * width + px) << 2;
        // Putihkan di template
        tmpl.data[idx] = tmpl.data[idx + 1] = tmpl.data[idx + 2] = 255;
        tmpl.data[idx + 3] = 255;
        // Putihkan di output
        out.data[idx] = out.data[idx + 1] = out.data[idx + 2] = 255;
        out.data[idx + 3] = 255;
      }
    }
  }

  const numDiff = pixelmatch(
    tmpl.data, out.data, diff.data,
    width, height,
    { threshold: 0.05, includeAA: false }
  );

  const pct = ((numDiff / (width * height)) * 100).toFixed(4);
  return { numDiff, pct, width, height };
}

async function main() {
  console.log(`\nVisual Diff @ ${DPI} DPI`);
  console.log(`  Template : ${templatePdf}`);
  console.log(`  Output   : ${outputPdf}\n`);

  const tmpDir = resolve(ROOT, '.diff-tmp');
  mkdirSync(tmpDir, { recursive: true });

  const tmplPngs = rasterize(templatePdf, join(tmpDir, 'tmpl'), 'tmpl');
  const outPngs  = rasterize(outputPdf,   join(tmpDir, 'out'),  'out');

  if (tmplPngs.length !== outPngs.length) {
    console.error(`[error] Jumlah halaman berbeda: template=${tmplPngs.length}, output=${outPngs.length}`);
    process.exit(1);
  }

  let totalDiff = 0;
  for (let i = 0; i < tmplPngs.length; i++) {
    const pageNum = i + 1;
    const result  = await diffPage(tmplPngs[i], outPngs[i], pageNum);
    const status  = result.numDiff === 0 ? '✓ OK' : `✗ DIFF ${result.numDiff}px`;
    console.log(`  Hal. ${pageNum}: ${status}  (${result.pct}% dari ${result.width}×${result.height}px)`);
    totalDiff += result.numDiff;
  }

  // Cleanup
  rmSync(tmpDir, { recursive: true, force: true });

  console.log(`\nTotal pixel diff (di luar zona overlay): ${totalDiff}`);
  if (totalDiff > 0) {
    console.error('[FAIL] Ada perbedaan di luar zona overlay. Periksa koordinat atau kualitas render.');
    process.exit(1);
  } else {
    console.log('[PASS] Tidak ada perbedaan pixel di luar zona overlay.\n');
    process.exit(0);
  }
}

main().catch(e => {
  console.error('[error]', e.message);
  process.exit(2);
});
