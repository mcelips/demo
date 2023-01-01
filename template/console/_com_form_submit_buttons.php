<div class="card fixed">
    <div class="card-body row">
        <div class="col-7">
            <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Сохранить
            </button>
            <button type="submit" name="submit_close" class="btn btn-default">
                Сохранить и закрыть
            </button>
            <a href="<?= $cancel_link ?>" class="btn btn-default" id="back_button">
                Отмена
            </a>
        </div>
        <div class="col-5 text-right d-none d-sm-block">
            <span class="btn btn-outline-light active" type="button" style="cursor: default;">
                <i class="fa fa-info mr-1"></i> <b>Ctrl+S</b> - Сохранить, <b>Ctrl+Alt+S</b> - Сохранить и закрыть, <b>Ctrl+Alt+W</b> - Отмена
            </span>
        </div>
    </div>
</div>