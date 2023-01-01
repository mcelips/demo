<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= config('app.name') ?> | Панель управления</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpg" href="<?= asset_url('images/favicon.jpg') ?>" />

    <!-- Styles -->
    <link href="<?= asset_url('css/adminlte.min.css') ?>" rel="stylesheet">
    <link href="<?= asset_url('css/fontawesome-free-5.min.css') ?>" rel="stylesheet">
    <link href="<?= asset_url('css/app.css') ?>" rel="stylesheet">
    <!-- Bootstrap Table -->
    <link rel="stylesheet" href="<?= asset_url('plugins/bootstrap-table/bootstrap-table.min.css') ?>">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= asset_url('plugins/toastr/toastr.min.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= asset_url('plugins/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= asset_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Summernote -->
    <link rel="stylesheet" href="<?= asset_url('plugins/summernote/summernote-bs4.min.css') ?>">

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="<?= asset_url('plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Bootstrap -->
    <script src="<?= asset_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- AdminLTE -->
    <script src="<?= asset_url('js/adminlte.min.js') ?>"></script>
    <script src="<?= asset_url('js/fontawesome-free-5.min.js') ?>"></script>
    <!-- Bootstrap Table -->
    <script src="<?= asset_url('plugins/bootstrap-table/bootstrap-table.min.js') ?>"></script>
    <script src="<?= asset_url('plugins/bootstrap-table/locale/bootstrap-table-ru-RU.min.js') ?>"></script>
    <script src="<?= asset_url('plugins/bootstrap-table/extensions/cookie/bootstrap-table-cookie.min.js') ?>"></script>
    <script src="<?= asset_url(
        'plugins/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js'
    ) ?>"></script>
    <!-- Bootstrap Switch -->
    <script src="<?= asset_url('plugins/bootstrap-switch/js/bootstrap-switch.min.js') ?>"></script>
    <!-- Toastr -->
    <script src="<?= asset_url('plugins/toastr/toastr.min.js') ?>"></script>
    <!-- ScrollToFixed -->
    <script src="<?= asset_url('plugins/scrolltofixed/jquery-scrolltofixed-min.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= asset_url('plugins/select2/js/select2.min.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= asset_url('plugins/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
    <!-- Summernote -->
    <script src="<?= asset_url('plugins/summernote/summernote-bs4.min.js') ?>"></script>
    <script src="<?= asset_url('plugins/summernote/lang/summernote-ru-RU.min.js') ?>"></script>
    <!-- App scripts -->
    <script src="<?= asset_url('js/app.js', (string)time()) ?>"></script>
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
    <?php render_component('console/_navbar.php') ?>
    <?php render_component('console/_sidebar.php') ?>

    <!-- Content -->
    <div class="content-wrapper">
        <?= $content ?>
    </div>
</div>

<!-- Модальное окно -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php render_component('_com_toastr_messages'); ?>

<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 150,
            minHeight: 100,
            lang: 'ru-RU',
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize', 'height']],
                ['color', ['color']],
                ['view', ['codeview', 'help']],
            ],
        });
        $('.summernote-full').summernote({
            height: 250,
            minHeight: 150,
            lang: 'ru-RU',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname', 'fontsize', 'height']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        });
        $('.summernote-air').summernote({
            lang: 'ru-RU',
            airMode: true,
            popover: {
                air: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                ]
            }
        });
    });
</script>
</body>
</html>