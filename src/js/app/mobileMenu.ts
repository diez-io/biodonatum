class MobileMenu {
    el;
    burger;

    constructor(el: Element) {
        this.el = el;
        this.burger = document.querySelector('[data-burger]');

        this.init()
    }

    init() {
        this.burger.addEventListener('click', this.toggleMenu.bind(this))
    }

    toggleMenu() {
        this.burger.classList.toggle('active')
        this.el.classList.toggle('active')
    }
}

export default MobileMenu