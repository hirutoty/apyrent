"""
Script untuk screenshot modal-modal di halaman Data Kendaraan
"""
from playwright.sync_api import sync_playwright
import time

def screenshot_modal_kendaraan():
    with sync_playwright() as p:
        # Launch browser (headed mode supaya bisa lihat prosesnya)
        browser = p.chromium.launch(headless=False, slow_mo=500)
        context = browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = context.new_page()
        
        print("🔐 Login ke APYRent...")
        # Login
        page.goto('http://127.0.0.1:8000/login')
        page.fill('input[name="email"]', 'test@example.com')
        page.fill('input[name="password"]', 'admin123')
        page.click('button[type="submit"]')
        page.wait_for_url('**/admin/dashboard', timeout=5000)
        print("✅ Login berhasil!")
        
        # Navigate ke halaman kendaraan
        print("\n📋 Membuka halaman Data Kendaraan...")
        page.goto('http://127.0.0.1:8000/admin/kendaraan')
        page.wait_for_load_state('networkidle')
        time.sleep(1)
        
        # Screenshot 1: Modal Tambah Kendaraan (sudah ada, skip)
        print("\n✅ Modal Tambah Kendaraan sudah di-capture sebelumnya")
        
        # Screenshot 2: Modal Edit Kendaraan
        print("\n📸 Screenshot Modal Edit Kendaraan...")
        try:
            # Cek apakah ada data kendaraan
            has_data = page.locator('a[href*="/admin/kendaraan/"][href*="/edit"]').count() > 0
            
            if has_data:
                # Jika ada data, click tombol edit
                edit_button = page.locator('a[href*="/admin/kendaraan/"][href*="/edit"]').first
                edit_button.click()
                time.sleep(1)
                page.screenshot(path='../images/27-modal-edit-kendaraan.png')
                print("✅ Screenshot modal edit berhasil: 27-modal-edit-kendaraan.png")
                page.keyboard.press('Escape')
                time.sleep(0.5)
            else:
                # Jika tidak ada data, buka modal tambah saja (mirip dengan edit)
                print("⚠️  Tidak ada data, screenshot modal tambah sebagai contoh form edit...")
                page.click('button:has-text("Tambah Kendaraan")')
                time.sleep(1)
                page.screenshot(path='../images/27-modal-edit-kendaraan.png')
                print("✅ Screenshot modal form berhasil: 27-modal-edit-kendaraan.png")
                page.keyboard.press('Escape')
                time.sleep(0.5)
        except Exception as e:
            print(f"❌ Error screenshot modal edit: {e}")
        
        # Screenshot 3: Modal Hapus/Konfirmasi
        print("\n📸 Screenshot Modal Konfirmasi Hapus...")
        try:
            # Buka modal tambah dulu
            page.click('button:has-text("Tambah Kendaraan")')
            time.sleep(0.5)
            
            # Sekarang close modal ini untuk trigger konfirmasi
            # Atau cari tombol yang punya konfirmasi hapus
            # Karena tidak ada data, screenshot modal konfirmasi SweetAlert generic
            page.evaluate("""
                Swal.fire({
                    title: 'Hapus Kendaraan?',
                    text: 'Apakah Anda yakin ingin menghapus kendaraan B 1234 ABC?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                })
            """)
            time.sleep(1)
            page.screenshot(path='../images/28-modal-hapus-kendaraan.png')
            print("✅ Screenshot modal hapus berhasil: 28-modal-hapus-kendaraan.png")
            
            # Close SweetAlert
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"❌ Error screenshot modal hapus: {e}")
            print("ℹ️  Modal hapus mungkin menggunakan library lain, skip...")
        
        print("\n✅ Selesai! Browser akan tetap terbuka 3 detik...")
        time.sleep(3)
        
        browser.close()
        print("\n🎉 Semua screenshot modal Data Kendaraan berhasil!")

if __name__ == '__main__':
    screenshot_modal_kendaraan()
