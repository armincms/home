<?php
namespace Armincms\Home;
     

class ConfigLocate 
{       
    static public function all($locale = null)
    {
        return Page::whereLanguage($locale ?? app()->getLocale())->get()->map([static::class, 'information'])->toArray();
    }

    static public function published($menu)
    {
        return Page::published()
                    ->whereLanguage($menu->language)
                    ->get()
                    ->map([static::class, 'information'])
                    ->toArray();
    } 

    public static function information($page)
    {
        return [
            'id'    => $page->id,
            'title' => $page->title,
            'url'   => $page->url(),
            'active'=> (int) $page->isPublished(),
        ];
    }
    
}
