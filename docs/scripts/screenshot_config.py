"""
Complete screenshot configuration for APYRent Documentation
All 85 screenshots with annotation coordinates
"""

BASE_URL = "http://127.0.0.1:8000"

# All screenshots with annotations
ALL_SCREENSHOTS = [
    # ========== BAB 1: LOGIN DAN DASHBOARD ==========
    {
        "bab": 1,
        "name": "01-login-page.png",
        "url": f"{BASE_URL}/login",
        "wait_for": "input[type='email']",
        "annotations": [
            {"type": "circle", "x": 520, "y": 285, "number": 1},
            {"type": "circle", "x": 520, "y": 340, "number": 2},
            {"type": "circle", "x": 575, "y": 425, "number": 3},
        ],
        "auth_required": False
    },
    {
        "bab": 1,
        "name": "02-dashboard.png",
        "url": f"{BASE_URL}/admin/dashboard",
        "wait_for": ".content-wrapper",
        "annotations": [
            {"type": "circle", "x": 100, "y": 50, "number": 1},
            {"type": "circle", "x": 1200, "y": 30, "number": 2},
        ],
        "auth_required": True
    },
    {
        "bab": 1,
        "name": "03-header-user-menu.png",
        "url": f"{BASE_URL}/admin/dashboard",
        "wait_for": ".content-wrapper",
        "annotations": [
            {"type": "circle", "x": 1200, "y": 30, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 1,
        "name": "04-logout-button.png",
        "url": f"{BASE_URL}/admin/dashboard",
        "wait_for": ".content-wrapper",
        "action": "hover_user_menu",
        "annotations": [
            {"type": "circle", "x": 1200, "y": 80, "number": 1},
        ],
        "auth_required": True
    },
    
    # ========== BAB 2: DATA & STOK KENDARAAN ==========
    {
        "bab": 2,
        "name": "05-sidebar-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": ".content-wrapper",
        "annotations": [
            {"type": "circle", "x": 100, "y": 150, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "06-tabel-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 150, "number": 1},
            {"type": "circle", "x": 1150, "y": 300, "number": 2},
            {"type": "circle", "x": 1200, "y": 300, "number": 3},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "07-btn-tambah-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 150, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "08-form-tambah-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan/create",
        "wait_for": "form",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 600},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "09-upload-foto-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan/create",
        "wait_for": "form",
        "scroll_to": "input[type='file']",
        "annotations": [
            {"type": "circle", "x": 400, "y": 500, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "10-btn-simpan-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan/create",
        "wait_for": "form",
        "scroll_to": "button[type='submit']",
        "annotations": [
            {"type": "circle", "x": 600, "y": 650, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "11-notif-berhasil.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": ".alert-success",
        "annotations": [
            {"type": "rectangle", "x1": 950, "y1": 80, "x2": 1250, "y2": 130},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "12-icon-edit-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "13-form-edit-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "form",
        "action": "click_edit_first",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 600},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "14-btn-update-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "form",
        "action": "click_edit_first",
        "scroll_to": "button[type='submit']",
        "annotations": [
            {"type": "circle", "x": 600, "y": 650, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "15-icon-hapus-kendaraan.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1200, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 2,
        "name": "16-popup-konfirmasi-hapus.png",
        "url": f"{BASE_URL}/admin/kendaraan",
        "wait_for": ".swal2-popup",
        "action": "click_delete_first",
        "annotations": [
            {"type": "circle", "x": 640, "y": 420, "number": 1},
            {"type": "circle", "x": 540, "y": 420, "number": 2},
        ],
        "auth_required": True
    },
    
    # ========== BAB 3: PAJAK KENDARAAN ==========
    {
        "bab": 3,
        "name": "17-sidebar-compliance.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": ".content-wrapper",
        "annotations": [
            {"type": "circle", "x": 100, "y": 400, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "18-tabel-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 900, "y": 250, "number": 1},
            {"type": "circle", "x": 1100, "y": 250, "number": 2},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "19-btn-perpanjang-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 250, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "20-form-perpanjang-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 500},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "21-upload-bukti-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "scroll_to": "input[type='file']",
        "annotations": [
            {"type": "circle", "x": 400, "y": 450, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "22-btn-ajukan-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "scroll_to": "button[type='submit']",
        "annotations": [
            {"type": "circle", "x": 600, "y": 550, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "23-notif-pengajuan-berhasil.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": ".alert-success",
        "annotations": [
            {"type": "rectangle", "x1": 950, "y1": 80, "x2": 1250, "y2": 130},
        ],
        "auth_required": True
    },
    {
        "bab": 3,
        "name": "24-btn-ajukan-ulang-pajak.png",
        "url": f"{BASE_URL}/admin/pajak",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    
    # ========== BAB 4: ASURANSI KENDARAAN ==========
    {
        "bab": 4,
        "name": "25-tabel-asuransi.png",
        "url": f"{BASE_URL}/admin/asuransi-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 900, "y": 250, "number": 1},
            {"type": "circle", "x": 1100, "y": 250, "number": 2},
        ],
        "auth_required": True
    },
    {
        "bab": 4,
        "name": "26-btn-perpanjang-asuransi.png",
        "url": f"{BASE_URL}/admin/asuransi-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 250, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 4,
        "name": "27-form-perpanjang-asuransi.png",
        "url": f"{BASE_URL}/admin/asuransi-kendaraan",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 550},
        ],
        "auth_required": True
    },
    {
        "bab": 4,
        "name": "28-btn-ajukan-ulang-asuransi.png",
        "url": f"{BASE_URL}/admin/asuransi-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    
    # ========== BAB 5: GPS KENDARAAN ==========
    {
        "bab": 5,
        "name": "29-tabel-gps.png",
        "url": f"{BASE_URL}/admin/gps-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 900, "y": 250, "number": 1},
            {"type": "circle", "x": 1100, "y": 250, "number": 2},
        ],
        "auth_required": True
    },
    {
        "bab": 5,
        "name": "30-btn-perpanjang-gps.png",
        "url": f"{BASE_URL}/admin/gps-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 250, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 5,
        "name": "31-form-perpanjang-gps.png",
        "url": f"{BASE_URL}/admin/gps-kendaraan",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 600},
        ],
        "auth_required": True
    },
    {
        "bab": 5,
        "name": "32-btn-ajukan-ulang-gps.png",
        "url": f"{BASE_URL}/admin/gps-kendaraan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    
    # ========== BAB 6: KIR ==========
    {
        "bab": 6,
        "name": "33-tabel-kir.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 900, "y": 250, "number": 1},
            {"type": "circle", "x": 1100, "y": 250, "number": 2},
        ],
        "auth_required": True
    },
    {
        "bab": 6,
        "name": "34-btn-perpanjang-kir.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 250, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 6,
        "name": "35-form-perpanjang-kir.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "form",
        "action": "click_perpanjang_first",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 180, "x2": 950, "y2": 550},
        ],
        "auth_required": True
    },
    {
        "bab": 6,
        "name": "36-btn-update-status-uji.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 6,
        "name": "37-form-update-status-uji.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "form",
        "action": "click_update_status_first",
        "annotations": [
            {"type": "circle", "x": 400, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    {
        "bab": 6,
        "name": "38-btn-ajukan-ulang-kir.png",
        "url": f"{BASE_URL}/admin/kir",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1100, "y": 300, "number": 1},
        ],
        "auth_required": True
    },
    
    # Continue for BAB 7-14...
    # Due to length constraints, showing pattern for remaining BABs
    
    
    # BAB 7: LAPORAN KEHILANGAN
    {
        "name": "39-menu-laporan-kehilangan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 450, "number": 1, "label": "Menu Laporan"},
        ]
    },
    {
        "name": "40-tabel-laporan-kehilangan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1100, "y2": 500},
        ]
    },
    {
        "name": "41-btn-tambah-laporan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "a[href*='create']",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "42-form-tambah-laporan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Tanggal"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Lokasi"},
            {"type": "circle", "x": 420, "y": 450, "number": 4, "label": "Keterangan"},
        ]
    },
    {
        "name": "43-btn-lihat-detail-laporan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Detail"},
        ]
    },
    {
        "name": "44-detail-laporan-kehilangan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 200, "x2": 900, "y2": 550},
        ]
    },
    {
        "name": "45-btn-hapus-laporan.png",
        "url": f"{BASE_URL}/admin/laporan-kehilangan",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 250, "number": 1, "label": "Hapus"},
        ]
    },
    
    # BAB 8: PEMBAYARAN
    {
        "name": "46-menu-pembayaran.png",
        "url": f"{BASE_URL}/admin/pembayaran",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 500, "number": 1, "label": "Menu Pembayaran"},
        ]
    },
    {
        "name": "47-tabel-pembayaran.png",
        "url": f"{BASE_URL}/admin/pembayaran",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1150, "y2": 500},
        ]
    },
    {
        "name": "48-btn-tambah-pembayaran.png",
        "url": f"{BASE_URL}/admin/pembayaran",
        "wait_for": "a[href*='create']",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "49-form-tambah-pembayaran.png",
        "url": f"{BASE_URL}/admin/pembayaran/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Booking"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Jumlah"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Metode"},
            {"type": "circle", "x": 420, "y": 380, "number": 4, "label": "Tanggal"},
        ]
    },
    {
        "name": "50-btn-approve-pembayaran.png",
        "url": f"{BASE_URL}/admin/pembayaran",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Approve"},
        ]
    },
    {
        "name": "51-konfirmasi-approve.png",
        "url": f"{BASE_URL}/admin/pembayaran",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 400, "y1": 250, "x2": 880, "y2": 400},
        ]
    },
    
    # BAB 9: MEMBER (Route: /admin/members)
    {
        "name": "52-menu-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 550, "number": 1, "label": "Menu Member"},
        ]
    },
    {
        "name": "53-tabel-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1100, "y2": 500},
        ]
    },
    {
        "name": "54-btn-tambah-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "a[href*='create']",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "55-form-tambah-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah Member"},
            {"type": "circle", "x": 420, "y": 400, "number": 2, "label": "Form Input"},
        ]
    },
    {
        "name": "56-btn-edit-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "action": "click_edit_first",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Edit"},
        ]
    },
    {
        "name": "57-btn-hapus-member.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 250, "number": 1, "label": "Hapus"},
        ]
    },
    
    # BAB 10: PELANGGAN (Uses same route as Member - /admin/members)
    {
        "name": "58-menu-pelanggan.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 600, "number": 1, "label": "Menu Pelanggan"},
        ]
    },
    {
        "name": "59-tabel-pelanggan.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1100, "y2": 500},
        ]
    },
    {
        "name": "60-btn-lihat-detail-pelanggan.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Detail"},
        ]
    },
    {
        "name": "61-detail-pelanggan.png",
        "url": f"{BASE_URL}/admin/members",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 200, "x2": 900, "y2": 550},
        ]
    },
    
    # BAB 11: BOOKING/RENTAL KENDARAAN (Route: /admin/member-rental)
    {
        "name": "62-menu-booking.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 650, "number": 1, "label": "Menu Booking"},
        ]
    },
    {
        "name": "63-tabel-booking.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1150, "y2": 500},
        ]
    },
    {
        "name": "64-btn-tambah-booking.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "a[href*='create']",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "65-form-tambah-booking.png",
        "url": f"{BASE_URL}/admin/member-rental/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Pelanggan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Tanggal Mulai"},
            {"type": "circle", "x": 420, "y": 380, "number": 4, "label": "Tanggal Selesai"},
        ]
    },
    {
        "name": "66-btn-detail-booking.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Detail"},
        ]
    },
    {
        "name": "67-detail-booking.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 200, "x2": 900, "y2": 550},
        ]
    },
    
    # BAB 12: KENDARAAN AKTIF & HISTORY (Routes: /admin/member-rental and /admin/history)
    {
        "name": "68-menu-kendaraan-aktif.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 700, "number": 1, "label": "Menu Rental Aktif"},
        ]
    },
    {
        "name": "69-tabel-kendaraan-aktif.png",
        "url": f"{BASE_URL}/admin/member-rental",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1150, "y2": 500},
        ]
    },
    {
        "name": "70-menu-rental-history.png",
        "url": f"{BASE_URL}/admin/history",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 750, "number": 1, "label": "Menu History"},
        ]
    },
    {
        "name": "71-tabel-rental-history.png",
        "url": f"{BASE_URL}/admin/history",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1150, "y2": 500},
        ]
    },
    {
        "name": "72-btn-export-history.png",
        "url": f"{BASE_URL}/admin/history",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Export"},
        ]
    },
    
    # BAB 13: SERVICE KENDARAAN
    {
        "name": "73-menu-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 800, "number": 1, "label": "Menu Service"},
        ]
    },
    {
        "name": "74-tabel-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1150, "y2": 500},
        ]
    },
    {
        "name": "75-btn-tambah-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "a[href*='create']",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "76-form-service-rutin.png",
        "url": f"{BASE_URL}/admin/service/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Jenis: Rutin"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Tanggal"},
            {"type": "circle", "x": 420, "y": 380, "number": 4, "label": "Biaya"},
        ]
    },
    {
        "name": "77-form-service-asuransi.png",
        "url": f"{BASE_URL}/admin/service/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Jenis: Asuransi"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "No. Klaim"},
        ]
    },
    {
        "name": "78-form-service-incident.png",
        "url": f"{BASE_URL}/admin/service/create",
        "wait_for": "form",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Jenis: Incident"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Kronologi"},
        ]
    },
    {
        "name": "79-btn-lihat-detail-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1050, "y": 250, "number": 1, "label": "Detail"},
        ]
    },
    {
        "name": "80-detail-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 300, "y1": 200, "x2": 900, "y2": 550},
        ]
    },
    {
        "name": "81-btn-hapus-service.png",
        "url": f"{BASE_URL}/admin/service",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 250, "number": 1, "label": "Hapus"},
        ]
    },
    
    # BAB 14: DATA LEASING (Route: /admin/data-leasing)
    {
        "name": "82-menu-leasing.png",
        "url": f"{BASE_URL}/admin/data-leasing",
        "wait_for": "table",
        "annotations": [
            {"type": "circle", "x": 180, "y": 850, "number": 1, "label": "Menu Leasing"},
        ]
    },
    {
        "name": "83-tabel-leasing.png",
        "url": f"{BASE_URL}/admin/data-leasing",
        "wait_for": "table",
        "annotations": [
            {"type": "rectangle", "x1": 250, "y1": 200, "x2": 1100, "y2": 500},
        ]
    },
    {
        "name": "84-btn-tambah-leasing.png",
        "url": f"{BASE_URL}/admin/data-leasing",
        "wait_for": "a[href*='create'], button:has-text('Tambah')",
        "annotations": [
            {"type": "circle", "x": 1150, "y": 150, "number": 1, "label": "Tambah"},
        ]
    },
    {
        "name": "85-form-tambah-leasing.png",
        "url": f"{BASE_URL}/admin/data-leasing",
        "wait_for": ".content-wrapper",
        "annotations": [
            {"type": "circle", "x": 420, "y": 200, "number": 1, "label": "Kendaraan"},
            {"type": "circle", "x": 420, "y": 260, "number": 2, "label": "Perusahaan"},
            {"type": "circle", "x": 420, "y": 320, "number": 3, "label": "Cicilan/Bulan"},
            {"type": "circle", "x": 420, "y": 380, "number": 4, "label": "Jatuh Tempo"},
        ]
    },
]

# Export for main script
SCREENSHOTS = ALL_SCREENSHOTS
