import { removeClass } from "../helpers/removeClass";

class Tabs {
    el;
    headElements;
    contentElements;

    constructor(el: Element) {
        this.el = el;
        this.headElements = this.el.querySelectorAll('[data-tabs-head]');
        this.contentElements = this.el.querySelectorAll('[data-tabs-content]');

        this.init()
    }

    init() {
        this.changeTabs()
    }

    changeTabs() {
        this.headElements.forEach((head, idx) => {
            head.addEventListener('click', () => {
                removeClass(this.headElements)
                removeClass(this.contentElements)
                head.classList.add('active')
                this.contentElements.item(idx).classList.add('active')
            })
        })
    }
}

export default Tabs