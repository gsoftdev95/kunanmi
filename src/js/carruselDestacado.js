
const swiper = new Swiper('.highlightsSwiper', {
    loop: true, // 🔁 Activa el loop infinito

    slidesPerView: 'auto',
    spaceBetween: 10, //Espacio entre cada tarjeta
    slidesPerGroup: 1, //Se desliza de una tarjeta en una

    navigation: {
        nextEl: '.swiper-button-next', //
        prevEl: '.swiper-button-prev', //
    },

    //Ajustes responsive
    breakpoints: {
        0: {
            slidesPerView: 1.2,
        },
        450: {
            slidesPerView: 2.5,
        },
        768: {
            slidesPerView: 3.5,
        },
        1024: {
            slidesPerView: 4.5,
        },
        1400: {
            slidesPerView: 5.5,
        }
    }
});
