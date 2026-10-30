"""
Script untuk screenshot modal-modal penting di semua modul APYRent
"""
from playwright.sync_api import sync_playwright
import time

def screenshot_all_modals():
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
        
        # BAB 3: PAJAK KENDARAAN
        print("📋 BAB 3: Pajak Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/pajak-kendaraan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Pajak
            page.click('button:has-text("Tambah Pajak")')
            time.sleep(1)
            page.screenshot(path='../images/29-modal-tambah-pajak.png')
            print("✅ 29-modal-tambah-pajak.png")
            screenshots.append(29)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Pajak: {e}")
        
        # BAB 4: ASURANSI
        print("\n📋 BAB 4: Asuransi Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/asuransi-kendaraan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Asuransi
            page.click('button:has-text("Tambah Asuransi")')
            time.sleep(1)
            page.screenshot(path='../images/30-modal-tambah-asuransi.png')
            print("✅ 30-modal-tambah-asuransi.png")
            screenshots.append(30)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Asuransi: {e}")
        
        # BAB 5: GPS
        print("\n📋 BAB 5: GPS Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/gps-kendaraan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah GPS
            page.click('button:has-text("Tambah GPS")')
            time.sleep(1)
            page.screenshot(path='../images/31-modal-tambah-gps.png')
            print("✅ 31-modal-tambah-gps.png")
            screenshots.append(31)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  GPS: {e}")
        
        # BAB 6: KIR
        print("\n📋 BAB 6: KIR Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/kir')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah KIR
            page.click('button:has-text("Tambah KIR")')
            time.sleep(1)
            page.screenshot(path='../images/32-modal-tambah-kir.png')
            print("✅ 32-modal-tambah-kir.png")
            screenshots.append(32)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  KIR: {e}")
        
        # BAB 7: LAPORAN KEHILANGAN
        print("\n📋 BAB 7: Laporan Kehilangan")
        try:
            page.goto('http://127.0.0.1:8000/admin/kehilangan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Lapor Kehilangan
            page.click('button:has-text("Lapor Kehilangan"), button:has-text("Tambah Laporan")')
            time.sleep(1)
            page.screenshot(path='../images/33-modal-lapor-kehilangan.png')
            print("✅ 33-modal-lapor-kehilangan.png")
            screenshots.append(33)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Kehilangan: {e}")
        
        # BAB 8: PEMBAYARAN
        print("\n📋 BAB 8: Pembayaran")
        try:
            page.goto('http://127.0.0.1:8000/admin/pembayaran')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Buat Purchase Order
            page.click('button:has-text("Buat PO"), button:has-text("Tambah"), a:has-text("Buat Purchase Order")')
            time.sleep(1)
            page.screenshot(path='../images/34-modal-buat-po.png')
            print("✅ 34-modal-buat-po.png")
            screenshots.append(34)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Pembayaran: {e}")
        
        # BAB 9: MEMBER
        print("\n📋 BAB 9: Data Member")
        try:
            page.goto('http://127.0.0.1:8000/admin/members')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Member
            page.click('button:has-text("Tambah Member")')
            time.sleep(1)
            page.screenshot(path='../images/35-modal-tambah-member.png')
            print("✅ 35-modal-tambah-member.png")
            screenshots.append(35)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Member: {e}")
        
        # BAB 10: PELANGGAN
        print("\n📋 BAB 10: Data Pelanggan")
        try:
            page.goto('http://127.0.0.1:8000/admin/pelanggan')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Pelanggan
            page.click('button:has-text("Tambah Pelanggan"), button:has-text("Tambah Customer")')
            time.sleep(1)
            page.screenshot(path='../images/36-modal-tambah-pelanggan.png')
            print("✅ 36-modal-tambah-pelanggan.png")
            screenshots.append(36)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Pelanggan: {e}")
        
        # BAB 11: BOOKING RENTAL
        print("\n📋 BAB 11: Booking & Rental")
        try:
            page.goto('http://127.0.0.1:8000/admin/rental')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Buat Booking
            page.click('button:has-text("Buat Rental"), button:has-text("Tambah Booking"), a:has-text("Buat Rental")')
            time.sleep(1)
            page.screenshot(path='../images/37-modal-buat-rental.png')
            print("✅ 37-modal-buat-rental.png")
            screenshots.append(37)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Rental: {e}")
        
        # BAB 12: HISTORY PERPANJANGAN
        print("\n📋 BAB 12: History Perpanjangan")
        try:
            page.goto('http://127.0.0.1:8000/admin/pajak-kendaraan/history')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            page.screenshot(path='../images/38-history-perpanjangan.png')
            print("✅ 38-history-perpanjangan.png")
            screenshots.append(38)
        except Exception as e:
            print(f"⚠️  History: {e}")
        
        # BAB 13: SERVICE KENDARAAN
        print("\n📋 BAB 13: Service Kendaraan")
        try:
            page.goto('http://127.0.0.1:8000/admin/service-history')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Service
            page.click('button:has-text("Tambah Service"), a:has-text("Tambah Service")')
            time.sleep(1)
            page.screenshot(path='../images/39-modal-tambah-service.png')
            print("✅ 39-modal-tambah-service.png")
            screenshots.append(39)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Service: {e}")
        
        # BAB 14: DATA LEASING
        print("\n📋 BAB 14: Data Leasing")
        try:
            page.goto('http://127.0.0.1:8000/admin/data-leasing')
            page.wait_for_load_state('networkidle')
            time.sleep(1)
            
            # Modal Tambah Leasing
            page.click('button:has-text("Tambah Leasing"), button:has-text("Tambah Data")')
            time.sleep(1)
            page.screenshot(path='../images/40-modal-tambah-leasing.png')
            print("✅ 40-modal-tambah-leasing.png")
            screenshots.append(40)
            page.keyboard.press('Escape')
            time.sleep(0.5)
        except Exception as e:
            print(f"⚠️  Leasing: {e}")
        
        print("\n✅ Selesai! Browser akan tetap terbuka 3 detik...")
        time.sleep(3)
        
        browser.close()
        
        print("\n" + "="*60)
        print(f"🎉 Total screenshot berhasil: {len(screenshots)}")
        print(f"📸 Screenshot ke-{screenshots[0]} sampai ke-{screenshots[-1]}")
        print("="*60)

if __name__ == '__main__':
    screenshot_all_modals()
