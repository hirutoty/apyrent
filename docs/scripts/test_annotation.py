"""
Test script: Annotate existing screenshots
"""

from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

# Paths
IMAGES_DIR = Path(__file__).parent.parent / "images"
OUTPUT_DIR = IMAGES_DIR / "annotated"
OUTPUT_DIR.mkdir(exist_ok=True)

def annotate_login_page():
    """
    Annotate login page screenshot
    """
    img_path = IMAGES_DIR / "01-login-page.png"
    if not img_path.exists():
        print(f"✗ File not found: {img_path}")
        return
    
    print(f"📸 Annotating: {img_path.name}")
    
    img = Image.open(img_path)
    draw = ImageDraw.Draw(img)
    
    # Try to load Arial font
    try:
        font = ImageFont.truetype("arial.ttf", 28)
    except:
        font = ImageFont.load_default()
    
    # Annotations for login page
    annotations = [
        {"x": 520, "y": 285, "number": 1, "label": "Email"},
        {"x": 520, "y": 340, "number": 2, "label": "Password"},
        {"x": 575, "y": 425, "number": 3, "label": "Masuk"},
    ]
    
    for anno in annotations:
        x, y = anno["x"], anno["y"]
        radius = 45
        
        # Draw red circle (thick outline)
        for i in range(5):
            draw.ellipse(
                [(x - radius - i, y - radius - i), 
                 (x + radius + i, y + radius + i)],
                outline="red",
                width=4
            )
        
        # Draw white circle background for number
        draw.ellipse(
            [(x - 18, y - 18), (x + 18, y + 18)],
            fill="white",
            outline="red",
            width=2
        )
        
        # Draw number
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
    
    # Save annotated image
    output_path = OUTPUT_DIR / f"annotated-{img_path.name}"
    img.save(output_path, quality=95)
    print(f"✓ Saved: {output_path}")
    print(f"  Size: {output_path.stat().st_size // 1024} KB")


def annotate_dashboard():
    """
    Annotate dashboard screenshot
    """
    img_path = IMAGES_DIR / "02-dashboard.png"
    if not img_path.exists():
        print(f"✗ File not found: {img_path}")
        return
    
    print(f"📸 Annotating: {img_path.name}")
    
    img = Image.open(img_path)
    draw = ImageDraw.Draw(img)
    
    try:
        font = ImageFont.truetype("arial.ttf", 28)
    except:
        font = ImageFont.load_default()
    
    # Annotations for dashboard
    annotations = [
        {"x": 100, "y": 100, "number": 1, "label": "Sidebar Menu"},
        {"x": 1180, "y": 50, "number": 2, "label": "User Menu"},
    ]
    
    for anno in annotations:
        x, y = anno["x"], anno["y"]
        radius = 45
        
        # Draw red circle
        for i in range(5):
            draw.ellipse(
                [(x - radius - i, y - radius - i), 
                 (x + radius + i, y + radius + i)],
                outline="red",
                width=4
            )
        
        # Draw white circle for number
        draw.ellipse(
            [(x - 18, y - 18), (x + 18, y + 18)],
            fill="white",
            outline="red",
            width=2
        )
        
        # Draw number
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
    
    # Save
    output_path = OUTPUT_DIR / f"annotated-{img_path.name}"
    img.save(output_path, quality=95)
    print(f"✓ Saved: {output_path}")
    print(f"  Size: {output_path.stat().st_size // 1024} KB")


if __name__ == "__main__":
    print("=" * 60)
    print("APYRent Screenshot Annotation Test")
    print("=" * 60)
    print()
    
    annotate_login_page()
    print()
    annotate_dashboard()
    
    print()
    print("=" * 60)
    print("✓ Annotation test complete!")
    print(f"  Output: {OUTPUT_DIR}")
    print("=" * 60)
