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
                $form_action = (empty($competition) === true)
                    ? route('console.competitions.create')
                    : route('console.competitions.edit');
                ?>
                <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data">
                    <?php if (isset($competition['id'])): ?>
                        <input type="hidden" name="id" value="<?= $competition['id'] ?>">
                    <?php endif; ?>

                    <?php render_component(
                        'console/_com_form_submit_buttons',
                        ['cancel_link' => route('console.competitions')]
                    ); ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">
                                    Название
                                    <small class="text-muted">title</small>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       value="<?= old('title', $competition) ?>"
                                       autocomplete="off">
                            </div>

                            <div class="form-group">
                                <div class="form-group">
                                    <label for="text">
                                        Описание
                                        <small class="text-muted">text</small>
                                    </label>
                                    <textarea name="text"
                                              id="text"
                                              class="form-control"
                                              cols="1"
                                              rows="3"><?= old('text', $competition) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="image_thumb_file">
                                            Превью изображение
                                            <small class="text-muted">image_thumb</small>
                                        </label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input"
                                                       name="image_thumb_file"
                                                       accept="image/*"
                                                       id="image_thumb_file">
                                                <label class="custom-file-label" for="image_thumb_file">Выбрать файл</label>
                                            </div>
                                        </div>
                                        <small class="text-info font-italic">
                                            (Разрешение обложки: 360 x 640)
                                        </small>
                                        <input type="hidden"
                                               name="image_thumb"
                                               value="<?= old('image_thumb', $competition) ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="image_file">
                                            Основная картинка
                                            <small class="text-muted">image</small>
                                        </label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input"
                                                       name="image_file"
                                                       accept="image/*"
                                                       id="image_file">
                                                <label class="custom-file-label" for="image_file">Выбрать файл</label>
                                            </div>
                                        </div>
                                        <small class="text-info font-italic">
                                            (Разрешение обложки: 720 x 1280)
                                        </small>
                                        <input type="hidden"
                                               name="image"
                                               value="<?= old('image', $competition) ?>">
                                    </div>
                                </div>

                                <?php if (isset($competition['image']) and ! empty($competition['image'])): ?>
                                    <div class="col" id="preview">
                                        <div class="row">
                                            <div class="col-6 text-center">
                                                <img src="<?= get_url(
                                                    'storage/competitions/' . $competition['image_thumb']
                                                ) . '?ver=' . TMR ?>"
                                                     class="img-thumbnail" style="height: 300px;">
                                                <div class="small text-muted">Превью изображение</div>
                                            </div>
                                            <div class="col-6 text-center">
                                                <img src="<?= get_url(
                                                    'storage/competitions/' . $competition['image']
                                                ) . '?ver=' . TMR ?>"
                                                     class="img-thumbnail" style="height: 300px;">
                                                <div class="small text-muted">Основное изображение</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    // запрещаем ввод более 2 знаков после запятой
    $('#price').on('input', function () {
        this.value = Number(parseFloat(this.value).toFixed(2));
    });
    <?php if (isset($competition['id'])): ?>
    // обновление превью обложки и PDF при обновлении данных
    $(document).ajaxSuccess(function (event, jqxhr, settings) {
        if (settings.url === '<?= route('console.competitions.edit')?>') {
            $('#preview').load('<?= route('console.competitions.edit', ['id' => $competition['id']]) ?> #preview')
        }
    });
    <?php endif; ?>
</script>
