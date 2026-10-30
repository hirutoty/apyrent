"""
Script untuk screenshot modal edit kendaraan yang benar (dengan data)
"""
from playwright.sync_api import sync_playwright
import time

def screenshot_edit_modal():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False, slow_mo=500)
        context = browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = context.new_page()
        
        print("🔐 Login ke APYRent...")
        page.goto('http://127.0.0.1:8000/login')
        page.fill('input[name="email"]', 'test@example.com')
        page.fill('input[name="password"]', 'admin123')
        page.click('button[type="submit"]')
        page.wait_for_url('**/admin/dashboard', timeout=5000)
        print("✅ Login berhasil!\n")
        
        # Langsung ke halaman kendaraan yang sudah ada data dari seeder
        print("📋 Membuka halaman Data Kendaraan...")
        page.goto('http://127.0.0.1:8000/admin/kendaraan')
        page.wait_for_load_state('networkidle')
        time.sleep(1)
        
        # Check apakah ada data
        has_data = page.locator('a[href*="/admin/kendaraan/"][href*="/edit"]').count() > 0
        
        if not has_data:
            print("⚠️  Tidak ada data kendaraan dari seeder")
            browser.close()
            return
        
        # Sekarang click tombol Edit di row pertama
        print("📸 Screenshot Modal Edit Kendaraan...")
        try:
            # Cari link edit pertama
            edit_link = page.locator('a[href*="/admin/kendaraan/"][href*="/edit"]').first
            if edit_link.is_visible():
                edit_link.click()
                time.sleep(1)
                
                # Screenshot modal edit
                page.screenshot(path='../images/27-modal-edit-kendaraan.png')
                print("✅ Screenshot modal edit berhasil: 27-modal-edit-kendaraan.png")
            else:
                print("⚠️  Link edit tidak ditemukan")
        except Exception as e:
            print(f"❌ Error: {e}")
        
        print("\n✅ Selesai! Browser akan tetap terbuka 3 detik...")
        time.sleep(3)
        
        browser.close()
        print("\n🎉 Screenshot modal edit kendaraan berhasil di-update!")

if __name__ == '__main__':
    screenshot_edit_modal()
