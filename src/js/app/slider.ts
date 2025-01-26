import Swiper from "swiper";
import { Navigation, Pagination, Thumbs, FreeMode } from "swiper/modules";
import { SlidesPerViewType } from "../helpers/types";

class Slider {
    el: HTMLElement;
    sliderType: string;
    slidesCount: SlidesPerViewType;
    buttonPrev: HTMLElement;
    buttonNext: HTMLElement;
    pagination: HTMLElement;
    desktopOnly;
    media;
    sliderInitialized: boolean;

    constructor(el: HTMLElement) {
        this.el = el;
        this.sliderType = this.el.getAttribute('data-slider');
        this.slidesCount = parseInt(this.el.getAttribute('data-slides'));

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
            case 'scientists':
                this.initScientistsSlider();
                break;
            case 'tabs':
                this.initTabsSliderConditionally();
                break;
        }
    }

    initTabsSliderConditionally() {
        if (window.innerWidth < 1199) {
            this.initTabsSlider();
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1199 && !this.sliderInitialized) {
                this.initTabsSlider();
                this.sliderInitialized = true;
            } else if (window.innerWidth >= 1199 && this.sliderInitialized) {
                this.destroyTabsSlider();
                this.sliderInitialized = false;
            }
        });
    }

    initTabsSlider() {
        //const slider: HTMLElement = this.el.querySelector('.swiper');

        new Swiper(this.el, {
            modules: [Navigation, Pagination, FreeMode],
            slidesPerView: 1.5,
            spaceBetween: 17.5,
            freeMode: true,
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            breakpoints: {
                700: {
                    slidesPerView: 2,
                },
                800: {
                    slidesPerView: 2.5,
                },
                900: {
                    slidesPerView: 3,
                },
            }
        });
        this.sliderInitialized = true;
    }

    destroyTabsSlider() {
        const sliderElement = this.el.querySelector('.swiper') as HTMLElement & { swiper?: Swiper };
        if (sliderElement?.swiper) {
            sliderElement.swiper.destroy(true, true);
        }
    }



    initScientistsSlider() {
        const slider: HTMLElement = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            allowTouchMove: false, // Disable swiping
            pagination: {
                el: this.pagination,
                clickable: true,
                enabled: false,
            },
            navigation: {
                prevEl: this.buttonPrev,
                nextEl: this.buttonNext,
                disabledClass: 'slider__btn--disabled',
                enabled: false,
            },
            spaceBetween: 30,
            breakpoints: {
                1199: {
                    slidesPerView: 4,
                    allowTouchMove: true,
                    pagination: {
                        enabled: true,
                    },
                    navigation: {
                        enabled: true,
                    },

                }
            }
        })
    }
    initProductDetailSlider() {
        const slider: HTMLElement = this.el.querySelector('.swiper');
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
        const slider: HTMLElement = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Pagination, FreeMode],
            slidesPerView: 2.5,
            freeMode: true,
            pagination: {
                el: this.pagination,
                clickable: true,
            },
            breakpoints: {
                400: {
                    slidesPerView: 3,
                },
                700: {
                    slidesPerView: 3.5,
                },
                800: {
                    slidesPerView: 4,
                },
                1199: {
                    slidesPerView: this.slidesCount ?? 1,
                },
            }
        })
    }

    initPartnersSlider() {
        const slider: HTMLElement = this.el.querySelector('.swiper');
        new Swiper(slider, {
            modules: [Navigation, Pagination, FreeMode],
            slidesPerView: 2.5,
            spaceBetween: 20,
            freeMode: true,
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
                700: {
                    slidesPerView: 3,
                },
                800: {
                    slidesPerView: 3.5,
                },
                900: {
                    slidesPerView: 4,
                },
                1199: {
                    slidesPerView: 6,
                },
            }
        }
        )
    }

    initDefaultSlider() {
        const slider: HTMLElement = this.el.querySelector('.swiper');
        const swiperOptions = {
            modules: [Navigation],
            slidesPerView: 'auto' as SlidesPerViewType,
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
        const slider: HTMLElement = this.el.querySelector('.swiper');
        const thumb = document.querySelector('[data-slider="thumb"]');
        const thumbMobile = document.querySelector('[data-slider-mob]');
        const thumbSwiper: HTMLElement = thumb.querySelector('.swiper');

        let thumbSlider = null

        if (this.media.matches) {
            thumbSlider = new Swiper(thumbMobile.querySelector('.swiper') as HTMLElement, {
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