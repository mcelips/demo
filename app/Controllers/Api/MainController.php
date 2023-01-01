<?php

namespace App\Controllers\Api;

use App\Components\Competition\Competition;
use App\Components\Coupon\Coupon;
use App\Components\Magazine\Magazine;
use App\Components\News\News;
use App\Controllers\Controller;
use App\Services\Helpers;
use Spatie\PdfToImage\Pdf;

class MainController extends Controller
{

    /**
     * Список конкурсов
     *
     * @return void
     */
    public function competitions()
    {
        $response = [];
        if ($competitions = (new Competition())->get()) {
            foreach ($competitions as $competition) {
                $response[] = [
                    'id'          => $competition['id'],
                    'title'       => $competition['title'],
                    'text'        => $competition['text'],
                    'image_thumb' => get_url('/storage/competitions/' . $competition['image_thumb']),
                    'image'       => get_url('/storage/competitions/' . $competition['image']),
                ];
            }
        }
        json_response($response);
    }

    /**
     * Купоны
     *
     * @return void
     */
    public function coupons()
    {
        $response = [];
        if ($coupons = (new Coupon())->get()) {
            foreach ($coupons as $coupon) {
                $response[] = [
                    'id'          => $coupon['id'],
                    'title'       => $coupon['title'],
                    'text'        => $coupon['text'],
                    'url'         => $coupon['url'],
                    'image_thumb' => get_url('/storage/coupons/' . $coupon['image_thumb']),
                    'image'       => get_url('/storage/coupons/' . $coupon['image']),
                ];
            }
        }
        json_response($response);
    }

    /**
     * Список журналов
     *
     * @return void
     */
    public function magazines()
    {
        $response = [];
        if ($magazines = (new Magazine())->get()) {
            foreach ($magazines as $magazine) {
                $response[] = [
                    'id'              => $magazine['id'],
                    'title'           => $magazine['title'],
                    'image'           => get_url('/storage/' . $magazine['storage_path'] . '/' . $magazine['image']),
                    'pdf'             => get_url('/storage/' . $magazine['storage_path'] . '/' . $magazine['pdf']),
                    'pdf_name'        => $magazine['pdf'],
                    'pdf_total_pages' => $magazine['pdf_total_pages'],
                    'price'           => Helpers::formatNumber($magazine['price']),
                ];
            }
        }
        json_api_response($response);
    }

    /**
     * Новости
     *
     * @return void
     */
    public function news()
    {
        $response = [];
        if ($news = (new News())->get()) {
            foreach ($news as $newsItem) {
                $response[] = [
                    'id'          => $newsItem['id'],
                    'title'       => $newsItem['title'],
                    'text'        => $newsItem['text'],
                    'image_thumb' => get_url('/storage/news/' . $newsItem['image_thumb']),
                    'image'       => get_url('/storage/news/' . $newsItem['image']),
                    'date'        => date('d.m.Y', strtotime($newsItem['created_at'])),
                ];
            }
        }
        json_response($response);
    }

    /**
     * Возвращает превью страницы
     *
     * @return void
     */
    public function get_pdf_thumb()
    {
        $pdf  = from_post('pdf');
        $page = max((int)from_post('page'), 1);

        $magazine = (new Magazine())->where('pdf', $pdf)->getOne();

        if (! $magazine) {
            json_response('Not found');
        }

        if ($page > $magazine['pdf_total_pages']) {
            json_response_error('Not found');
        }

        $thumb = sprintf(
            'storage/%s/pdf_thumbs/%s_%s.jpg',
            $magazine['storage_path'],
            $magazine['pdf'],
            $page,
        );

        json_response(get_url($thumb));
    }

}