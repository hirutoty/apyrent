/**
 * coordinates.js
 *
 * Semua koordinat overlay data dinamis di atas PDF template.
 *
 * SISTEM KOORDINAT:
 *   pdf-lib menggunakan origin kiri-BAWAH (PDF standard).
 *   Spesifikasi dokumen menggunakan origin kiri-ATAS (screen/CSS).
 *
 *   Konversi:  pdfY = PAGE_HEIGHT - topY - textHeight
 *   PAGE_HEIGHT = 841.68 pt  (A4 exact dari file asli)
 *   lineHeight Arial 11.04pt ≈ 12.6 pt
 *
 * STRUKTUR SETIAP ENTRY:
 *   id        : identifier unik (untuk debugging)
 *   dataKey   : dot-path ke field di ContractData, e.g. "secondPartyName"
 *               atau "secondPartyNoticeLines.0" untuk array
 *   valueFn   : (opsional) function(data) => string, jika value perlu
 *               dikombinasikan dari beberapa field
 *   whiteBox  : { x, y, width, height } kotak putih penutup placeholder
 *               (koordinat pdf-lib, origin kiri-bawah)
 *   text      : { x, y, font, size, align?, centerAnchorX?, maxWidth? }
 *               align 'center' butuh centerAnchorX + maxWidth
 *
 * FONT KEYS (sesuai fontMap di generateContract.js):
 *   'arial'      → Helvetica (closest standard substitute)
 *   'arialBold'  → Helvetica-Bold
 *   'times'      → Times-Roman
 *   'timesBold'  → Times-Bold
 */

const PH = 841.68; // PAGE_HEIGHT
const LH = 12.6;   // lineHeight untuk Arial 11.04pt

/**
 * Helper: konversi topY (origin atas) ke pdfY (origin bawah).
 * @param {number} topY  - jarak dari atas halaman (pt)
 * @param {number} [lh]  - tinggi teks (pt), default LH
 */
const py = (topY, lh = LH) => PH - topY - lh;

// ─────────────────────────────────────────────────────────────────────────────
// OVERLAYS
// Key = page index (0-based)
// ─────────────────────────────────────────────────────────────────────────────

export const OVERLAYS = {

  /* ══════════════════════════════════════════════════
     HALAMAN 1  (page index 0)
     Judul, identitas para pihak, pengantar, Pasal 1
  ══════════════════════════════════════════════════ */
  0: [

    // ── Nomor kontrak (center, Times Bold 9.96pt) ──────────────────
    {
      id: 'contractNumber',
      dataKey: 'contractNumber',
      whiteBox: { x: 130, y: py(86.41, 12), width: 335, height: 13 },
      text: {
        font: 'timesBold', size: 9.96,
        // center di antara x=130 dan x=465 → anchor x=297.72, maxWidth=335
        align: 'center', centerAnchorX: 130, maxWidth: 335,
        y: py(86.41, 12),
      },
    },

    // ── Hari Indonesia ("………….," di baris pembuka) ─────────────────
    {
      id: 'contractDayId',
      dataKey: 'contractDay',
      whiteBox: { x: 108, y: py(111.61), width: 90, height: LH },
      text: { x: 108, y: py(111.61), font: 'arial', size: 11.04 },
    },

    // ── Hari Inggris ──────────────────────────────────────────────
    {
      id: 'contractDayEn',
      dataKey: 'contractDayEn',
      whiteBox: { x: 303.05, y: py(111.61), width: 90, height: LH },
      text: { x: 303.05, y: py(111.61), font: 'arial', size: 11.04 },
    },

    // ── Tanggal + bulan + tahun baris Indonesia ───────────────────
    // "tanggal ………… bulan ………… tahun dua ribu dua puluh enam."
    // Kita overlay baris "………….. bulan …………." saja
    {
      id: 'dateMonthYearId',
      valueFn: (d) => `${d.contractDate} ${d.contractMonth} ${d.contractYear}`,
      whiteBox: { x: 86.424, y: py(124.21), width: 197, height: LH },
      text: { x: 86.424, y: py(124.21), font: 'arial', size: 11.04 },
    },

    // ── Tanggal + bulan + tahun baris Inggris ────────────────────
    {
      id: 'dateMonthYearEn',
      valueFn: (d) => `${d.contractDate} ${d.contractMonthEn} ${d.contractYear},`,
      whiteBox: { x: 303.05, y: py(124.21), width: 207, height: LH },
      text: { x: 303.05, y: py(124.21), font: 'arial', size: 11.04 },
    },

    // ── Nama Pihak Kedua – kolom kiri (butir "2. …………………………….,") ──
    {
      id: 'secondPartyNameId',
      dataKey: 'secondPartyName',
      whiteBox: { x: 99.024, y: py(327.88), width: 190, height: LH },
      text: { x: 99.024, y: py(327.88), font: 'arial', size: 11.04 },
    },

    // ── Nama Pihak Kedua – kolom kanan ───────────────────────────
    {
      id: 'secondPartyNameEn',
      dataKey: 'secondPartyName',
      whiteBox: { x: 315.05, y: py(327.88), width: 195, height: LH },
      text: { x: 315.05, y: py(327.88), font: 'arial', size: 11.04 },
    },

    // ── KTP/Alamat Pihak Kedua – kolom kiri ──────────────────────
    {
      id: 'secondPartyKtpId',
      dataKey: 'secondPartyKtp',
      whiteBox: { x: 99.024, y: py(340.48), width: 190, height: LH },
      text: { x: 99.024, y: py(340.48), font: 'arial', size: 11.04 },
    },

    // ── KTP/Alamat Pihak Kedua – kolom kanan ─────────────────────
    {
      id: 'secondPartyKtpEn',
      dataKey: 'secondPartyKtp',
      whiteBox: { x: 315.05, y: py(340.48), width: 195, height: LH },
      text: { x: 315.05, y: py(340.48), font: 'arial', size: 11.04 },
    },

    // ── Wakil Pihak Kedua – kolom kiri ("diwakili oleh ………………………...,") ─
    {
      id: 'secondPartyRepId',
      dataKey: 'secondPartyRepresentative',
      whiteBox: { x: 99.024, y: py(353.08), width: 190, height: LH },
      text: { x: 99.024, y: py(353.08), font: 'arial', size: 11.04 },
    },

    // ── Wakil Pihak Kedua – kolom kanan ──────────────────────────
    {
      id: 'secondPartyRepEn',
      dataKey: 'secondPartyRepresentative',
      whiteBox: { x: 315.05, y: py(353.08), width: 195, height: LH },
      text: { x: 315.05, y: py(353.08), font: 'arial', size: 11.04 },
    },
  ],

  /* ══════════════════════════════════════════════════
     HALAMAN 2  (page index 1)
     Pasal 2 (Masa Sewa) dan Pasal 3 (Harga Sewa)
  ══════════════════════════════════════════════════ */
  1: [

    // ── Masa sewa Pasal 2 butir 1 – kolom kiri ────────────────────
    // "………… - ………… terhitung sejak tanggal serah terima kendaraan."
    {
      id: 'rentalPeriodId',
      valueFn: (d) => `${d.rentalPeriod} (${d.rentalStart} s/d ${d.rentalEnd})`,
      whiteBox: { x: 99.024, y: py(104.749), width: 185, height: LH },
      text: { x: 99.024, y: py(104.749), font: 'arial', size: 11.04 },
    },

    // ── Masa sewa Pasal 2 butir 1 – kolom kanan ───────────────────
    {
      id: 'rentalPeriodEn',
      valueFn: (d) => `${d.rentalPeriod} (${d.rentalStartEn} to ${d.rentalEndEn})`,
      whiteBox: { x: 315.05, y: py(104.749), width: 195, height: LH },
      text: { x: 315.05, y: py(104.749), font: 'arial', size: 11.04 },
    },
  ],

  /* ══════════════════════════════════════════════════
     HALAMAN 7  (page index 6)
     Alamat pemberitahuan, Article 10, Tanda Tangan
  ══════════════════════════════════════════════════ */
  6: [

    // ── Blok alamat Pihak Kedua (kolom kiri, 4 baris + HP) ────────
    // topY awal kolom = 66.829, setiap baris +12.6
    {
      id: 'noticeParty2Line1',
      dataKey: 'secondPartyNoticeLines.0',
      whiteBox: { x: 81.024, y: py(66.829), width: 210, height: LH },
      text: { x: 81.024, y: py(66.829), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line2',
      dataKey: 'secondPartyNoticeLines.1',
      whiteBox: { x: 81.024, y: py(79.429), width: 210, height: LH },
      text: { x: 81.024, y: py(79.429), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line3',
      dataKey: 'secondPartyNoticeLines.2',
      whiteBox: { x: 81.024, y: py(92.029), width: 210, height: LH },
      text: { x: 81.024, y: py(92.029), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line4',
      dataKey: 'secondPartyNoticeLines.3',
      whiteBox: { x: 81.024, y: py(104.629), width: 210, height: LH },
      text: { x: 81.024, y: py(104.629), font: 'arial', size: 11.04 },
    },

    // ── HP Pihak Kedua (setelah baris "Hp. ....") ─────────────────
    {
      id: 'secondPartyPhone',
      valueFn: (d) => d.secondPartyPhone ? `Hp. ${d.secondPartyPhone}` : '',
      whiteBox: { x: 81.024, y: py(117.229), width: 210, height: LH },
      text: { x: 81.024, y: py(117.229), font: 'arial', size: 11.04 },
    },

    // ── Blok alamat Pihak Kedua – kolom KANAN (mirror) ────────────
    {
      id: 'noticeParty2Line1En',
      dataKey: 'secondPartyNoticeLines.0',
      whiteBox: { x: 298.37, y: py(66.829), width: 212, height: LH },
      text: { x: 298.37, y: py(66.829), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line2En',
      dataKey: 'secondPartyNoticeLines.1',
      whiteBox: { x: 298.37, y: py(79.429), width: 212, height: LH },
      text: { x: 298.37, y: py(79.429), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line3En',
      dataKey: 'secondPartyNoticeLines.2',
      whiteBox: { x: 298.37, y: py(92.029), width: 212, height: LH },
      text: { x: 298.37, y: py(92.029), font: 'arial', size: 11.04 },
    },
    {
      id: 'noticeParty2Line4En',
      dataKey: 'secondPartyNoticeLines.3',
      whiteBox: { x: 298.37, y: py(104.629), width: 212, height: LH },
      text: { x: 298.37, y: py(104.629), font: 'arial', size: 11.04 },
    },
    {
      id: 'secondPartyPhoneEn',
      valueFn: (d) => d.secondPartyPhone ? `Hp. ${d.secondPartyPhone}` : '',
      whiteBox: { x: 298.37, y: py(117.229), width: 212, height: LH },
      text: { x: 298.37, y: py(117.229), font: 'arial', size: 11.04 },
    },

    // ── Tanggal TTD – kolom kiri ───────────────────────────────────
    // "Jakarta, 3 Februari 2026"  → "Jakarta, {signingDateId}"
    {
      id: 'signingDateId',
      valueFn: (d) => `Jakarta, ${d.signingDateId}`,
      whiteBox: { x: 81.024, y: py(523.48), width: 210, height: LH },
      text: { x: 81.024, y: py(523.48), font: 'arial', size: 11.04 },
    },

    // ── Tanggal TTD – kolom kanan ─────────────────────────────────
    {
      id: 'signingDateEn',
      valueFn: (d) => `Jakarta, ${d.signingDateEn}`,
      whiteBox: { x: 298.37, y: py(523.48), width: 212, height: LH },
      text: { x: 298.37, y: py(523.48), font: 'arial', size: 11.04 },
    },

    // ── Nama penanda tangan Pihak Kedua (bawah garis TTD kanan) ───
    {
      id: 'secondPartySignerName',
      dataKey: 'secondPartySignerName',
      whiteBox: { x: 298.37, y: py(649.9), width: 212, height: LH },
      text: { x: 298.37, y: py(649.9), font: 'arial', size: 11.04 },
    },
  ],
};
