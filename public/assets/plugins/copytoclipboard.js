// Copy to Clipboard from Input
function copyToClipboard(element_id) {
    document.getElementById(element_id).select();
    if (true === document.execCommand('copy')) {
        toastr.success('Текст успешно скопирован.', 'Успех')
    } else {
        toastr.error('Не удалось скопировать текст.', 'Ошибка')
    }
}