document.addEventListener("DOMContentLoaded", function() {

    // --- 1. HEADER LADEN ---
    const headerContainer = document.getElementById("header-placeholder");

    if (headerContainer) {
        // Prüfen ob wir auf der englischen Seite sind (falls du header-en.html hast)
        // Standardmäßig laden wir header.html
        let headerFile = "header.html";

        // Optional: Falls du data-lang="en" im Platzhalter nutzt
        if (headerContainer.getAttribute("data-lang") === "en") {
            headerFile = "header-en.html"; // Falls diese Datei existiert
        }

        fetch(headerFile)
            .then(response => {
                if (!response.ok) throw new Error("Header konnte nicht geladen werden.");
                return response.text();
            })
            .then(data => {
                // Wir erstellen das <header> Element, das in header.html fehlt
                const headerElement = document.createElement("header");
                headerElement.className = "header"; // Wichtig für dein CSS!
                headerElement.innerHTML = data;

                // Platzhalter durch den echten Header ersetzen
                headerContainer.replaceWith(headerElement);

                // WICHTIG: Erst JETZT das Mobile Menü aktivieren,
                // weil der Burger-Button vorher nicht existierte
                initMobileMenu();
            })
            .catch(error => console.error("Fehler beim Laden des Headers:", error));
    }

    // --- 2. FOOTER LADEN ---
    const footerContainer = document.getElementById("footer-placeholder");
    if (footerContainer) {
        const lang = footerContainer.getAttribute("data-lang");
        const footerFile = (lang === "en") ? "footer-en.html" : "footer.html";

        fetch(footerFile)
            .then(response => response.text())
            .then(data => footerContainer.innerHTML = data)
            .catch(error => console.error("Fehler beim Laden des Footers:", error));
    }
});

// Funktion für das Mobile Menü (ausgelagert)
function initMobileMenu() {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-list');

    if (burger && nav) {
        burger.addEventListener('click', () => {
            nav.classList.toggle('nav-active');
            burger.classList.toggle('toggle');
        });
    }
}