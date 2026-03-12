/* ========================================
   HEADER JS - SPA Navigation & Routing
   ======================================== */
document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger-menu");
    const navLinks = document.getElementById("nav-links");

    // Hamburger menu toggle
    if (hamburger) {
        hamburger.addEventListener("click", function() {
            navLinks.classList.toggle("active");
            hamburger.classList.toggle("open");
        });
    }

    // 1. Intercept Navigation Clicks
    document.body.addEventListener("click", async (e) => {
        const link = e.target.closest("a.nav-link");
        
        // Ignore if it's not a nav link, opens in a new tab, or is an external link
        if (!link || link.target === "_blank" || link.getAttribute('href').startsWith('http')) return;
        
        e.preventDefault();
        const url = link.href;

        // Close mobile menu on click
        if (navLinks.classList.contains("active")) {
            navLinks.classList.remove("active");
            hamburger.classList.remove("open");
        }

        // Update URL bar
        window.history.pushState({ path: url }, "", url);

        // Execute swap
        await loadPage(url, link);
    });

    // 2. Handle Browser Back/Forward Buttons
    window.addEventListener("popstate", () => {
        loadPage(window.location.href, null);
    });

    // 3. The Content Swapping Logic
    async function loadPage(url, clickedLink) {
        const container = document.getElementById("main-content");
        if (!container) {
            window.location.href = url; // Fallback hard redirect
            return;
        }

        // Start fade-out
        container.classList.add("fade-out");

        // Highlight the clicked button immediately
        document.querySelectorAll(".nav-link").forEach(nav => nav.classList.remove("active-page"));
        if (clickedLink) {
            clickedLink.classList.add("active-page");
        } else {
            // Highlight correct link if back button was used
            const activeNav = document.querySelector(`.nav-link[href*="${window.location.pathname.split('/').pop()}"]`);
            if(activeNav) activeNav.classList.add("active-page");
        }

        try {
            const response = await fetch(url);
            const html = await response.text();

            // Parse the new HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newContent = doc.getElementById("main-content");

            if (newContent) {
                setTimeout(() => {
                    // Swap the content
                    container.innerHTML = newContent.innerHTML;
                    document.title = doc.title;

                    // WAKE UP OTHER SCRIPTS! Trigger custom event
                    document.dispatchEvent(new Event('PageContentUpdated'));

                    // Fade it back in
                    container.classList.remove("fade-out");
                    container.classList.add("fade-in");
                    setTimeout(() => container.classList.remove("fade-in"), 300);
                    
                    // Scroll to top smoothly
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 300); 
            } else {
                window.location.href = url;
            }
        } catch (error) {
            console.error("AJAX Load Error:", error);
            window.location.href = url;
        }
    }
});