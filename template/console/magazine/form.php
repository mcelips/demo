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
                $form_action = (empty($magazine) === true)
                    ? route('console.magazines.create')
                    : route('console.magazines.edit');
                ?>
                <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data">
                    <?php if (isset($magazine['id'])): ?>
                        <input type="hidden" name="id" value="<?= $magazine['id'] ?>">
                    <?php endif; ?>

                    <?php render_component(
                        'console/_com_form_submit_buttons',
                        ['cancel_link' => route('console.magazines')]
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
                                       value="<?= old('title', $magazine) ?>"
                                       autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="price">
                                    Цена
                                    <small class="text-muted">price</small>
                                </label>
                                <input type="number"
                                       class="form-control"
                                       id="price"
                                       name="price"
                                       step="0.01"
                                       min="0"
                                       value="<?= (float)old('price', $magazine) ?>"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="storage_path" value="<?= old('storage_path', $magazine) ?>">
                                    <input type="hidden" name="pdf_total_pages" value="<?= old('pdf_total_pages', $magazine) ?>">
                                    <div class="form-group">
                                        <label for="image_file">
                                            Обложка
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
                                               value="<?= old('image', $magazine) ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="pdf_file">
                                            PDF
                                            <small class="text-muted">pdf</small>
                                        </label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file"
                                                       class="custom-file-input"
                                                       accept="application/pdf"
                                                       name="pdf_file"
                                                       id="pdf_file">
                                                <label class="custom-file-label" for="pdf_file">Выбрать файл</label>
                                            </div>
                                            <input type="hidden"
                                                   name="pdf"
                                                   value="<?= old('pdf', $magazine) ?>">
                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($magazine['image']) and ! empty($magazine['image'])): ?>
                                    <div class="col" id="preview">
                                        <div class="row">
                                            <div class="col-6 text-center">
                                                <img src="<?= get_url(
                                                    'storage/' . $magazine['storage_path'] . '/' . $magazine['image']
                                                ) . '?ver=' . TMR ?>"
                                                     class="img-thumbnail" style="height: 300px;">
                                            </div>
                                            <div class="col-6 text-center">
                                                <embed src="<?= get_url('storage/' . $magazine['storage_path'] . '/' . $magazine['pdf']) . '?ver=' . TMR ?>"
                                                       type="application/pdf"
                                                       style="width: 400px; height: 300px;"
                                                       class="img-thumbnail" />
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
    <?php if (isset($magazine['id'])): ?>
    // обновление превью обложки и PDF при обновлении данных
    $(document).ajaxSuccess(function (event, jqxhr, settings) {
        if (settings.url === '<?= route('console.magazines.edit')?>') {
            $('#preview').load('<?= route('console.magazines.edit', ['id' => $magazine['id']]) ?> #preview')
        }
    });
    <?php endif; ?>
</script>
