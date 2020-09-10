<?php

namespace Armincms\Home;
 
use Illuminate\Support\ServiceProvider;  

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations'); 
        $this->configureModules(); 
        $this->configureMenus(); 
        $this->configureSites(); 
        \Gate::policy(Page::class, Policies\Page::class);
    } 

    public function configureModules()
    {    
        \Config::set('module.locatables.page', [
            'title' => 'blog::title.page', 
            'name'  => 'page',
            'items' => [ConfigLocate::class, 'all'],
        ]);  
    }

    public function configureMenus()
    {
        
        \Config::set('menu.menuables.page', [
            'title' => 'blog::title.page',
            'callback' => [ConfigLocate::class, 'published'],
        ]);   
    }

    public function configureSites()
    { 
        \Site::push('home', function($blog) { 
            $blog
                ->pushComponent(new Components\Tag)
                ->pushComponent(new Components\Page); 
        });
 
    }
}
