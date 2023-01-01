<!-- Main content -->
<section class="content pt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body text-red text-bold">
                        <i>Привет, <?= user('username') ?>.</i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        Добро пожаловать в консоль "<?= config('app.name') ?>".
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>