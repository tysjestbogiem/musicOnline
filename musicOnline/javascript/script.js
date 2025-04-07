new Swiper('.card-wrapper', {
    loop: false,
    spaceBetween: 20,

    // Pagination bullets
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true
    },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    slidesPerView: "auto", 
    centeredSlides: false, 

    breakpoints: {
        0: { slidesPerView: 1, spaceBetween: 5 },
        500: { slidesPerView: 2, spaceBetween: 6 },
        700: { slidesPerView: 3, spaceBetween: 7 },
        885: { slidesPerView: 4, spaceBetween: 8 },
        1080: { slidesPerView: 5, spaceBetween: 9 },
        1200: { slidesPerView: 6, spaceBetween: 10 }
    }
    
});


// this is to scrolle nav bar 
window.addEventListener("scroll", function () {
    let header = document.getElementById("header");
    
    if (window.scrollY > 1) { // When scrolled 100px down
        header.style.position = "fixed";
        header.style.top = "6px";
        header.style.width = "96%";
        header.style.left = "50%";
        header.style.transform = "translateX(-50%)";
        header.style.zIndex = "1000";
    } else {
        header.style.position = "absolute";
    }
});

// open mobile menu
document.addEventListener("DOMContentLoaded", function () {
    let menuButton = document.querySelector(".hamburger-menu");
    let closeButton = document.querySelector(".close-menu");
    let navLinks = document.querySelector(".nav-links");
    let mobileView = document.querySelector(".mobile-view");

    // Open menu on hamburger click
    menuButton.addEventListener("click", function () {
        navLinks.classList.add("active");
        mobileView.classList.add("menu-open"); 
    });

    // Close menu on "X" button click
    closeButton.addEventListener("click", function () {
        navLinks.classList.remove("active");
        mobileView.classList.remove("menu-open"); 
    });
});



function navigateToMenu(select) {
    let url = select.value;
    if (url) {
        window.location.href = url;
    }
}



