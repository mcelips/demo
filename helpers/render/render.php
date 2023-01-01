<?php

helpers([
    'files.path_to_file',
]);

/**
 * @param string      $view
 * @param array       $params
 * @param bool|string $layout
 * @param string|null $folder
 *
 * @return void
 */
function render(
    $view,
    $params = [],
    $layout = 'default',
    $folder = null
)
{
    // файл вида
    $view_file = ($folder !== null)
        ? path_to_file(sprintf('template/%s/%s', $folder, $view))
        : path_to_file(sprintf('template/%s', $view));

    if ($view_file === false) {
        $view = ($folder !== null)
            ? str_replace('//', '/', str_replace('.', '/', sprintf('template/%s/%s', $folder, $view)))
            : str_replace('//', '/', str_replace('.', '/', sprintf('template/%s', $view)));

        throw new DomainException("Render error! View '$view' not found.");
    }

    // необходимо загрузить только вид
    if ($layout === false) {
        extract($params);
        require_once $view_file;

        return;
    }

    // файл слоя
    $layout_file = ($folder !== null)
        ? path_to_file(sprintf('template/%s/layout_%s', $folder, $layout))
        : path_to_file(sprintf('template/layout_%s', $layout));


    if ($layout_file === false) {
        $layout = ($folder !== null)
            ? sprintf('template/%s/layout_%s', $folder, $layout)
            : sprintf('template/layout_%s', $layout);

        throw new DomainException("Render error! Layout '$layout' not found.");
    }

    ob_start();
    extract($params);
    require_once $view_file;
    $content = ob_get_clean();
    require_once $layout_file;
}


/**
 * @param string      $view
 * @param array       $params
 * @param string|null $folder
 *
 * @return void
 */
function render_component($view, $params = [], $folder = null)
{
    render($view, $params, false, $folder);
}
