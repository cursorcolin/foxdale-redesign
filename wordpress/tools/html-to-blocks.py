#!/usr/bin/env python3
"""Convert the static Foxdale site pages into Gutenberg block markup.

Reads site/*.html, transforms the content between the header and footer
into serialized core blocks (group / heading / paragraph / image / list /
buttons), and writes wordpress/seed/pages/<slug>.html.

Image references become {{img:filename}} placeholders that the seed
importer (wordpress/seed/seed.php) resolves to theme asset URLs.
Internal links (x.html) become pretty permalinks (/x/).
"""

import json
import re
import sys
from pathlib import Path

from bs4 import BeautifulSoup, NavigableString, Tag

ROOT = Path(__file__).resolve().parent.parent.parent
SITE = ROOT / "site"
OUT = ROOT / "wordpress" / "seed" / "pages"

PAGES = {
    "index.html": "home",
    "life.html": "life",
    "residences.html": "residences",
    "healthcare.html": "healthcare",
    "campus.html": "campus",
    "planning.html": "planning",
    "visit.html": "visit",
    "about.html": "about",
    "careers.html": "careers",
    "giving.html": "giving",
}

BG_URL_RE = re.compile(r"background-image\s*:\s*url\(['\"]?([^'\")]+)['\"]?\)")


def rewrite_href(href: str) -> str:
    if not href:
        return href
    m = re.match(r"^([a-z-]+)\.html(#.*)?$", href)
    if m:
        slug, anchor = m.group(1), m.group(2) or ""
        if slug == "index":
            return "/" + anchor
        return f"/{slug}/{anchor}"
    return href


def rewrite_links(el: Tag) -> None:
    for a in el.find_all("a"):
        if a.get("href"):
            a["href"] = rewrite_href(a["href"])


def img_placeholder(src: str) -> str:
    name = src.split("/")[-1]
    prefix = "people/" if "/people/" in src else ""
    return "{{img:" + prefix + name + "}}"


def attrs_json(class_name=None, tag_name=None, level=None, extra=None, anchor=None):
    attrs = {}
    if tag_name and tag_name != "div":
        attrs["tagName"] = tag_name
    if level is not None and level != 2:
        attrs["level"] = level
    if anchor:
        attrs["anchor"] = anchor
    if class_name:
        attrs["className"] = class_name
    if extra:
        attrs.update(extra)
    if not attrs:
        return ""
    return " " + json.dumps(attrs, ensure_ascii=False, separators=(",", ":"))


def inner_html(el: Tag) -> str:
    """Inline content of an element with links rewritten."""
    rewrite_links(el)
    return el.decode_contents().strip()


def classes(el: Tag) -> list:
    return el.get("class") or []


def class_str(el: Tag) -> str:
    return " ".join(classes(el))


def is_btn(el) -> bool:
    return isinstance(el, Tag) and el.name == "a" and any(c.startswith("btn") for c in classes(el))


def button_block(a: Tag) -> str:
    style = next((c for c in classes(a) if c.startswith("btn-")), "btn-solid")
    href = rewrite_href(a.get("href", "#"))
    target = ' target="_blank" rel="noreferrer noopener"' if a.get("target") == "_blank" else ""
    label = a.decode_contents().strip()
    return (
        f'<!-- wp:button {{"className":"{style}"}} -->\n'
        f'<div class="wp-block-button {style}"><a class="wp-block-button__link wp-element-button" '
        f'href="{href}"{target}>{label}</a></div>\n'
        f"<!-- /wp:button -->"
    )


def buttons_block(btns: list, class_name=None, center=False) -> str:
    extra = {"layout": {"type": "flex", "justifyContent": "center"}} if center else None
    attrs = attrs_json(class_name=class_name, extra=extra)
    cls = "wp-block-buttons" + (f" {class_name}" if class_name else "")
    inner = "\n\n".join(button_block(b) for b in btns)
    return f"<!-- wp:buttons{attrs} -->\n<div class=\"{cls}\">{inner}</div>\n<!-- /wp:buttons -->"


def paragraph_block(el: Tag, class_name=None) -> str:
    cls = class_name if class_name is not None else class_str(el) or None
    attrs = attrs_json(class_name=cls)
    cls_attr = f' class="{cls}"' if cls else ""
    return f"<!-- wp:paragraph{attrs} -->\n<p{cls_attr}>{inner_html(el)}</p>\n<!-- /wp:paragraph -->"


def heading_block(el: Tag) -> str:
    level = int(el.name[1])
    cls = class_str(el) or None
    attrs = attrs_json(class_name=cls, level=level)
    cls_attr = " wp-block-heading" + (f" {cls}" if cls else "")
    return (
        f"<!-- wp:heading{attrs} -->\n"
        f'<h{level} class="{cls_attr.strip()}">{inner_html(el)}</h{level}>\n'
        f"<!-- /wp:heading -->"
    )


def image_block(img: Tag, class_name=None) -> str:
    src = img_placeholder(img.get("src", ""))
    alt = img.get("alt", "")
    attrs = attrs_json(class_name=class_name)
    cls_attr = f' class="wp-block-image {class_name}"' if class_name else ' class="wp-block-image"'
    return (
        f"<!-- wp:image{attrs} -->\n"
        f'<figure{cls_attr}><img src="{src}" alt="{alt}"/></figure>\n'
        f"<!-- /wp:image -->"
    )


def list_block(ul: Tag) -> str:
    items = []
    for li in ul.find_all("li", recursive=False):
        items.append(
            f"<!-- wp:list-item -->\n<li>{inner_html(li)}</li>\n<!-- /wp:list-item -->"
        )
    inner = "\n\n".join(items)
    return f'<!-- wp:list -->\n<ul class="wp-block-list">{inner}</ul>\n<!-- /wp:list -->'


def shortcode_block(code: str) -> str:
    return f"<!-- wp:shortcode -->\n{code}\n<!-- /wp:shortcode -->"


FORM_PLACEHOLDER = (
    '<!-- wp:group {"className":"form-placeholder"} -->\n'
    '<div class="wp-block-group form-placeholder"><!-- wp:paragraph -->\n'
    "<p><strong>Form goes here.</strong> Install a form plugin (WPForms or Gravity Forms), "
    "rebuild the tour-request form (Name, Email, Phone, \u201cI\u2019m interested in\u2026\u201d "
    "dropdown including the brochure-by-mail option, Message), then replace this placeholder "
    "with the plugin\u2019s form block.</p>\n"
    "<!-- /wp:paragraph --></div>\n"
    "<!-- /wp:group -->"
)

IMG_ONLY_WRAPPERS = {"photo", "thumb", "avatar"}


def group_block(el: Tag) -> str:
    cls_list = [c for c in classes(el)]
    tag = el.name if el.name in ("section", "article", "figure") else "div"

    # Background-image style becomes an absolutely positioned hero image.
    bg_block = None
    style = el.get("style", "")
    m = BG_URL_RE.search(style)
    if m:
        fake = BeautifulSoup(f'<img src="{m.group(1)}" alt="">', "html.parser").img
        bg_block = image_block(fake, class_name="hero-bg")

    children_blocks = []
    if bg_block:
        children_blocks.append(bg_block)
    children_blocks.extend(transform_children(el))

    cls = " ".join(cls_list) or None
    anchor = el.get("id")
    attrs = attrs_json(class_name=cls, tag_name=tag, anchor=anchor)
    cls_attr = "wp-block-group" + (f" {cls}" if cls else "")
    id_attr = f' id="{anchor}"' if anchor else ""
    inner = "\n\n".join(children_blocks)
    return (
        f"<!-- wp:group{attrs} -->\n"
        f'<{tag}{id_attr} class="{cls_attr}">{inner}</{tag}>\n'
        f"<!-- /wp:group -->"
    )


def transform_children(parent: Tag) -> list:
    """Transform child elements, batching consecutive buttons."""
    blocks = []
    pending_btns = []

    def flush_btns(center=False):
        nonlocal pending_btns
        if pending_btns:
            blocks.append(buttons_block(pending_btns, center=center))
            pending_btns = []

    for child in parent.children:
        if isinstance(child, NavigableString):
            if child.strip():
                blocks.append(paragraph_block(BeautifulSoup(f"<p>{child}</p>", "html.parser").p))
            continue
        if not isinstance(child, Tag):
            continue
        if is_btn(child):
            pending_btns.append(child)
            continue
        flush_btns()
        block = transform_element(child)
        if block:
            blocks.append(block)

    centered = "tight" in classes(parent) or "actions" in classes(parent)
    flush_btns(center=centered)
    return blocks


def transform_element(el: Tag):
    cls = classes(el)
    name = el.name

    # Skipped / special-cased chunks
    if "plans" in cls or "lightbox" in cls:
        return None
    if "plan-filters" in cls:
        return shortcode_block("[foxdale_floor_plans]")
    if name == "form":
        return FORM_PLACEHOLDER
    if name == "script":
        return None

    if name in ("h1", "h2", "h3", "h4", "h5", "h6"):
        return heading_block(el)
    if name == "p":
        return paragraph_block(el)
    if name == "ul":
        return list_block(el)
    if name == "img":
        return image_block(el)
    if is_btn(el):
        return buttons_block([el])
    if name == "span":
        return paragraph_block(el)
    if name == "a":
        # Standalone non-button anchors (doc list entries) become paragraphs.
        rewrite_links(el)
        cls_s = class_str(el) or None
        attrs = attrs_json(class_name=cls_s)
        cls_attr = f' class="{cls_s}"' if cls_s else ""
        return f"<!-- wp:paragraph{attrs} -->\n<p{cls_attr}>{el.decode()}</p>\n<!-- /wp:paragraph -->"

    if name in ("div", "section", "article", "figure", "footer", "header"):
        # Wrapper divs holding only an image become image blocks.
        tags = [c for c in el.children if isinstance(c, Tag)]
        if (
            len(tags) == 1
            and tags[0].name == "img"
            and (set(cls) & IMG_ONLY_WRAPPERS or name == "figure")
            and not el.get("style")
        ):
            return image_block(tags[0], class_name=class_str(el) or None)
        # actions wrapper -> buttons row
        if "actions" in cls and all(is_btn(t) for t in tags):
            return buttons_block(tags, class_name="actions", center=False)
        return group_block(el)

    # Fallback: treat unknown elements as paragraphs of their content.
    return paragraph_block(el)


def convert(path: Path) -> str:
    soup = BeautifulSoup(path.read_text(encoding="utf-8"), "html.parser")
    body = soup.body
    blocks = []
    for child in body.children:
        if not isinstance(child, Tag):
            continue
        cls = classes(child)
        if "utility" in cls or "lightbox" in cls:
            continue
        if child.name in ("header", "footer", "script"):
            continue
        block = transform_element(child)
        if block:
            blocks.append(block)
    return "\n\n".join(blocks) + "\n"


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    for src, slug in PAGES.items():
        out = OUT / f"{slug}.html"
        out.write_text(convert(SITE / src), encoding="utf-8")
        print(f"{src} -> {out.relative_to(ROOT)}")


if __name__ == "__main__":
    sys.exit(main())
