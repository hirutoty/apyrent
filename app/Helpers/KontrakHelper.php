<?php

namespace App\Helpers;

/**
 * Helper untuk konten default pasal ketentuan kontrak sewa kendaraan.
 *
 * Struktur tiap pasal:
 *   judul_id  : string  — judul bahasa Indonesia (e.g. "PASAL 2\nMASA SEWA")
 *   judul_en  : string  — judul bahasa Inggris
 *   tipe      : 'paragraf' | 'list' | 'sublist'
 *               paragraf  = teks prose biasa (no numbering)
 *               list      = numbered (1, 2, 3...)
 *               sublist   = lettered (a, b, c...) — digunakan untuk sub-poin
 *   poin      : array of { id, en }
 *
 * Pasal 1 (DATA-DATA KENDARAAN) sekarang masuk dalam array dan dapat diedit.
 * Pasal 2–10 berikutnya adalah isi ketentuan kontrak.
 */
class KontrakHelper
{
    public static function defaultPasalKetentuan(): array
    {
        return [
            // ─── PASAL 1 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 1\nDATA-DATA KENDARAAN",
                'judul_en' => "ARTICLE 1\nVEHICLE DATA",
                'tipe'     => 'paragraf',
                'poin'     => [
                    [
                        'id' => 'PIHAK PERTAMA telah menyerahkan kendaraan untuk disewa oleh PIHAK KEDUA dan PIHAK KEDUA telah menerima kendaraan tersebut yang tertuang dalam berita acara serah terima kendaraan dan check list yang ditandatangani oleh Para Pihak dan merupakan bagian yang tak terpisahkan dari Perjanjian ini. Adapun spesifikasi dan jumlah kendaraan tertuang dalam lampiran 1 (satu) Perjanjian ini.',
                        'en' => 'The FIRST PARTY shall provide a car to be rented by the SECOND PARTY and the SECOND PARTY shall receive the said car that is specifically stated in the vehicle handover form and check list form signed by The Parties that constitute and inseparable part of this Agreement. The car specification and quantity are described in detail in attachment 1 (one) of the Agreement.',
                    ],
                ],
            ],

            // ─── PASAL 2 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 2\nMASA SEWA",
                'judul_en' => "ARTICLE 2\nRENTAL PERIOD",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'Mobil tersebut diatas disewa oleh PIHAK KEDUA untuk jangka waktu {DURASI}, mulai {TANGGAL_MULAI} s/d {TANGGAL_SELESAI}, terhitung sejak tanggal serah terima kendaraan.',
                        'en' => 'The Car mentioned above shall be rented by the Second Party for a period of {DURASI}, commencing {TANGGAL_MULAI_EN} until {TANGGAL_SELESAI_EN}, after the delivery of cars.',
                    ],
                    [
                        'id' => 'Apabila kendaraan tidak dikembalikan tepat waktu, maka akan dikenakan biaya sewa harian sebesar Rp. 400.000,- / hari.',
                        'en' => 'If the vehicle is not returned on time, it will be charged a daily rental of Rp.400.000,- / day',
                    ],
                    [
                        'id' => 'Pengiriman kendaraan paling lambat 3 minggu setelah diterimanya SPK (Surat Perintah Kerja) atau PO (Purchase Order).',
                        'en' => 'Delivery of cars at least 3 (three) weeks after the receipt of Work Authorization or PO (Purchase Order)',
                    ],
                ],
            ],

            // ─── PASAL 3 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 3\nHARGA SEWA DAN PEMBAYARAN",
                'judul_en' => "ARTICLE 3\nRENTAL PRICE AND PAYMENT",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'Harga sewa mobil dinyatakan dalam lampiran 1 (satu) Perjanjian ini.',
                        'en' => 'The car rental fee is written on the attachment 1 (one) of the Agreement.',
                    ],
                    [
                        'id' => "Sewa mobil yang dibayarkan sudah termasuk :\n  - Pemeliharaan dan reparasi Kendaraan\n  - Biaya STNK/KIR\n  - Asuransi All Risk\n  - Kendaraan Pengganti\nDan tidak termasuk\n  - PPN {PPN}%\n  - PPH 23 {PPH}%\n  - Bensin, parkir dan tol.",
                        'en' => "The car rental fee paid include:\n  - Maintenance And Repair\n  - Motor Vehicle Document (STNK/KIR)\n  - All Risk Insurance\n  - Replacement car\nAnd exclude\n  - Value Added Tax {PPN}%\n  - Income tax {PPH}%\n  - Gasoline, parking and toll fee",
                    ],
                    [
                        'id' => 'PIHAK KEDUA akan melakukan pembayaran sejumlah tersebut diatas kepada PIHAK PERTAMA paling lambat 14 hari terhitung dari tanggal diterimanya tagihan resmi pada bulan berjalan, dengan dilampiri invoice, faktur pajak dan dokumen lain yang mendukung.',
                        'en' => 'The SECOND PARTY shall pay to the FIRST PARTY the payment of car rental fee at the latest 14 (Fourteen) days from the date of receiving Invoice of the current month, along with attach invoice, VAT certificate and other supporting document.',
                    ],
                    [
                        'id' => "Pembayaran dilakukan melalui:\n  Nama Bank    : {NAMA_BANK}\n  No. Rekening : {NO_REKENING}\n  Atas nama    : {ATAS_NAMA}",
                        'en' => "Payment is done through:\n  Bank Name    : {NAMA_BANK}\n  Account No.  : {NO_REKENING}\n  Account Name : {ATAS_NAMA}",
                    ],
                    [
                        'id' => 'Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya sebagaimana disebut diatas maka PIHAK KEDUA akan dikenakan denda sebesar 10 % dari total nilai sewa per hari untuk setiap hari keterlambatan.',
                        'en' => 'If The SECOND PARTY fails to perform the payment obligation mentioned above, the SECOND PARTY shall be liable to a fine as much as 10 % of the total payable rent per day.',
                    ],
                ],
            ],

            // ─── PASAL 4 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 4\nKEWAJIBAN PIHAK PERTAMA",
                'judul_en' => "ARTICLE 4\nOBLIGATION OF THE FIRST PARTY",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'PIHAK PERTAMA berkewajiban untuk melakukan perawatan dan perbaikan di pool/bengkel PIHAK PERTAMA maupun bengkel rekanan yang ditunjuk PIHAK PERTAMA sehingga mobil dalam keadaan siap beroperasi/baik selama masa sewa.',
                        'en' => 'The FIRST PARTY is obligated to maintain and repair the rented car in the repair shop of the FIRST PARTY or any repair shop appointed by the FIRST PARTY so that the car is in good condition during the rental period.',
                    ],
                    [
                        'id' => 'Batas jarak tempuh kendaraan adalah sebesar 2500 km/bulan',
                        'en' => 'The maximum distance travel in a month is 2500 km',
                    ],
                    [
                        'id' => 'Jarak tempuh dapat diakumulasikan dan kelebihannya akan dibayar diakhir sewa.',
                        'en' => 'The mileage can be accumulated and the surcharge will be billed in the end of the rent period.',
                    ],
                    [
                        'id' => 'Apabila mobil yang disewa tersebut mengalami kerusakan mesin sewaktu berada di luar kota, maka akan dikenakan biaya tambahan untuk jasa storing.',
                        'en' => 'If the car breakdown when it is out of town due to engine failure, additional costs will be charged for on call services.',
                    ],
                    [
                        'id' => 'PIHAK PERTAMA telah sepakat untuk menyediakan penggantian mobil jika mobil yang disewa oleh PIHAK KEDUA sedang dalam perbaikan lebih dari 24 (dua puluh empat jam) jam.',
                        'en' => 'FIRST PARTY has agreed to provide replacement car in case of the car rent by SECOND PARTY is being repaired for more than 24 (twenty four) hours.',
                    ],
                    [
                        'id' => 'PIHAK PERTAMA dapat melakukan penggantian ban, apabila mana yang lebih dulu mencapai pemakaian 60.000 km atau setelah 2 tahun.',
                        'en' => 'THE FIRST PARTY can replace the tires, whichever reaches 60,000 km of use first or after 2 years.',
                    ],
                    [
                        'id' => "PIHAK PERTAMA berkewajiban untuk mengasuransikan kendaraan secara All Risk tetapi diluar banjir dan Hura-Hara dengan ketentuan sebagai berikut:\n  a. Kewajiban Pihak Ketiga yang ditanggung PIHAK PERTAMA sesuai dengan polis asuransi sebesar Rp. 10.000.000,- (Sepuluh juta rupiah) untuk sedan dan minibus per kejadian. Kelebihan tanggungan menjadi tanggung jawab PIHAK KEDUA.\n  b. Dalam hal kecelakaan/kehilangan/pencurian mobil yang disewa, dimana kerugian tidak ditanggung oleh asuransi, maka kerugian sepenuhnya beralih menjadi tanggung jawab PIHAK KEDUA.\n  c. Selama proses pengurusan pengajuan klaim asuransi atas kehilangan tersebut, PIHAK KEDUA tidak mendapat kendaraan pengganti dan berkewajiban membayar klaim own risk sebesar 10% dari uang pertanggungan yang tertera di polis.\n  d. Dalam hal terjadinya kecelakaan yang memerlukan perbaikan body repair, PIHAK KEDUA berkewajiban membayar biaya resiko sendiri.",
                        'en' => "The FIRST PARTY is obligated to insure the rented car with all risk insurance but exclude flood, SRCC (Strike, Riot, Civil, Commotion) under the following provisions:\n  a. Third Party Liabilities (TPL) accounted by FIRST PARTY is equal to or maximum Rp. 10.000.000,- (ten million rupiah) for sedan and minibus per occurrence. Exceeding amount becomes the SECOND PARTY responsibility.\n  b. In the event of damage/loss/theft of the car, hence the claim is rejected by the insurance company and in effect will hold responsible fully to the cost effect of occurrence.\n  c. While undergoing the process of insurance claim for the loss/theft of the car, The SECOND PARTY will not receive replacement car and responsible to pay own risk claim of 10% of the insured sum that is written in the insurance policy.\n  d. In the event of accident that requires body repair, the SECOND PARTY is obligated to pay own risk.",
                    ],
                ],
            ],

            // ─── PASAL 5 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 5\nKEWAJIBAN PIHAK KEDUA",
                'judul_en' => "ARTICLE 5\nOBLIGATION OF THE SECOND PARTY",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'PIHAK KEDUA menyatakan akan menjaga dan merawat mobil yang disewa serta menyediakan tempat parkir yang aman.',
                        'en' => 'SECOND PARTY assures to keep and protect the rented car and also provide a safe parking lot.',
                    ],
                    [
                        'id' => 'Selama masa sewa, kendaraan di parkir di tempat parkir PIHAK KEDUA.',
                        'en' => 'During rental period, the car will be parked in SECOND PARTY\'s parking lot.',
                    ],
                    [
                        'id' => 'Bila terjadi kehilangan/pencurian mobil, PIHAK KEDUA berkewajiban untuk memberitahu PIHAK PERTAMA dalam waktu 1x24 jam, untuk bersama-sama melaporkan kepada kepolisian agar mendapat Surat Keterangan Laporan Kehilangan dan Surat Pemblokiran STNK mobil yang dikeluarkan oleh POLDA setempat. Biaya yang dikeluarkan menjadi tanggung jawab PIHAK KEDUA.',
                        'en' => 'In the event of loss/theft, SECOND PARTY has obligation to inform FIRST PARTY within 24 hour. Together, both parties report to the Police station in order to obtain the Lost Report Information Letter and Vehicle Motor Document (STNK) Blocking Letter that is issued by Police Department (POLDA). All costs incurred will be responsibility of SECOND PARTY.',
                    ],
                    [
                        'id' => 'PIHAK KEDUA tidak berhak memindah tangankan dan atau menyewakan mobil tersebut kepada pihak lain termasuk menjadikan mobil sebagai jaminan',
                        'en' => 'The SECOND PARTY is not allowed to re-let and/or transfer its right in any nature to any other party including making the car as a guarantee.',
                    ],
                    [
                        'id' => 'PIHAK KEDUA tidak diperbolehkan untuk merubah atau mengganti bentuk mobil, menambah atau meniadakan perlengkapan mobil tanpa seizin PIHAK PERTAMA.',
                        'en' => 'The SECOND PARTY is not allowed to change and/or replace the form of the car, to add, replace, or detach any part of the car without previously notify the FIRST PARTY.',
                    ],
                    [
                        'id' => "PIHAK KEDUA berkewajiban untuk memberitahu secara tertulis kepada PIHAK PERTAMA dalam hal:\n  a. Perubahan nama/alamat PIHAK KEDUA\n  b. Jika ada perubahan dalam fungsi atau kegunaan mobil.",
                        'en' => "The SECOND PARTY is obligated to send a written notice to the FIRST PARTY :\n  a. If the SECOND PARTY changes their name/address.\n  b. In case of any change of car utilization purpose.",
                    ],
                    [
                        'id' => 'PIHAK KEDUA tidak diperbolehkan untuk menggunakan mobil untuk balap/lomba mobil, kampanye politik, aksi kriminal, membawa penumpang dengan alasan komersial atau alasan lainnya selain alasan domestik atau sosial.',
                        'en' => 'The SECOND PARTY is not allowed to use the car in/for any car race, political campaign, criminal action, carrying any passenger for commercial purpose and/or any other purpose besides the domestic and social purposes.',
                    ],
                    [
                        'id' => 'Mengembalikan kendaraan pada saat masa sewa berakhir dalam keadaan semula, dikecualikan perubahan yang dikarenakan pemakaian yang wajar dengan lampaunya waktu.',
                        'en' => 'To return the car on the expiration of the lease duration in original condition, save for reasonable wear and tear due the passage of time.',
                    ],
                ],
            ],

            // ─── PASAL 6 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 6\nSTNK",
                'judul_en' => "ARTICLE 6\nMOTOR VEHICLE DOCUMENT (STNK)",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'Pengurusan dan biaya STNK adalah kewajiban PIHAK PERTAMA dan akan dilakukan perpanjangan 7 (tujuh) hari sebelum masa STNK berakhir.',
                        'en' => 'The extension cost of the Motor Vehicle Document (STNK) shall be paid by the FIRST PARTY and shall be conducted 7 (seven) days prior to the expiration date.',
                    ],
                    [
                        'id' => 'PIHAK KEDUA bertanggung jawab untuk menanggung seluruh biaya yang timbul sebagai akibat hilangnya STNK dan atau terjadinya keterlambatan pengurusan perpanjangan STNK karena kesalahan dan atau kelalaian PIHAK KEDUA.',
                        'en' => 'The SECOND PARTY is responsible for all the costs born for the lost of vehicle legal document (STNK) and also for any delay in extension process because of the SECOND PARTY negligence.',
                    ],
                ],
            ],

            // ─── PASAL 7 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 7\nPENGEMUDI",
                'judul_en' => "ARTICLE 7\nDRIVER",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'Kendaraan yang disewa akan dikemudikan oleh pengemudi PIHAK KEDUA.',
                        'en' => 'The Car lease shall be driven by SECOND PARTY\'s driver',
                    ],
                    [
                        'id' => 'PIHAK KEDUA akan menanggung segala kerugian dan akibat hukum yang ditimbulkan ketika kendaraan dikemudikan oleh pengemudi yang ditugaskan PIHAK KEDUA dan tidak di parkir di tempat yang telah ditentukan.',
                        'en' => 'SECOND PARTY shall bear all losses and legal consequences caused when the vehicle is being driven by a driver who was assigned by the SECOND PARTY and when the vehicle is not parked in the designated parking lot.',
                    ],
                ],
            ],

            // ─── PASAL 8 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 8\nPEMUTUSAN DAN PERPANJANGAN PERJANJIAN",
                'judul_en' => "ARTICLE 8\nTERMINATION AND EXTENSION OF AGREEMENT",
                'tipe'     => 'list',
                'poin'     => [
                    [
                        'id' => 'Apabila PIHAK KEDUA tidak dapat melaksanakan kewajibannya dalam hal pembayaran uang sewa kepada PIHAK PERTAMA seperti tercantum dalam perjanjian ini, maka PIHAK PERTAMA berhak untuk mengakhiri perjanjian ini dengan mengirim surat peringatan 3 (tiga) hari kalender sebelum pemutusan kepada PIHAK KEDUA dan tidak lebih dari 3 (tiga) hari kalender setelah PIHAK KEDUA menerima surat peringatan PIHAK PERTAMA, PIHAK KEDUA harus mengembalikan mobil kepada PIHAK PERTAMA dengan kondisi baik dan dilokasi dimana pertama kali PIHAK PERTAMA menyerahkan kendaraan kepada PIHAK KEDUA.',
                        'en' => 'If the SECOND PARTY fails to fulfill one of the stipulations with respect to the payment obligation to the FIRST PARTY as regulated in this agreement, the FIRST PARTY shall have the right to terminate this agreement by delivering a written reprimand 3 (three) calendar days before the termination to the SECOND PARTY and not later than 3 (three) calendar days after the SECOND PARTY receives the reprimand letter from the FIRST PARTY, the SECOND PARTY has to return the car to the FIRST PARTY in a good condition and at the location where the FIRST PARTY hand over the car to the SECOND PARTY at the first time.',
                    ],
                    [
                        'id' => 'Kedua belah pihak dapat memperpanjang masa kontrak dan atau menambah jumlah kendaraan sewa dengan suatu perjanjian tambahan (addendum), yang merupakan satu kesatuan yang tidak terpisahkan dengan Perjanjian ini.',
                        'en' => 'Both parties can extend the contract period and or add the quantity of cars rented with a supplemental agreement (addendum), which is an inseparable part of the Agreement.',
                    ],
                    [
                        'id' => 'Kedua belah pihak dapat mengakhiri atau membatalkan perjanjian ini sebelum masa sewa berakhir namun dikenakan sanksi atau denda sebesar 25% (dua puluh persen) dari nilai sisa kontrak dan uang sewa yang telah dibayar dimuka tidak dapat dikembalikan, kecuali mobil tersebut sering mengalami kerusakan selama digunakan',
                        'en' => 'Both PARTIES can terminate/cancel this Agreement before the expiry date but liable for a fine or penalty 25% (twenty five percent) of the total unpaid rent and the rental fee that has been paid upfront could not be refunded, unless the car is often damaged during use',
                    ],
                    [
                        'id' => 'Hal-hal yang belum atau tidak cukup diatur dalam pasal akan mengacu pada lampiran perjanjian ini.',
                        'en' => 'All other matter which is not covered in the articles will be covered in the attachment.',
                    ],
                ],
            ],

            // ─── PASAL 9 ─────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 9\nPEMBERITAHUAN",
                'judul_en' => "ARTICLE 9\nNOTICE",
                'tipe'     => 'paragraf',
                'poin'     => [
                    [
                        'id' => "Segala pemberitahuan, permintaan dan komunikasi lainnya sehubungan dengan perjanjian ini, harus dibuat secara tertulis dan disampaikan secara pribadi atau dikirim melalui jasa kurir atau faksimili kepada para pihak dengan alamat:\n\nPIHAK PERTAMA\n{NAMA_PERUSAHAAN}\n{ALAMAT_PERUSAHAAN}\nTelp. {TELEPON_PERUSAHAAN}\nFax.  {FAX_PERUSAHAAN}\n\nPIHAK KEDUA\n{NAMA_PIHAK_KEDUA}\n{ALAMAT_PIHAK_KEDUA}\nHp. {KONTAK_PIHAK_KEDUA}",
                        'en' => "Any notice, request and other communications relating to this agreement must be made in writing and submitted in person or delivered through courier or facsimile to parties in the following addresses:\n\nTHE FIRST PARTY\n{NAMA_PERUSAHAAN}\n{ALAMAT_PERUSAHAAN}\nTelp. {TELEPON_PERUSAHAAN}\nFax.  {FAX_PERUSAHAAN}\n\nTHE SECOND PARTY\n{NAMA_PIHAK_KEDUA}\n{ALAMAT_PIHAK_KEDUA}\nHp. {KONTAK_PIHAK_KEDUA}",
                    ],
                ],
            ],

            // ─── PASAL 10 ────────────────────────────────────────────────────
            [
                'judul_id' => "PASAL 10\nPENUTUP",
                'judul_en' => "ARTICLE 10\nCLOSING PROVISION",
                'tipe'     => 'paragraf',
                'poin'     => [
                    [
                        'id' => 'Apabila terjadi perselisihan akan diselesaikan oleh kedua belah pihak secara musyawarah untuk mufakat, bila tidak tercapai kesepakatan secara musyawarah, maka kedua belah pihak setuju untuk memilih domisili hukum tetap dan tidak berubah di Kantor Panitera Negeri Jakarta Selatan, di Jakarta.',
                        'en' => 'Any dispute arising form this Agreement of Vehicle Rent shall be settled in deliberation by Both Parties, and if the dispute cannot be settled in deliberation, Both Parties shall agree to elect the general and permanent domicile at the office clerk of the District Court of Jakarta Selatan, in Jakarta.',
                    ],
                    [
                        'id' => 'Demikian Surat Perjanjian Sewa Menyewa ini dibuatkan dan ditanda tangani oleh kedua belah pihak dalam 2 (dua) rangkap yang keduanya bermaterai cukup dan mempunyai kekuatan hukum yang sama.',
                        'en' => 'In witness whereof this Agreement of Vehicle Rent was made and signed by Both Parties in duplicate, each duty stamped and having the same legal force.',
                    ],
                ],
            ],
        ];
    }
}
