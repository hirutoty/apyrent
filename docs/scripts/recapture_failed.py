"""
Recapture only screenshots that are too small (< 15KB = likely 404 pages)
"""

import time
from pathlib import Path
from playwright.sync_api import sync_playwright
from PIL import Image, ImageDraw, ImageFont
from screenshot_config import SCREENSHOTS

BASE_URL = "http://127.0.0.1:8000"
LOGIN_EMAIL = "test@example.com"
LOGIN_PASSWORD = "admin123"
OUTPUT_DIR = Path(__file__).parent.parent / "images"

# Screenshots under this size are likely 404 pages
MIN_VALID_SIZE_KB = 15


def get_failed_screenshots():
    """Find screenshots that are too small (likely 404)"""
    failed = []
    for config in SCREENSHOTS:
        path = OUTPUT_DIR / config["name"]
        if path.exists():
            size_kb = path.stat().st_size / 1024
            if size_kb < MIN_VALID_SIZE_KB:
                failed.append(config)
                print(f"  ⚠ {config['name']} ({size_kb:.1f} KB) - needs recapture")
        else:
            failed.append(config)
            print(f"  ✗ {config['name']} - missing")
    return failed


def annotate_image(image_path, annotations):
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
            for i in range(5):
                draw.ellipse(
                    [(x - radius - i, y - radius - i),
                     (x + radius + i, y + radius + i)],
                    outline="red", width=4
                )
            if "number" in anno:
                draw.ellipse([(x - 18, y - 18), (x + 18, y + 18)],
                             fill="white", outline="red", width=2)
                number_text = str(anno["number"])
                bbox = draw.textbbox((0, 0), number_text, font=font)
                tw = bbox[2] - bbox[0]
                th = bbox[3] - bbox[1]
                draw.text((x - tw // 2, y - th // 2 - 2), number_text, fill="red", font=font)

        elif anno["type"] == "rectangle":
            x1, y1, x2, y2 = anno["x1"], anno["y1"], anno["x2"], anno["y2"]
            for i in range(4):
                draw.rectangle([(x1 - i, y1 - i), (x2 + i, y2 + i)], outline="red", width=3)

    img.save(image_path, quality=95)


def login(page):
    page.goto(f"{BASE_URL}/login")
    page.fill("input[type='email']", LOGIN_EMAIL)
    page.fill("input[type='password']", LOGIN_PASSWORD)
    page.click("button:has-text('Masuk')")
    page.wait_for_url(f"{BASE_URL}/admin/dashboard", timeout=10000)
    print("  ✓ Login OK\n")


def main():
    print("=" * 60)
    print("Recapture Failed Screenshots (< 15KB)")
    print("=" * 60)

    print("\nScanning for failed screenshots...")
    failed = get_failed_screenshots()

    if not failed:
        print("\n✅ All screenshots look OK! Nothing to recapture.")
        return

    print(f"\nFound {len(failed)} screenshots to recapture.\n")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False)
        context = browser.new_context(viewport={"width": 1280, "height": 720})
        page = context.new_page()

        print("🔐 Logging in...")
        login(page)

        recaptured = 0
        for i, config in enumerate(failed, 1):
            name = config["name"]
            url = config["url"]
            wait_for = config.get("wait_for", ".content-wrapper")

            print(f"[{i}/{len(failed)}] 📸 {name}")
            print(f"  URL: {url}")

            try:
                page.goto(url, timeout=15000)
                try:
                    page.wait_for_selector(wait_for, timeout=8000)
                except:
                    print(f"  ⚠ Selector '{wait_for}' not found, continuing...")

                time.sleep(0.8)

                output_path = OUTPUT_DIR / name
                page.screenshot(path=str(output_path))
                size_kb = output_path.stat().st_size / 1024

                if size_kb < MIN_VALID_SIZE_KB:
                    print(f"  ✗ Still too small ({size_kb:.1f} KB) - page may still be 404")
                else:
                    print(f"  ✓ Saved ({size_kb:.1f} KB)")
                    if config.get("annotations"):
                        annotate_image(output_path, config["annotations"])
                        print(f"  ✓ Annotated")
                    recaptured += 1

                print()

            except Exception as e:
                print(f"  ✗ ERROR: {e}\n")

        browser.close()

    print("=" * 60)
    print(f"Recaptured: {recaptured}/{len(failed)}")
    print("=" * 60)


if __name__ == "__main__":
    main()
