<?php

namespace App\Components\Magazine;

use App\Models\Model;

/**
 * CRON - Создание превью для PDF
 */
class PdfThumbQueue extends Model
{
    const STATUS_FAILED = -1;
    const STATUS_NOT_FINISHED = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED     = 2;
    protected $table = 'pdf_thumb_queue';
}