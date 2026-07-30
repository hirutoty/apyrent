/**
 * schema.js
 * Zod validation schema for contract overlay data.
 *
 * Only the dynamic fields (placeholders in the original PDF template) are
 * defined here. All static text stays untouched in the template.
 */

import { z } from 'zod';

export const ContractSchema = z.object({
  // ── Tanggal pembuka ────────────────────────────────────────────
  /** Nama hari Indonesia, e.g. "Kamis" */
  contractDay: z.string().min(1, 'contractDay is required'),

  /** Nama hari Inggris, e.g. "Thursday" */
  contractDayEn: z.string().min(1, 'contractDayEn is required'),

  /** Angka tanggal, e.g. "30" */
  contractDate: z.string().min(1, 'contractDate is required'),

  /** Nama bulan Indonesia, e.g. "Juli" */
  contractMonth: z.string().min(1, 'contractMonth is required'),

  /** Nama bulan Inggris, e.g. "July" */
  contractMonthEn: z.string().min(1, 'contractMonthEn is required'),

  /** Tahun, e.g. "2026" */
  contractYear: z.string().min(1, 'contractYear is required'),

  // ── Nomor kontrak ──────────────────────────────────────────────
  /** e.g. "075/Kont-Cust-Apy/I/2026" or "KTR-202607-0001" */
  contractNumber: z.string().min(1, 'contractNumber is required'),

  // ── Identitas Pihak Kedua ──────────────────────────────────────
  /** Nama badan usaha / perorangan Pihak Kedua */
  secondPartyName: z.string().min(1, 'secondPartyName is required'),

  /** Alamat sesuai KTP Pihak Kedua (akan ditimpa pada placeholder "KTP_…………") */
  secondPartyKtp: z.string().default('………………………………'),

  /** Nama wakil / penanggung jawab Pihak Kedua */
  secondPartyRepresentative: z.string().default('………………………………'),

  // ── Masa sewa ──────────────────────────────────────────────────
  /** Label durasi, e.g. "12 bulan" */
  rentalPeriod: z.string().min(1, 'rentalPeriod is required'),

  /** Tanggal mulai Indonesia, e.g. "30 Juli 2026" */
  rentalStart: z.string().min(1, 'rentalStart is required'),

  /** Tanggal selesai Indonesia, e.g. "30 Juli 2027" */
  rentalEnd: z.string().min(1, 'rentalEnd is required'),

  /** Tanggal mulai Inggris, e.g. "30 July 2026" */
  rentalStartEn: z.string().min(1, 'rentalStartEn is required'),

  /** Tanggal selesai Inggris, e.g. "30 July 2027" */
  rentalEndEn: z.string().min(1, 'rentalEndEn is required'),

  // ── Blok pemberitahuan Pihak Kedua (halaman 7) ─────────────────
  /**
   * Baris-baris alamat untuk blok pemberitahuan.
   * Maksimal 4 baris; baris kosong tetap dikirim ("") agar overlay konsisten.
   */
  secondPartyNoticeLines: z
    .array(z.string())
    .min(1)
    .max(4)
    .default(['………………………………', '', '', '']),

  /** Nomor HP Pihak Kedua */
  secondPartyPhone: z.string().default(''),

  // ── Tanda tangan (halaman 7) ───────────────────────────────────
  /** Tanggal penandatanganan Indonesia, e.g. "3 Februari 2026" */
  signingDateId: z.string().min(1, 'signingDateId is required'),

  /** Tanggal penandatanganan Inggris, e.g. "3 February 2026" */
  signingDateEn: z.string().min(1, 'signingDateEn is required'),

  /** Nama penanda tangan Pihak Kedua (di bawah garis TTD) */
  secondPartySignerName: z.string().default('………………………………'),
});

/** @typedef {import('zod').infer<typeof ContractSchema>} ContractData */
