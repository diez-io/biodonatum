import Slider from "./slider";
import Form from "./form";
import inputmask from "inputmask";
import Tabs from "./tabs";
import Dropdown from "./dropdown";
import MobileMenu from "./mobileMenu";
import LoadMore from "./loadMore";
import Popup from "./popup";
import TypeAnimation from "./typeAnimation";
import VideoAnimation from "./videoAnimation";


class App {
    videoAnimation: VideoAnimation;

    constructor() {
        this.videoAnimation = null;

        this.init();
    }
    init = () => {
        this.createSlider();
        this.createRequest();
        this.createMask();
        this.createTabs();
        this.createMobileMenu();
        this.createLoadMore();
        this.createPopup();
        this.createDropdown();
        this.createTypeAnimation();
        this.createVideoAnimation();
        this.initScrollToTopBtn();
    }

    initScrollToTopBtn = () => {
        const scrollToTopBtn = document.querySelector('.scroll-up-btn') as HTMLElement;

        window.addEventListener('scroll', () => {
            scrollToTopBtn.style.display = window.scrollY > 200 ? 'flex' : '';
        });

        scrollToTopBtn.addEventListener('click', () => {
            if (this.videoAnimation) {
                this.videoAnimation.scrollToTop();
            }

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    createVideoAnimation = () => {
        const element = document.querySelector('.video-animation');

        if (element) {
            this.videoAnimation = new VideoAnimation(element);
        }
    }

    createTypeAnimation = () => {
        const elements = document.querySelectorAll('.type-animation');

        elements && elements.forEach(element => {
            new TypeAnimation(element);
        });
    }

    createPopup = () => {
        const popups = document.querySelectorAll('.popup');

        popups && popups.forEach(popup => {
            new Popup(popup as HTMLElement);
        });
    }

    createLoadMore = () => {
        const loadMoreBtns: NodeListOf<HTMLElement> = document.querySelectorAll('.load-more-btn');

        loadMoreBtns && loadMoreBtns.forEach(element => {
            new LoadMore(element);
        });
    };

    createSlider = () => {
        const sliders = document.querySelectorAll('[data-slider]')
        if (!sliders) return
        sliders.forEach(slider => {
            new Slider(slider as HTMLElement)
        })
    }

    createRequest = () => {
        const forms = document.querySelectorAll('[data-form]');
        if (!forms) return

        forms.forEach(form => {
            new Form(form)
        })
    }

    createMask = () => {
        const inputs = document.querySelectorAll('input');

        inputs.forEach(input => {
            if (input.type === 'tel') {
                inputmask({ "mask": "+9{1,3} (999) 999-99-99" }).mask(input);
            } else if (input.hasAttribute('data-email')) {
                inputmask({
                    mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
                    definitions: {
                        '*': {
                            validator: "[A-Za-z0-9_!#$%&'*+/=?^_`{|}~\\-]",
                            cardinality: 1,
                        },
                    },
                    greedy: false,
                    onBeforePaste: function (pastedValue, opts) {
                        pastedValue = pastedValue.toLowerCase();
                        return pastedValue.replace("mailto:", "");
                    },
                }).mask(input);
            }
        })
    }

    createTabs = () => {
        const tabs = document.querySelectorAll('[data-tabs]')
        if (!tabs) return
        tabs.forEach(tab => {
            new Tabs(tab)
        })
    }


    createDropdown = () => {
        const dropdown = document.querySelectorAll('[data-dropdown]')
        if (!dropdown) return
        dropdown.forEach(el => {
            new Dropdown(el)
        })
    }

    createMobileMenu = () => {
        const menu = document.querySelector('[data-menu]');

        if (!menu) return

        new MobileMenu(menu)
    }


}

export { App };

