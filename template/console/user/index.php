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
                <div class="card">
                    <div class="card-body">
                        <a href="<?= route('console.users.create') ?>" class="btn btn-success">
                            <i class="fa fas fa-plus mr-2"></i> Добавить
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover bs-table">
                            <thead>
                            <tr class="text-center">
                                <th style="width: 250px;" data-width="250">Управление</th>
                                <th style="width: 50px;" data-width="50" data-sortable="true">ID</th>
                                <th data-sortable="true">Логин</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($users): foreach ($users as $user): ?>
                                <tr>
                                    <td class="text-right">
                                        <a href="<?= route('console.users.edit', ['id' => $user['id']]) ?>"
                                           class="btn btn-sm btn-default">
                                            <i class="fa fa-fw fa-pencil-alt"></i> Изменить
                                        </a>
                                        <?php if ($user['id'] > 1): ?>
                                            <a href="<?= route('console.users.delete', ['id' => $user['id']]) ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Вы уверены?')">
                                                <i class="fa fa-fw fa-trash"></i> Удалить
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <?= $user['username'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
</script>
