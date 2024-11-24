import Slider from "./slider";
import Form from "./form";
import inputmask from "inputmask";
import Tabs from "./tabs";
import MobileMenu from "./mobileMenu";
class App {
    constructor() {
        this.init();
    }
    init = () => {
        this.createSlider()
        this.createRequest()
        this.createMask()
        this.createTabs()
        this.createMobileMenu()
    }

    createSlider = () => {
        const sliders = document.querySelectorAll('[data-slider]')
        if (!sliders) return
        sliders.forEach(slider => {
            new Slider(slider)
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
                inputmask({ "mask": "+7 (999) 999-99-99" }).mask(input);
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

    createMobileMenu = () => {
        const menu = document.querySelector('[data-menu]');

        if (!menu) return

        new MobileMenu(menu)
    }


}

export { App };

