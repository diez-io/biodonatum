import Typed from "typed.js";

class TypeAnimation {

    el: Element;
    strings: Element;
    typed: Element;

    constructor(el: Element) {
        this.el = el;
        this.strings = this.el.querySelector('.type-animation__strings');
        this.typed = this.el.querySelector('.type-animation__typed');

        this.init()
    }

    init() {
        new Typed(this.typed, {
            stringsElement: this.strings,
            typeSpeed: 30,
            backSpeed: 27,
            backDelay: 2500,
            loop: true,
            loopCount: Infinity,
        });
    }
}

export default TypeAnimation;
