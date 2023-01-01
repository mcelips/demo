<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= $title ?></h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php
                $form_action = (empty($user) === true)
                    ? route('console.users.create')
                    : route('console.users.edit');
                ?>
                <form action="<?= $form_action ?>" method="post">
                    <?php if (isset($user['id'])): ?>
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <?php endif; ?>

                    <?php render_component(
                        'console/_com_form_submit_buttons',
                        ['cancel_link' => route('console.users')]
                    ); ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">
                                    Логин
                                    <small class="text-muted">username</small>
                                </label>
                                <?php if (isset($user['id']) and $user['id'] === 1): ?>
                                    <input type="text"
                                           class="form-control"
                                           id="username"
                                           value="<?= old('username', $user) ?>"
                                           disabled>
                                </span>
                                <?php else: ?>
                                    <input type="text"
                                           class="form-control"
                                           id="username"
                                           name="username"
                                           value="<?= old('username', $user) ?>"
                                           autocomplete="off">
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="slug">
                                    Пароль
                                    <small class="text-muted">password</small>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="password"
                                       name="password"
                                       autocomplete="off">
                                <small class="text-info font-italic">
                                    (Минимальная длина: <?= \App\Components\Auth\Services\PasswordService::PASSWORD_MIN_LENGTH ?> символов)
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
