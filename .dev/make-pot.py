#!/usr/bin/env python3
"""
Write languages/daren.pot from the theme's PHP.

`wp i18n make-pot` does this properly and is what to use where WP-CLI is
installed. This is the fallback for a machine without it: it reads the
gettext calls the theme actually uses — __(), _e(), esc_html__(), esc_html_e(),
esc_attr__(), esc_attr_e() and _n() — with single-quoted strings, and carries
each `translators:` comment across. It refuses a call whose text domain is not
`daren`, which is the mistake a port makes.

Usage:  python3 .dev/make-pot.py
"""

import glob
import os
import re
import sys
import time

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
os.chdir(ROOT)

STR = r"'((?:[^'\\]|\\.)*)'"
SINGLE = re.compile(r"\b(__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*" + STR + r"\s*,\s*" + STR + r"\s*\)")
PLURAL = re.compile(r"\b_n\(\s*" + STR + r"\s*,\s*" + STR + r"\s*,[^,]+,\s*" + STR + r"\s*\)")
COMMENT = re.compile(r"/\*\s*translators:(.*?)\*/", re.S)


def unescape(text):
    return text.replace("\\'", "'").replace("\\\\", "\\")


def po(text):
    return '"' + text.replace("\\", "\\\\").replace('"', '\\"').replace("\n", "\\n") + '"'


entries = {}
problems = []
files = sorted(glob.glob("functions.php") + glob.glob("inc/*.php"))

for path in files:
    source = open(path, encoding="utf-8").read()
    comments = [(m.end(), " ".join(m.group(1).split())) for m in COMMENT.finditer(source)]

    def note_for(pos):
        before = [c for end, c in comments if end <= pos and source.count("\n", end, pos) <= 2]
        return before[-1] if before else None

    for m in SINGLE.finditer(source):
        text, domain = unescape(m.group(2)), m.group(3)
        line = source.count("\n", 0, m.start()) + 1
        if domain != "daren":
            problems.append("%s:%d text domain %r" % (path, line, domain))
            continue
        key = (text, None)
        entry = entries.setdefault(key, {"refs": [], "note": None})
        entry["refs"].append("%s:%d" % (path, line))
        entry["note"] = entry["note"] or note_for(m.start())

    for m in PLURAL.finditer(source):
        single, plural, domain = unescape(m.group(1)), unescape(m.group(2)), m.group(3)
        line = source.count("\n", 0, m.start()) + 1
        if domain != "daren":
            problems.append("%s:%d text domain %r" % (path, line, domain))
            continue
        entry = entries.setdefault((single, plural), {"refs": [], "note": None})
        entry["refs"].append("%s:%d" % (path, line))
        entry["note"] = entry["note"] or note_for(m.start())

if problems:
    sys.exit("Wrong text domain:\n  " + "\n  ".join(problems))

version = re.search(r"^Version:\s*(\S+)", open("style.css", encoding="utf-8").read(), re.M).group(1)
out = [
    "# Copyright (C) %s Colorlib" % time.strftime("%Y"),
    "# This file is distributed under the GNU General Public License v2 or later.",
    'msgid ""',
    'msgstr ""',
    '"Project-Id-Version: Daren %s\\n"' % version,
    '"Report-Msgid-Bugs-To: https://colorlib.com/wp/themes/daren/\\n"',
    '"MIME-Version: 1.0\\n"',
    '"Content-Type: text/plain; charset=UTF-8\\n"',
    '"Content-Transfer-Encoding: 8bit\\n"',
    '"POT-Creation-Date: %s\\n"' % time.strftime("%Y-%m-%dT%H:%M:%S+00:00", time.gmtime()),
    '"X-Domain: daren\\n"',
    "",
]
for (text, plural), entry in entries.items():
    if entry["note"]:
        out.append("#. translators: " + entry["note"])
    out.append("#: " + " ".join(entry["refs"]))
    if "%" in text:
        out.append("#, php-format")
    out.append("msgid " + po(text))
    if plural is None:
        out.append('msgstr ""')
    else:
        out.append("msgid_plural " + po(plural))
        out.append('msgstr[0] ""')
        out.append('msgstr[1] ""')
    out.append("")

os.makedirs("languages", exist_ok=True)
with open("languages/daren.pot", "w", encoding="utf-8") as fh:
    fh.write("\n".join(out))
print("languages/daren.pot: %d strings from %d files" % (len(entries), len(files)))
