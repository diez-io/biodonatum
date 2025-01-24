class Dropdown {
    triggers: Element[];

    constructor(rootElement: Element) {
        // Находим все триггеры внутри переданного элемента
        this.triggers = Array.from(rootElement.querySelectorAll('[data-dropdown-trigger]'));
        this.init();
    }

    init() {
        // Для каждого триггера добавляем обработчик событий
        this.triggers.forEach(trigger => {
            trigger.addEventListener('click', () => this.toggle(trigger));
        });
    }

    toggle(trigger: Element) {
        // Переключаем состояние конкретного триггера
        if (trigger.classList.contains('active')) {
            trigger.classList.remove('active');
        } else {
            trigger.classList.add('active');
        }
    }
}

export default Dropdown;
