<?php

namespace Armincms\Home\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{ID, Text, Textarea, Number, Password, Select}; 
use Armincms\Nova\Resource as BaseResource;
use Armincms\Fields\{Targomaan, BelongsToMany}; 
use Armincms\Taggable\Nova\Tag; 
use Outhebox\NovaHiddenField\HiddenField; 
use Inspheric\Fields\Url;

abstract class Resource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Home\Page::class; 

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title'
    ];

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query
                    ->where('language', app()->getLocale())
                    ->where('resource', static::class);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [ 
            ID::make()->sortable(),  

            new Targomaan([ 

                Url::make(__('Title'), 'url')
                    ->exceptOnForms()
                    ->alwaysClickable() 
                    ->resolveUsing(function($value, $resource, $attribute) {
                        return $this->site()->url(urldecode($value));
                    })
                    ->titleUsing(function($value, $resource) {
                        return $this->title;
                    }) 
                    ->labelUsing(function($value, $resource) {
                        return $this->title;
                    }),
                
                HiddenField::make('resource')
                    ->defaultValue(static::class)
                    ->onlyOnForms(),

                Select::make(__('Mark As'), 'marked_as') 
                    ->options([
                        static::getDraftVlaue() => __('Draft'), 
                        static::getPublishVlaue() => __('Publish'), 
                    ])
                    ->default(static::getDraftVlaue())
                    ->displayUsingLabels(), 

                Text::make(__('Title'), 'title')
                    ->required()
                    ->onlyOnForms(), 

                $this->slugField(),

                Url::make('URL')
                    ->alwaysClickable()
                    ->hideWhenCreating()
                    ->onlyOnForms()
                    ->readOnly()
                    ->resolveUsing(function($value, $resource, $attribute) {
                        return $this->site()->url(urldecode($value));
                    })
                    ->fillUsing(function() {}),


                Number::make(__('Hits'), 'hits')
                    ->exceptOnForms(),
            ]), 

            BelongsToMany::make(__('Tags'), 'tags', Tag::class)
                ->hideFromIndex(), 

            (new Targomaan([ 
                // $this->abstractField(), 

                $this->gutenbergField(), 
            ]))->withoutToolbar(), 

            new Panel(__('Media'), [
                (new Targomaan([
                    $this->imageField(),
                ]))->withoutToolbar(),
            ]),

            $this->when($request->isMethod('put') || $request->isMethod('post'),     
                Text::make('async')->fillUsing(function($request, $model) {  
                    $model::saved(function($saved) use ($model) { 
                        if($saved->is($model)) {  
                            $tags = $model->tags()->get(); 

                            $model->translations()->get()->each(function($trans) use ($model, $tags) { 
                                $trans->tags()->sync($tags);
                            });  
                        }
                    });
                }),
            ),
        ];
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    { 
        return with(parent::newModel(), function($model) {
            return $model->forceFill(['resource' => class_basename(static::class)]);
        });
    }  

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}