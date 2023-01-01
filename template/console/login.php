<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= config('app.name') ?> | Панель управления</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpg" href="<?= asset_url('images/favicon.jpg') ?>" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset_url('css/fontawesome-free-5.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= asset_url('css/adminlte.min.css') ?>">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= asset_url('plugins/toastr/toastr.min.css') ?>">

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="<?= asset_url('plugins/jquery/jquery.min.js') ?>"></script>
    <!-- Toastr -->
    <script src="<?= asset_url('plugins/toastr/toastr.min.js') ?>"></script>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo"><?= config('app.name') ?></div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <form action="<?= route('login') ?>" method="post">
                <div class="input-group mb-3">
                    <input type="text"
                           class="form-control"
                           id="username"
                           name="username"
                           value="<?= old('username') ?>"
                           autofocus
                           placeholder="Логин"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           placeholder="Пароль"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember" checked>
                            <label for="remember">
                                Запомнить меня
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            Войти
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<?php render_component('_com_toastr_messages'); ?>
</body>
</html>
