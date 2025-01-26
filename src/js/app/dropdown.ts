class Dropdown {

    el;
    triggerElements;

    constructor(el: Element) {
        this.el = el;
        this.triggerElements = this.el.querySelectorAll('[data-dropdown-trigger]');

        this.init()
    }

    init() {
        this.triggerElements.forEach(element => element.addEventListener('click', this.toggle));
    }

    toggle(e:Event) {
        (e.currentTarget as HTMLElement).classList.toggle('active');

    }
}

export default Dropdown;
