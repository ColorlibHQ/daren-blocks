"""Turn the raw captures from shoot.mjs into the product page's JPEGs.

    python3 .dev/publish/finish.py

Everything is 1140px wide (the card is 1200x800), progressive JPEG q82. The
palette sheet is six tiles of the same screen, two across, each labelled.
"""
import os
from PIL import Image, ImageDraw, ImageFont

HERE = os.path.dirname(os.path.abspath(__file__))
RAW = os.path.join(HERE, 'raw')
OUT = os.path.join(HERE, 'images')
os.makedirs(OUT, exist_ok=True)
SLUG = 'daren'


def save(img, name):
    path = os.path.join(OUT, name)
    img.convert('RGB').save(path, 'JPEG', quality=82, optimize=True, progressive=True)
    print(name, img.size, os.path.getsize(path) // 1024, 'KB')


def width(img, w=1140):
    return img.resize((w, round(img.height * w / img.width)), Image.LANCZOS)


card = Image.open(os.path.join(RAW, 'card.png'))
save(card.resize((1200, 800), Image.LANCZOS), f'{SLUG}-free-blog-wordpress-theme.jpg')

for raw, name in [
    ('home', 'home'),
    ('story-layouts', 'story-layouts'),
    ('dark-mode', 'dark-mode'),
    ('contact-form', 'contact-form'),
    ('blog', 'blog'),
    ('single-post', 'single-post'),
]:
    save(width(Image.open(os.path.join(RAW, raw + '.png'))), f'{SLUG}-block-theme-{name}.jpg')

# Palettes: 2 x 3 tiles.
names = ['scarlet', 'cobalt', 'jade', 'tangerine', 'orchid', 'midnight']
gap, cols = 12, 2
tile_w = (1140 - gap * (cols - 1)) // cols
tiles = []
for n in names:
    im = Image.open(os.path.join(RAW, f'palette-{n}.png'))
    im = im.crop((110, 0, 1330, 734))
    tiles.append((n, width(im, tile_w)))
tile_h = tiles[0][1].height
rows = (len(tiles) + cols - 1) // cols
sheet = Image.new('RGB', (1140, rows * tile_h + gap * (rows - 1)), (214, 214, 214))
try:
    font = ImageFont.truetype('/System/Library/Fonts/Supplemental/Arial Bold.ttf', 17)
except OSError:
    font = ImageFont.load_default()
draw = ImageDraw.Draw(sheet)
for i, (n, im) in enumerate(tiles):
    x = (i % cols) * (tile_w + gap)
    y = (i // cols) * (tile_h + gap)
    sheet.paste(im, (x, y))
    label = n.capitalize()
    tw = draw.textlength(label, font=font)
    bx, by = x + tile_w - tw - 26, y + tile_h - 38
    draw.rounded_rectangle((bx, by, bx + tw + 18, by + 28), radius=6, fill=(20, 20, 20))
    draw.text((bx + 9, by + 5), label, font=font, fill='white')
save(sheet, f'{SLUG}-block-theme-colour-palettes.jpg')
