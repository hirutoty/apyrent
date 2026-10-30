"""
Script perbaikan untuk screenshot modal yang gagal
"""
from playwright.sync_api import sync_playwright
import time

def screenshot_missing_modals():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False, slow_mo=300)
        context = browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = context.new_page()
        
        print("🔐 Login ke APYRent...")
        page.goto('http://127.0.0.1:8000/login')
        page.fill('input[name="email"]', 'test@example.com')
        page.fill('input[name="password"]', 'admin123')
        page.click('button[type="submit"]')
        page.wait_for_url('**/admin/dashboard', timeout=5000)
        print("✅ Login berhasil!\n")
        
        screenshots = []
        
        # BAB 3: PAJAK - perbaikan
        print("📋 BAB 3: Pajak Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/pajak-kendaraan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Button onclick="openModalTambah()"
            page.click('button[onclick="openModalTambah()"]')
            time.sleep(1)
            page.screenshot(path='../images/29-modal-tambah-pajak.png')
            print("✅ 29-modal-tambah-pajak.png")
            screenshots.append(29)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"❌ Pajak: {e}")
        
        # BAB 4: ASURANSI - perbaikan
        print("\n📋 BAB 4: Asuransi Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/asuransi-kendaraan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Coba berbagai selector
            selectors = [
                'button[onclick*="Modal"]',
                'button:has-text("Tambah")',
                'a:has-text("Tambah")'
            ]
            
            for selector in selectors:
                try:
                    if page.locator(selector).first.is_visible(timeout=1000):
                        page.click(selector)
                        time.sleep(1)
                        page.screenshot(path='../images/30-modal-tambah-asuransi.png')
                        print("✅ 30-modal-tambah-asuransi.png")
                        screenshots.append(30)
                        page.keyboard.press('Escape')
                        time.sleep(0.5)
                        break
                except:
                    continue
            else:
                print("⚠️  Asuransi: Button tidak ditemukan")
        except Exception as e:
            print(f"❌ Asuransi: {e}")
        
        # BAB 7: KEHILANGAN - perbaikan
        print("\n📋 BAB 7: Laporan Kehilangan")
        try:
            page.goto('http://127.0.0.1:8000/admin/kehilangan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            selectors = [
                'button:has-text("Lapor")',
                'button:has-text("Tambah")',
                'a:has-text("Lapor")',
                'a:has-text("Tambah")'
            ]
            
            for selector in selectors:
                try:
                    if page.locator(selector).first.is_visible(timeout=1000):
                        page.click(selector)
                        time.sleep(1)
                        page.screenshot(path='../images/33-modal-lapor-kehilangan.png')
                        print("✅ 33-modal-lapor-kehilangan.png")
                        screenshots.append(33)
                        page.keyboard.press('Escape')
                        time.sleep(0.5)
                        break
                except:
                    continue
            else:
                print("⚠️  Kehilangan: Button tidak ditemukan")
        except Exception as e:
            print(f"❌ Kehilangan: {e}")
        
        # BAB 8: PEMBAYARAN - perbaikan
        print("\n📋 BAB 8: Pembayaran")
        try:
            page.goto('http://127.0.0.1:8000/admin/pembayaran')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            selectors = [
                'a:has-text("Buat")',
                'button:has-text("Tambah")',
                'a[href*="/create"]'
            ]
            
            for selector in selectors:
                try:
                    if page.locator(selector).first.is_visible(timeout=1000):
                        page.click(selector)
                        time.sleep(1)
                        page.screenshot(path='../images/34-halaman-buat-po.png')
                        print("✅ 34-halaman-buat-po.png (halaman, bukan modal)")
                        screenshots.append(34)
                        break
                except:
                    continue
            else:
                print("⚠️  Pembayaran: Button tidak ditemukan")
        except Exception as e:
            print(f"❌ Pembayaran: {e}")
        
        # BAB 11: RENTAL - perbaikan
        print("\n📋 BAB 11: Booking & Rental")
        try:
            page.goto('http://127.0.0.1:8000/admin/rental')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            selectors = [
                'a:has-text("Buat")',
                'button:has-text("Tambah")',
                'a[href*="/create"]'
            ]
            
            for selector in selectors:
                try:
                    if page.locator(selector).first.is_visible(timeout=1000):
                        page.click(selector)
                        time.sleep(2)
                        page.screenshot(path='../images/37-halaman-buat-rental.png')
                        print("✅ 37-halaman-buat-rental.png (halaman, bukan modal)")
                        screenshots.append(37)
                        break
                except:
                    continue
            else:
                print("⚠️  Rental: Button tidak ditemukan")
        except Exception as e:
            print(f"❌ Rental: {e}")
        
        print("\n✅ Selesai! Browser akan tetap terbuka 3 detik...")
        time.sleep(3)
        
        browser.close()
        
        print("\n" + "="*60)
        print(f"🎉 Total screenshot perbaikan: {len(screenshots)}")
        if screenshots:
            print(f"📸 Screenshot: {screenshots}")
        print("="*60)

if __name__ == '__main__':
    screenshot_missing_modals()
