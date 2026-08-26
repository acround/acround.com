#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Резбайка — SEO-блок для одностраничного сайта на трёх языках.

Сербский, русский, английский — все три в одном index.html: переключение
идёт CSS-классами .lang-sr/.lang-ru/.lang-en (main.js меняет только
атрибут [lang] на <html>), без отдельных страниц под каждый язык. Поэтому
и SEO — один URL, один canonical, без hreflang: alternate нечему быть,
адрес один и тот же для всех языков. Сербский — язык по умолчанию, поэтому
структурированные данные и карточка бизнеса берут текст из его вариантов.

Запуск после любой правки текстов:

    python3 tools/seo.py

Собирает: sitemap.xml, robots.txt, блок для поиска (canonical, Open Graph,
JSON-LD) в index.html.
"""

import html as html_mod
import json
import os
import re

# ─────────────────────────────────────────────────────────────────────────
#  НАСТРОЙКА. Здесь всё, что зависит от вас.
# ─────────────────────────────────────────────────────────────────────────

SITE_URL = 'https://rezbajka.akround.com'   # ← ваш домен, без слэша на конце

LANG_TAG = 'sr-Latn'   # значение [lang] на <html> по умолчанию — язык сайта
LANG_LOCALES = {'sr': 'sr_Latn_RS', 'ru': 'ru_RU', 'en': 'en_US'}

# Для карточки бизнеса в поиске. Пустые поля просто не попадут в разметку —
# лучше пусто, чем выдумано.
BUSINESS = {
    'email':      '',                   # 'pochta@example.com'
    'telephone':  '',                   # '+381...'
    'city':       '',                   # 'Beograd'
    'country':    '',                   # 'RS' — код страны по ISO
    'areaServed': [],                   # ['RS', 'RU'] — где вы работаете
    'priceRange': '',                   # '$$' или диапазон — Google это показывает
    'sameAs':     [],                   # ссылки на соцсети и профили
}

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
MARK_OPEN = '<!-- seo:start -->'
MARK_CLOSE = '<!-- seo:end -->'


def read(rel):
    with open(os.path.join(ROOT, rel), encoding='utf-8') as f:
        return f.read()


def write(rel, text):
    path = os.path.join(ROOT, rel)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(text)


def strip_tags(s):
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', s)).strip()


# ─────────────────────────────────────────────────────────────────────────
#  Данные для разметки — берём прямо из страницы, сербский вариант
# ─────────────────────────────────────────────────────────────────────────

def collect_faq(html):
    """Вопросы и ответы для FAQPage. Источник один — сама страница, поэтому
    не расходится с тем, что видит посетитель."""
    qs = re.findall(
        r'class="faq__q"[^>]*>.*?<span class="lang-sr"[^>]*>(.*?)<span[^>]*class="faq__sign"',
        html, re.S)
    as_ = re.findall(
        r'<div class="faq__a"><div>\s*<span class="lang-sr"[^>]*>(.*?)</span>',
        html, re.S)
    return [(strip_tags(q), strip_tags(a)) for q, a in zip(qs, as_)]


def collect_services(html):
    return [strip_tags(t) for t in re.findall(
        r'<article class="card[^"]*"[^>]*>.*?<h3><span class="lang-sr"[^>]*>(.*?)</span>',
        html, re.S)]


# ─────────────────────────────────────────────────────────────────────────
#  Блок для поиска
# ─────────────────────────────────────────────────────────────────────────

def head_block(html):
    title = html_mod.unescape(re.search(r'<title\b[^>]*\bdata-sr="([^"]*)"', html).group(1))
    meta_tag = re.search(r'<meta\b[^>]*name="description"[^>]*>', html).group(0)
    desc = html_mod.unescape(re.search(r'\bdata-ct-sr="([^"]*)"', meta_tag).group(1))
    url = SITE_URL + '/'

    out = [MARK_OPEN,
           '<link rel="canonical" href="%s">' % url,
           '<meta property="og:site_name" content="Rezbajka">',
           '<meta property="og:type" content="website">',
           '<meta property="og:url" content="%s">' % url,
           '<meta property="og:title" content="%s">' % title,
           '<meta property="og:description" content="%s">' % desc,
           '<meta property="og:image" content="%s/assets/img/og.png">' % SITE_URL,
           '<meta property="og:image:width" content="1200">',
           '<meta property="og:image:height" content="630">',
           '<meta property="og:locale" content="%s">' % LANG_LOCALES['sr']]
    for code in ('ru', 'en'):
        out.append('<meta property="og:locale:alternate" content="%s">' % LANG_LOCALES[code])
    out += [
           '<meta name="twitter:card" content="summary_large_image">',
           '<meta name="twitter:title" content="%s">' % title,
           '<meta name="twitter:description" content="%s">' % desc,
           '<meta name="twitter:image" content="%s/assets/img/og.png">' % SITE_URL,
           '<meta name="robots" content="index, follow, max-image-preview:large">']

    business = {
        '@type': 'ProfessionalService',
        '@id': SITE_URL + '/#business',
        'name': 'Rezbajka',
        'url': url,
        'image': SITE_URL + '/assets/img/og.png',
        'description': desc,
        'inLanguage': LANG_TAG,
        'knowsLanguage': ['sr-Latn', 'ru', 'en'],
    }
    if BUSINESS['email']:
        business['email'] = BUSINESS['email']
    if BUSINESS['telephone']:
        business['telephone'] = BUSINESS['telephone']
    if BUSINESS['priceRange']:
        business['priceRange'] = BUSINESS['priceRange']
    if BUSINESS['sameAs']:
        business['sameAs'] = BUSINESS['sameAs']
    if BUSINESS['city'] or BUSINESS['country']:
        addr = {'@type': 'PostalAddress'}
        if BUSINESS['city']:
            addr['addressLocality'] = BUSINESS['city']
        if BUSINESS['country']:
            addr['addressCountry'] = BUSINESS['country']
        business['address'] = addr
    if BUSINESS['areaServed']:
        business['areaServed'] = [{'@type': 'Country', 'name': c} for c in BUSINESS['areaServed']]

    services = collect_services(html)
    if services:
        business['hasOfferCatalog'] = {
            '@type': 'OfferCatalog',
            'name': title,
            'itemListElement': [
                {'@type': 'Offer', 'itemOffered': {'@type': 'Service', 'name': s}}
                for s in services
            ],
        }

    graph = [business, {
        '@type': 'WebSite',
        '@id': SITE_URL + '/#website',
        'url': url,
        'name': business['name'],
        'inLanguage': LANG_TAG,
        'publisher': {'@id': SITE_URL + '/#business'},
    }]

    faq = collect_faq(html)
    if faq:
        graph.append({
            '@type': 'FAQPage',
            '@id': url + '#faq',
            'inLanguage': LANG_TAG,
            'mainEntity': [
                {'@type': 'Question', 'name': q,
                 'acceptedAnswer': {'@type': 'Answer', 'text': a}}
                for q, a in faq
            ],
        })

    ld = json.dumps({'@context': 'https://schema.org', '@graph': graph},
                    ensure_ascii=False, indent=1)
    out.append('<script type="application/ld+json">\n%s\n</script>' % ld)
    out.append(MARK_CLOSE)
    return '\n'.join(out)


def inject(html, block):
    if MARK_OPEN in html:
        return re.sub(re.escape(MARK_OPEN) + r'.*?' + re.escape(MARK_CLOSE),
                      lambda _: block, html, flags=re.S)
    return html.replace('</head>', block + '\n</head>', 1)


# ─────────────────────────────────────────────────────────────────────────
#  Сборка
# ─────────────────────────────────────────────────────────────────────────

def main():
    src = read('index.html')

    # Устаревшие og-теги из ручной разметки убираем: их место теперь в блоке
    src = re.sub(r'\n<meta property="og:[^>]*>', '', src)

    page = inject(src, head_block(src))
    write('index.html', page)

    write('sitemap.xml',
          '<?xml version="1.0" encoding="UTF-8"?>\n'
          '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
          '  <url>\n    <loc>%s/</loc>\n    <changefreq>monthly</changefreq>\n'
          '    <priority>1.0</priority>\n  </url>\n'
          '</urlset>\n' % SITE_URL)

    write('robots.txt',
          'User-agent: *\nAllow: /\n\nSitemap: %s/sitemap.xml\n' % SITE_URL)

    print('Собрано:')
    print('  index.html — блок для поиска обновлён')
    print('  sitemap.xml, robots.txt')
    print('  вопросов в разметке для поиска: %d' % len(collect_faq(page)))
    print('  услуг в карточке бизнеса: %d' % len(collect_services(page)))
    if SITE_URL.endswith('.example'):
        print('\n  ВНИМАНИЕ: в tools/seo.py стоит домен-заглушка.')
        print('  Впишите свой в SITE_URL и запустите скрипт заново.')


if __name__ == '__main__':
    main()
