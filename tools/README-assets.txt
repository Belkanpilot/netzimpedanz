Cache-Busting (ohne dass Nutzer den Browser-Cache leeren müssen)
================================================================

1) Apache: .htaccess setzt für .css, .js, .html, .php „no-cache, must-revalidate“.
   Ohne mod_headers greifen die Zeilen nicht – dann Nginx/Hosting-Panel analog setzen.

2) PHP-Seiten (registration*.php, dashboard.php): inc/assets.php hängt ?v=Dateiänderungszeit
   an style.css, main.js usw. – automatisch bei jedem geänderten Asset.

3) Statische *.html: ./tools/bump-html-assets.sh ausführen nach CSS/JS-Änderungen,
   damit sich ?v= in den Links erhöht (ein Commit mit aktualisierten HTML reicht).

4) CDN/Proxy vor dem Server: dort ggf. Cache leeren oder kurze TTL für /style.css etc.
