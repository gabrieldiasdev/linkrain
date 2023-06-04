<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Link;
use App\Models\View;

class PageController extends Controller
{
    public function index($slug) {
        $page = Page::where('slug', $slug)->first();

        if($page) {
            //Background
            $background = '#FFFFFF';
            switch($page->op_background_type) {
                case 'image':
                    $background = "url('".url('/media/uploads').'/'.$page->op_background_value."')";
                break;
                case 'color':
                    $colors = explode(',', $page->op_background_value);
                    $background = 'linear-gradient(90deg,';
                    $background .= $colors[0].',';
                    $background .= !empty($colors[1]) ? $colors[1] : $colors[0];
                    $background .= ')';
                break;
            }

            //Links
            $links = Link::where('id_page', $page->id)->where('status', 1)->orderBy('order')->get();

            //Registrar a visualização
            $view = View::firstOrNew(
                ['id_page' => $page->id, 'view_date' => date('Y-m-d')]
            );
            $view->total++;
            $view->save();

            return view('page', [
                'font_color' => $page->op_font_color,
                'profile_image' => url('/media/uploads').'/'.$page->op_profile_image,
                'title' => $page->op_title,
                'description' => $page->op_description,
                'facebook_pixel' => $page->op_facebook_pixel,
                'background' => $background,
                'links' => $links
            ]);
        } else {
            return view('notfound');
        }
    }
}
