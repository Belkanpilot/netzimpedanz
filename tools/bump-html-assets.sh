#!/usr/bin/env bash
# Nach Änderungen an CSS/JS ausführen: setzt ?v= auf max. Änderungszeit aller Assets.
# So aktualisieren sich die Links in statischen *.html ohne manuelles Cache-Leeren.
set -euo pipefail
cd "$(dirname "$0")/.."
V="$(php -r '$m=0;foreach(["style.css","header.css","programm.css","legal.css","main.js"] as $f){if(is_file($f))$m=max($m,filemtime($f));}echo $m;')"
for f in index.html index-en.html programm.html programm-en.html danke.html danke-en.html \
         datenschutz.html impressum.html history.html history-en.html; do
  [[ -f "$f" ]] || continue
  sed -i '' \
    -e "s|href=\"style\\.css\"|href=\"style.css?v=$V\"|g" \
    -e "s|href=\"style\\.css?v=[0-9]*\"|href=\"style.css?v=$V\"|g" \
    -e "s|href=\"header\\.css\"|href=\"header.css?v=$V\"|g" \
    -e "s|href=\"header\\.css?v=[0-9]*\"|href=\"header.css?v=$V\"|g" \
    -e "s|href=\"programm\\.css\"|href=\"programm.css?v=$V\"|g" \
    -e "s|href=\"programm\\.css?v=[0-9]*\"|href=\"programm.css?v=$V\"|g" \
    -e "s|href=\"legal\\.css\"|href=\"legal.css?v=$V\"|g" \
    -e "s|href=\"legal\\.css?v=[0-9]*\"|href=\"legal.css?v=$V\"|g" \
    -e "s|src=\"main\\.js\"|src=\"main.js?v=$V\"|g" \
    -e "s|src=\"main\\.js?v=[0-9]*\"|src=\"main.js?v=$V\"|g" \
    "$f"
done
echo "HTML asset query v=$V"
