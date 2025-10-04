document.addEventListener("DOMContentLoaded", function() {
    const page = window.location.pathname.split("/").pop();

    const headers = {
        "about.html": { title: "About Us", lead: "Learn more about our mission." },
        "products.html": { title: "Our Products", lead: "Explore our products." },
        "services.html": { title: "Our Services", lead: "Discover our services." },
        "contacts.html": { title: "Contact Us", lead: "Get in touch with us." }
    };

    if (headers[page]) {
        document.getElementById("breadcrumb-page").innerText = headers[page].title;
        document.getElementById("header-title").innerText = headers[page].title;
        document.getElementById("header-lead").innerText = headers[page].lead;
    }
});