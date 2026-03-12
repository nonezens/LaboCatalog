/* ========================================
   HEADER JS - SPA Navigation
   ======================================== */
document.addEventListener("DOMContentLoaded", () => {
    // 1. Intercept Navigation Clicks
    document.body.addEventListener("click", async (e) => {
        const link = e.target.closest("a.nav-link");
        
        // Ignore if it's not a nav link or opens in a new tab
        if (!link || link.target === "_blank") return;
        
        e.preventDefault();
        const url = link.href;

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
            window.location.href = url; 
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
            const activeNav = document.querySelector(`.nav-link[href="${window.location.pathname.split('/').pop()}"]`);
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

                    // Trigger the custom event so your other JS files wake up!
                    document.dispatchEvent(new Event('PageContentUpdated'));

                    // Fade it back in
                    container.classList.remove("fade-out");
                    container.classList.add("fade-in");
                    setTimeout(() => container.classList.remove("fade-in"), 300);
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