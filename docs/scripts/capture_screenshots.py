"""
APYRent Documentation Screenshot Automation
Captures pages and auto-annotates with red circles
"""

import time
from pathlib import Path
from playwright.sync_api import sync_playwright
from PIL import Image, ImageDraw, ImageFont
from screenshot_config import SCREENSHOTS

# Configuration
BASE_URL = "http://127.0.0.1:8000"
LOGIN_EMAIL = "test@example.com"
LOGIN_PASSWORD = "admin123"
OUTPUT_DIR = Path(__file__).parent.parent / "images"
OUTPUT_DIR.mkdir(exist_ok=True)


def annotate_image(image_path, annotations):
    """Add red circles and numbering to screenshot"""
    img = Image.open(image_path)
    draw = ImageDraw.Draw(img)
    
    try:
        font = ImageFont.truetype("arial.ttf", 28)
    except:
        font = ImageFont.load_default()
    
    for anno in annotations:
        if anno["type"] == "circle":
            x, y = anno["x"], anno["y"]
            radius = 45
            
            # Draw thick red circle
            for i in range(5):
                draw.ellipse(
                    [(x - radius - i, y - radius - i), 
                     (x + radius + i, y + radius + i)],
                    outline="red",
                    width=4
                )
            
            # Draw number if provided
            if "number" in anno:
                # White background
                draw.ellipse(
                    [(x - 18, y - 18), (x + 18, y + 18)],
                    fill="white",
                    outline="red",
                    width=2
                )
                
                # Red number
                number_text = str(anno["number"])
                bbox = draw.textbbox((0, 0), number_text, font=font)
                text_width = bbox[2] - bbox[0]
                text_height = bbox[3] - bbox[1]
                
                draw.text(
                    (x - text_width // 2, y - text_height // 2 - 2),
                    number_text,
                    fill="red",
                    font=font
                )
        
        elif anno["type"] == "rectangle":
            x1, y1 = anno["x1"], anno["y1"]
            x2, y2 = anno["x2"], anno["y2"]
            
            # Draw thick red rectangle
            for i in range(4):
                draw.rectangle(
                    [(x1 - i, y1 - i), (x2 + i, y2 + i)],
                    outline="red",
                    width=3
                )
    
    img.save(image_path, quality=95)
    print(f"  ✓ Annotated")


def login(page):
    """Login to APYRent"""
    print("🔐 Logging in...")
    page.goto(f"{BASE_URL}/login")
    page.fill("input[type='email']", LOGIN_EMAIL)
    page.fill("input[type='password']", LOGIN_PASSWORD)
    page.click("button:has-text('Masuk')")
    page.wait_for_url(f"{BASE_URL}/admin/dashboard", timeout=10000)
    print("  ✓ Login successful\n")


def capture_screenshot(page, config):
    """Navigate and capture screenshot"""
    name = config["name"]
    url = config["url"]
    wait_for = config.get("wait_for", ".content-wrapper")
    action = config.get("action")
    
    print(f"📸 Capturing: {name}")
    
    # Navigate
    page.goto(url, timeout=15000)
    
    # Wait for element
    try:
        page.wait_for_selector(wait_for, timeout=10000)
    except:
        print(f"  ⚠ Warning: {wait_for} not found")
    
    # Perform action if specified
    if action == "click_edit_first":
        try:
            page.click("a[href*='edit']:first, button:has-text('Edit'):first")
            time.sleep(1)
        except:
            print(f"  ⚠ Warning: Could not click edit")
    
    elif action == "click_delete_first":
        try:
            page.click("button.delete:first, a.delete:first")
            time.sleep(1)
        except:
            print(f"  ⚠ Warning: Could not click delete")
    
    elif action == "click_perpanjang_first":
        try:
            page.click("button:has-text('Perpanjang'):first, a:has-text('Perpanjang'):first")
            time.sleep(1)
        except:
            print(f"  ⚠ Warning: Could not click perpanjang")
    
    # Take screenshot
    output_path = OUTPUT_DIR / name
    page.screenshot(path=str(output_path))
    print(f"  ✓ Saved: {name}")
    
    return output_path


def main():
    """Main automation"""
    print("=" * 60)
    print("APYRent Screenshot Automation - BAB 1-6")
    print("=" * 60)
    print()
    
    # Check if APYRent is running
    import requests
    try:
        requests.get(BASE_URL, timeout=5)
        print(f"✓ APYRent is running at {BASE_URL}\n")
    except:
        print(f"✗ ERROR: APYRent not accessible at {BASE_URL}")
        print("  Please start APYRent first.\n")
        return
    
    with sync_playwright() as p:
        print("🌐 Launching browser...")
        browser = p.chromium.launch(headless=False)
        context = browser.new_context(
            viewport={"width": 1280, "height": 720}
        )
        page = context.new_page()
        
        # Login
        login(page)
        
        # Capture screenshots
        total = len(SCREENSHOTS)
        captured = 0
        annotated = 0
        
        for i, config in enumerate(SCREENSHOTS, 1):
            print(f"[{i}/{total}] ", end="")
            
            try:
                # Capture
                output_path = capture_screenshot(page, config)
                captured += 1
                
                # Annotate
                if config.get("annotations"):
                    annotate_image(output_path, config["annotations"])
                    annotated += 1
                
                time.sleep(0.5)
                
            except Exception as e:
                print(f"  ✗ ERROR: {str(e)}")
                continue
            
            print()
        
        browser.close()
        
        # Summary
        print("=" * 60)
        print("📊 Summary:")
        print(f"  Total: {total}")
        print(f"  Captured: {captured}")
        print(f"  Annotated: {annotated}")
        print(f"  Failed: {total - captured}")
        print(f"  Output: {OUTPUT_DIR}")
        print("=" * 60)


if __name__ == "__main__":
    main()
