import Swiper from "swiper";
import { Navigation, Pagination, Thumbs } from "swiper/modules";

class Slider {
    el;
    sliderType;
    buttonPrev;
    buttonNext;
    slidesCount;
    pagination;
    desktopOnly;
    media;

    constructor(el: Element) {
        this.el = el;
        this.sliderType = this.el.getAttribute('data-slider');
        this.slidesCount = this.el.getAttribute('data-slides')

        this.buttonPrev = this.el.querySelector('.slider__btn--prev');
        this.buttonNext = this.el.querySelector('.slider__btn--next');
        this.pagination = this.el.querySelector('.swiper-pagination');

        this.media = matchMedia('(max-width: 1199px)');
        this.desktopOnly = this.el.hasAttribute('data-desktop-only');

        this.init()
    }

    init() {
        switch (this.sliderType) {
            case 'detail':
                this.initProductDetailSlider();
                break;
            case 'detail-teasers':
                this.initDetailTeasersSlider();
                break;
            case 'partners':
                this.initPartnersSlider();
                break;
            case 'default':
                this.initDefaultSlider();
                break;
            case 'thumbs':
                this.initThumbsSlider();
                break;
        }
    }
    initProductDetailSlider() {
        const slider = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Navigation, Pagination],
            slidesPerView: 'auto',
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            navigation: {
                prevEl: this.buttonPrev,
                nextEl: this.buttonNext,
                disabledClass: 'slider__btn--disabled'
            },
            breakpoints: {
                1199: {
                    slidesPerView: this.slidesCount ? this.slidesCount : 1,
                }
            }
        })
    }
    initDetailTeasersSlider() {
        const slider = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Pagination],
            slidesPerView: 2.5,
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            breakpoints: {
                1199: {
                    slidesPerView: this.slidesCount ? this.slidesCount : 1,
                }
            }
        })
    }

    initPartnersSlider() {
        const slider = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Navigation, Pagination],
            slidesPerView: 2.5,
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            navigation: {
                prevEl: this.buttonPrev,
                nextEl: this.buttonNext,
                disabledClass: 'slider__btn--disabled'
            },
            breakpoints: {
                1199: {
                    slidesPerView: this.slidesCount ? this.slidesCount : 1,
                }
            }
        }
        )
    }

    initDefaultSlider() {
        const slider = this.el.querySelector('.swiper');
        const swiperOptions = {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween: 30,
            navigation: {
                prevEl: this.buttonPrev,
                nextEl: this.buttonNext,
                disabledClass: 'slider__btn--disabled'
            },
            breakpoints: {
                1199: {
                    slidesPerView: this.slidesCount ? this.slidesCount : 1,
                }
            }
        }

        // Проверяем на наличие атрибута desktopOnly, если есть, тогда проверяем попадает ли в разрешение, если нет, просто инитим слайдер
        let swiperSlider = this.desktopOnly ? !this.media.matches ? new Swiper(slider, swiperOptions) : null : new Swiper(slider, swiperOptions);

        this.media.addEventListener('change', (event) => {
            const { matches } = event;

            if (matches && this.desktopOnly) {
                swiperSlider.destroy(true, true)
            } else {
                swiperSlider = new Swiper(slider, swiperOptions)
            }
        })
    }

    initThumbsSlider() {
        const slider = this.el.querySelector('.swiper');
        const thumb = document.querySelector('[data-slider="thumb"]');
        const thumbMobile = document.querySelector('[data-slider-mob]');
        const thumbSwiper = thumb.querySelector('.swiper');

        let thumbSlider = null

        if (this.media.matches) {
            thumbSlider = new Swiper(thumbMobile.querySelector('.swiper'), {
                slidesPerView: 1,
                spaceBetween: 0,
            })
        } else {
            thumbSlider = new Swiper(thumbSwiper, {
                slidesPerView: 1,
                spaceBetween: 0,
            })
        }

        new Swiper(slider, {
            modules: [Navigation, Pagination, Thumbs],
            slidesPerView: this.slidesCount ? this.slidesCount : 1,
            spaceBetween: 30,
            navigation: {
                prevEl: this.buttonPrev,
                nextEl: this.buttonNext,
            },
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            thumbs: {
                swiper: thumbSlider,
            },
        })
    }
}

export default Slider