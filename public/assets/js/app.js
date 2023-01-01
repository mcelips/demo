$(function () {
    // Bootstrap Table
    $('.bs-table').bootstrapTable({
        pagination: true,
        pageList: [10, 25, 50, 100, 'All'],
        pageSize: 50,
        paginationVAlign: 'both',
        search: true,
        searchHighlight: true,
        showFullscreen: true,
        showRefresh: true,
        showSearchClearButton: true,
        showToggle: true,
        silentSort: true,
        sortable: true,
        sortStable: true,
    });

    // ScrollToFixed Enable
    $('.fixed').scrollToFixed();

    // Tooltip Enable
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});

    // Select2 Enable
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    // Bootstrap Switch
    $('input[data-bootstrap-switch]').each(function () {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

    // Bootstrap Custom File Input
    bsCustomFileInput.init();

    // проверка строки на JSON
    $('.json-check').on('input propertychange', function () {
        let json_string = $(this).val()
        let is_json = true
        // пробуем преобразовать строку в JSON
        try {
            JSON.parse(json_string)
        } catch (e) {
            is_json = false
        }

        // если все ок, убираем класс "невалиден", иначе добавляем класс
        if (is_json === true || json_string === '') {
            $(this).removeClass('is-invalid')
        } else {
            $(this).addClass('is-invalid')
        }
    })

    // горячие клавиши для сохранения формы
    document.onkeydown = (e) => {
        // Ctrl + Alt + W - сохранить и закрыть
        if (e.ctrlKey && e.altKey && (e.key === 'w' || e.key === 'W' || e.key === 'ц' || e.key === 'Ц')) {
            if ($('form #back_button')[0] !== undefined) {
                $('form #back_button')[0].click();
            }
            e.preventDefault();
        }

        // Ctrl + Alt + S - сохранить и закрыть
        if (e.ctrlKey && e.altKey && (e.key === 's' || e.key === 'S' || e.key === 'ы' || e.key === 'Ы')) {
            if ($('form [name="submit_close"]') !== undefined) {
                $('form [name="submit_close"]').click();
            }
            e.preventDefault();
        }

        // Ctrl + S - сохранить
        if (e.ctrlKey && (e.key === 's' || e.key === 'S' || e.key === 'ы' || e.key === 'Ы')) {
            if ($('form [name="submit"]') !== undefined) {
                $('form [name="submit"]').click();
            }
            e.preventDefault();
        }
    }

    // AJAX отправка формы
    let clickedSubmitButton = null
    $('[type="submit"]').click(function () {
        clickedSubmitButton = $(this)
    })
    $("form").submit(function (e) {
        e.preventDefault();

        let form = $(this);
        let actionUrl = form.attr('action')
        let formData = new FormData(this)

        // указываем какая кнопка была нажата
        formData.set(encodeURI(clickedSubmitButton.attr('name')), '1')

        // информируем об отправке данных на сервер
        toastr_info('Сохраняю...')

        // блокируем кнопки
        block_submit_buttons(true)

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.clear()
                let message = response.message[0].message ?? null
                if (response.status) {
                    // для проверки ссылки в сообщении
                    let regex = /^((http|https):\/\/)?(www\.)?([A-zА-я0-9][A-zА-я0-9\-]*\.?)*\.[A-zА-я0-9-]{2,8}(\/([\w#!:.?+=&%@!\-\/])*)?/i

                    // если пришла ссылка в ответ
                    if (regex.test(message)) {
                        // редирект на страницу по ссылке
                        window.location.replace(message)
                    } else {
                        // иначе показываем сообщение
                        toastr.success(message)
                    }
                    return true

                }
                // произошла ошибка
                toastr.error(message)
            },
            error: function () {
                // сообщение об ошибке
                toastr.clear()
                toastr.error('Не удалось сохранить данные. Ошибка сервера.')
            },
            complete: function () {
                // снимаем блокировку кнопки
                block_submit_buttons(false)
            }
        });
    });

    /**
     * Проверяем, заблокирована ли ссылка "Отмена"
     */
    $('#back_button').click(function(e) {
        if ($(this).hasClass('disabled')) {
            e.preventDefault();
        }
    });

    /**
     * Блокирует все кнопки типа submit
     *
     * @param need_to_block
     */
    function block_submit_buttons(need_to_block) {
        if (need_to_block) {
            $('#back_button').addClass('disabled')
        } else {
            $('#back_button').removeClass('disabled')
        }
        $('button[type="submit"]').prop('disabled', need_to_block)
    }

    /**
     * Выводит всплывающее информационно сообщение
     *
     * @param text
     * @param title
     */
    function toastr_info(text = 'Обрабатываю запрос...', title = '') {
        toastr.info(
            '<i class="fa fa-fw fa-spinner fa-spin"></i> ' + text, title,
            {showDuration: 10, hideDuration: 10, timeOut: 0, extendedTimeOut: 0}
        )
    }

    // выбор активного пункта меню в Sidebar
    let url = window.location;
    // for single sidebar menu
    $('ul.nav-sidebar a').each(function () {
        if (this.href === url || url.href.indexOf(this.href) > -1 || url.pathname.indexOf(this.href) > -1) {
            $('ul.nav-sidebar a').removeClass('active')
            $(this).addClass("active")
        }
    })

    // for sidebar menu and treeview
    $('ul.nav-treeview a').filter(function () {
        return (this.href === url || url.href.indexOf(this.href) > -1 || url.pathname.indexOf(this.href) > -1)
    }).parentsUntil(".nav-sidebar > .nav-treeview")
        .css({'display': 'block'})
        .addClass('menu-open').prev('a')
        .addClass('active');

    // category button
    let categoryLink = $('.nav-sidebar a').filter(function () {
        return (this.href === url || url.href.indexOf(this.href) > -1 || url.pathname.indexOf(this.href) > -1)
    }).closest('li.category')

    // change folder icon to open folder
    categoryLink.find('.fa-folder')
        .removeClass('fa-folder')
        .addClass('fa-folder-open')

    // change angle icon
    categoryLink.find('.fa-angle-left')
        .removeClass('fa-angle-left')
        .addClass('fa-angle-down')
});


/** ВЫВОД СООБЩЕНИЙ (.alert) */
let timerId;

// Скрываем сообщение по клику
$('#message').on('click', function () {
    hide_message(0);
});

// Очищаем область сообщений
function clear_message() {
    $('#message').removeClass('alert alert-danger alert-success alert-info')
        .html('')
        .show();
}

/**
 * Плавное скрытие сообщения.
 * @param delay Задержка до скрытия.
 */
function hide_message(delay = 3000) {
    // Проверяем запущен ли таймер
    if (typeof timerId !== 'undefined') {
        // Сбрасываем таймер
        clearTimeout(timerId);
    }
    // Записываем таймер
    timerId = setTimeout(function () {
        $('#message').fadeOut(300, function () {
            clear_message();
        });
    }, delay);
}

/**
 * Показывает сообщение
 * @param cls       Классы, которые необходимо добавить.
 * @param text      Текст сообщения
 * @param auto_hide Авто скрытие сообщения.
 */
function show_message(cls, text, auto_hide = true) {
    clear_message();
    $('#message').addClass('alert')
        .addClass(cls)
        .html(text);
    // Скрываем и очищаем сообщение
    if (auto_hide) {
        hide_message();
    }
}

/**
 * Показывает сообщение об успехе.
 * @param text
 */
function show_error(text) {
    show_message('alert-danger', text);
}

/**
 * Показывает сообщение об ошибке.
 * @param text
 */
function show_success(text) {
    show_message('alert-success', text);
}

/**
 * Показывает информационное сообщение.
 * @param text
 */
function show_info(text) {
    show_message('alert-info', text, false);
}

/**
 * Возвращает отформатированную строку, заменяя спецификатор на аргумент
 * @param format
 * @param args
 * @returns {string}
 */
function sprintf(format, ...args) {
    if ((typeof args[0] === 'object') && args[0] !== null) {
        for (let [key, value] of Object.entries(args[0])) {
            let regExp = new RegExp('%' + key + '', 'g'); // regex pattern string
            format = format.replace(regExp, value);
        }
        return format;
    } else {
        let i = 0;
        return format.replace(/%s/g, () => args[i++]);
    }
}