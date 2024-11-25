class Dropdown {
    el;
    triggerElement;

    constructor(el: Element) {
        this.el = el;
        this.triggerElement = this.el.querySelector('[data-dropdown-trigger]');

        this.init()
    }

    init() {
        this.triggerElement.addEventListener('click', this.toggle.bind(this))
    }

    toggle() {
        if (this.el.classList.contains('active')) {
            this.el.classList.remove('active')
        } else {
            this.el.classList.add('active')
        }
    }
}

export default Dropdown