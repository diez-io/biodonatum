jQuery(function ($) {
    class Form {
        $form;
        $submit;
        $agreeCheckbox;
        $customCheckbox;
        errors;
        url;
        inputs;
        requiredInputs;
        $responseOutput;
        notValidTips;

        constructor($form) {
            this.$form = $form;
            this.$submit = this.$form.find('[type="submit"]');
            this.$agreeCheckbox = this.$form.find('[data-agree]');
            this.$customCheckbox = this.$form.find('[data-agree-custom]');
            this.url = this.$form.attr('action');
            this.inputs = this.$form.find('input, textarea');
            this.requiredInputs = this.inputs.filter('.input--required');
            this.errors = [];
            this.$responseOutput = this.$form.find('.wpcf7-response-output');
            this.notValidTips = this.$form.find('.wpcf7-not-valid-tip');
            this.init();
        }

        init() {
            this.changeBtnStatus();
            this.toggleAgree();

            this.$submit.on('click', (e) => {
                e.preventDefault();
                this.$responseOutput.hide();
                this.inputs.removeClass('wpcf7-not-valid');
                this.notValidTips.hide();
                this.validate();
            });

            this.$customCheckbox && this.$customCheckbox.on('click', e => {
                if (e.target.tagName === 'SPAN') {
                    // open privacy policy
                }
                else {
                    this.toggleAgreeCustom();
                }
            });

            this.inputs.on('input', () => this.changeBtnStatus());
        }

        validate() {
            let error = false; // Начальное значение false

            this.inputs.each((i, input) => {
                console.log(common.staticContent.fill_out_this_field);
                // Исключаем скрытые поля и те, которые не требуют валидации
                if (input.type === 'hidden' || input.name === 'AUTH_FORM' || input.name === 'TYPE' || input.name === 'backurl' || input.name === 'remember' || input.type === 'checkbox') {
                    return; // Пропускаем эти поля
                }
                // Проверяем длину значения
                if (input.value.length < 3) {
                    $(input).toggleClass('wpcf7-not-valid', true);
                    const $notValidTip = $(input).siblings('.wpcf7-not-valid-tip');
                    $notValidTip.text(common.staticContent.fill_out_this_field);
                    $notValidTip.show();
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

            this.inputs.each((i, input) => {
                if (input.name) {
                    formData.append(input.name, input.value);
                }
            });

            return formData;
        }

        sendData() {
            this.$form.toggleClass('submitting');

            $.ajax({
                url: this.url,
                method: "POST",
                data: this.getData(),
                processData: false,
                contentType: false,
                success: (response, status, xhr) => {
                    const contentType = xhr.getResponseHeader('Content-Type');

                    console.log(response);

                    if (contentType && contentType.includes('text/html')) {
                        console.warn('Received HTML instead of expected JSON');
                    }
                    else if (response.success) {
                        //this.resetInputs(); // Reset input values
                    }

                    if (response.redirect) {
                        location.href = response.redirect;
                    }

                    this.showResult(response);
                },
                error: (xhr, status, error) => {
                    console.error('Error during submission:', error);
                },
                complete: () => {
                    this.$form.removeClass('submitting');
                }
            });
        }

        showResult(response) {
            if (response.successComponent) {
                const $successComponent = $($.parseHTML(response.successComponent));
                const successFor = '.' + $successComponent.data('success-for');

                console.log('response.successComponent');

                if (this.$form.parent().is('.registration--registration, .lost-password__container')) {
                    console.log('attr logged-id');
                    $('.header__user').attr('logged-in', '');
                }

                $(successFor).replaceWith($successComponent);

                $successComponent.find('.registration__back').on('click', function() {
                    $(this).parent().hide();
                    $('.registration--login').show();
                });
            }
            else {
                if (response.message) {
                    this.$responseOutput.text(response.message);
                    this.$responseOutput.show();
                }

                if (response.errors) {
                    for (let [code, error] of Object.entries(response.errors)) {
                        const $notValidTip = this.notValidTips.filter('.error_' + code);
                        $notValidTip.siblings('.input').toggleClass('wpcf7-not-valid', true);
                        $notValidTip.text(error);
                        $notValidTip.show();
                    }
                }
            }
        }

        resetInputs() {
            this.inputs.forEach(input => {
                if (input.tagName === 'INPUT') {
                    const inputElement = input; // Явно указываем тип как HTMLInputElement
                    // Проверяем тип инпута
                    if (inputElement.type === 'checkbox') {
                        inputElement.checked = false; // Сбрасываем чекбоксы
                    } else {
                        inputElement.value = ''; // Обнуляем значение для текстовых полей
                    }
                } else if (input.tagName === 'TEXTAREA') {
                    const textareaElement = input; // Явно указываем тип как HTMLTextAreaElement
                    textareaElement.value = ''; // Обнуляем значение для текстовых областей
                }
            });
        }


        changeBtnStatus() {
            let isActive = true;

            if (this.$agreeCheckbox) {
                isActive = this.$agreeCheckbox.checked;
            }

            if (isActive) {
                this.requiredInputs.each(function () {
                    return (isActive = $(this).val() !== '');
                });
            }

            this.$submit[0].toggleAttribute('disabled', !isActive);
        }

        toggleAgree() {
            if (this.$agreeCheckbox) {
                this.$agreeCheckbox.on('change', () => {
                    this.changeBtnStatus();
                });
            }
        }

        toggleAgreeCustom() {
            this.$customCheckbox.find('svg').toggle();

            this.$agreeCheckbox.checked = !this.$agreeCheckbox.checked;
            this.changeBtnStatus();
        }
    }

    $('.custom-woocommerce-form').each(function() {
        new Form($(this));
    });

    $('.account__add-btn').on('click', function() {
        $(this).hide();
        $(this).siblings('#add_payment_method').show();
    });

    $('.account__edit-address-btn').on('click', function() {
        $(this).hide();
        $(this).siblings('address').hide();
        $(this).siblings('.account__edit-address').css('position', 'static');
    });
});
