import axios from "axios";

class Form {
    el;
    submit;
    agreeCheckbox: HTMLInputElement;
    customCheckbox: HTMLElement;
    url;
    inputs;

    constructor(el: Element) {
        this.el = el;
        this.submit = this.el.querySelector('button[type="submit"]');
        this.agreeCheckbox = this.el.querySelector('[data-agree]');
        this.customCheckbox = this.el.querySelector('[data-agree-custom]');
        this.url = this.el.getAttribute('action');
        this.inputs = [...Array.from(this.el.querySelectorAll('input')),
        ...Array.from(this.el.querySelectorAll('textarea'))];
        this.init();
    }

    init() {
        this.changeBtnStatus();
        this.toggleAgree();

        this.submit.addEventListener('click', (evt) => {
            evt.preventDefault();
            this.validate();
        });

        this.customCheckbox && this.customCheckbox.addEventListener('click', e => {
            if ((e.target as HTMLElement).tagName === 'SPAN') {
                // open privacy policy
            }
            else {
                this.toggleAgreeCustom();
            }
        });
    }

    validate() {
        let error = false; // Начальное значение false

        this.inputs.forEach((input) => {
            // Исключаем скрытые поля и те, которые не требуют валидации
            if (input.type === 'hidden' || input.name === 'AUTH_FORM' || input.name === 'TYPE' || input.name === 'backurl' || input.name === 'remember' || input.type === 'checkbox') {
                return; // Пропускаем эти поля
            }
            // Проверяем длину значения
            if (input.value.length < 3) {
                console.log(`Поле с названием ${input.name} содержит слишком мало символов (${input.value.length})`);
                error = true;
            }
            // Проверяем, если inputmask существует и заполнено ли поле полностью
            else if (input.inputmask && !input.inputmask.isComplete()) {
                console.log(`Поле с названием ${input.name} содержит незавершённый ввод`);
                error = true;
            }
        });
        // Если ошибок нет, отправляем данные
        if (!error) {
            this.sendData()
        }
    }

    getData() {
        const formData = new FormData();
        this.inputs.forEach(input => {
            if (input.name) {
                formData.append(input.name, input.value);
            }
        });
        return formData;
    }

    sendData() {
        axios.post(this.url, this.getData())
            .then(response => {
                console.log(response.data); // View response data
                if (response.headers['content-type'].includes('text/html')) {
                    console.warn('Received HTML instead of expected JSON');
                } else {
                    if (response.data.status === "success") {
                        const successDiv = this.el.querySelector('[data-success]') as HTMLElement;
                        if (successDiv) {
                            successDiv.style.display = 'block'; // Show success element in the current form
                        }
                        this.resetInputs(); // Reset input values
                    }
                }
            })
            .catch(error => {
                console.error('Error during submission:', error);
            });
    }

    resetInputs() {
        this.inputs.forEach(input => {
            if (input.tagName === 'INPUT') {
                const inputElement = input as HTMLInputElement; // Явно указываем тип как HTMLInputElement
                // Проверяем тип инпута
                if (inputElement.type === 'checkbox') {
                    inputElement.checked = false; // Сбрасываем чекбоксы
                } else {
                    inputElement.value = ''; // Обнуляем значение для текстовых полей
                }
            } else if (input.tagName === 'TEXTAREA') {
                const textareaElement = input as HTMLTextAreaElement; // Явно указываем тип как HTMLTextAreaElement
                textareaElement.value = ''; // Обнуляем значение для текстовых областей
            }
        });
    }


    changeBtnStatus() {
        if (this.agreeCheckbox) {
            if (this.agreeCheckbox.checked) {
                this.submit.removeAttribute('disabled');
            }
            else {
                this.submit.setAttribute('disabled', 'true');
            }
        }
    }

    toggleAgree() {
        if (this.agreeCheckbox) {
            this.agreeCheckbox.addEventListener('change', () => {
                this.changeBtnStatus();
            });
        }
    }

    toggleAgreeCustom() {
        this.customCheckbox.querySelectorAll('svg').forEach(svg => {
            svg.style.display = svg.style.display === 'none' ? '' : 'none';
        });

        this.agreeCheckbox.checked = !this.agreeCheckbox.checked;
        this.changeBtnStatus();
    }
}

export default Form;
