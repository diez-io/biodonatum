class Popup {
    btn:HTMLElement;
    body:HTMLElement;
    popup:HTMLElement;

    constructor(el: HTMLElement) {
        this.popup = el;
        this.btn = el.querySelector('.popup-btn');
        this.body = el.querySelector('.popup-body');

        this.init()
    }

    init() {
        this.btn.addEventListener('click', this.toggle);
        document.body.addEventListener('click', this.hide);
    }

    toggle = () => {
        this.body.classList.toggle('active');
    };

    hide = (e:MouseEvent) => {
        if (e.target != this.popup && !this.popup.contains(e.target as Node)) {
            this.body.classList.remove('active');
        }
    }
}

export default Popup
