<?php

namespace Armincms\Home\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{ID, Text, Textarea, Number, Password, Select}; 
use Armincms\Nova\Resource as BaseResource;
use Armincms\Fields\{Targomaan, BelongsToMany};  
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
                    ->when(static::class != Page::class, function($query) {
                        $query->where('resource', static::class);
                    });
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

                Select::make(__('Mark As'), 'marked_as') 
                    ->options([
                        static::getDraftValue() => __('Draft'), 
                        static::getPublishValue() => __('Publish'), 
                    ])
                    ->default(static::getDraftValue())
                    ->displayUsingLabels(), 

                Text::make(__('Title'), 'title')
                    ->required()
                    ->rules('required')
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

                $this->imageField(),
            ]),  

            (new Targomaan([ 
                // $this->abstractField(), 

                $this->gutenbergField(), 
            ]))->withoutToolbar(),  

            new Panel(__('Advanced'), [
                new Targomaan([

                    $this->seoField()
                        ->hideFromIndex(),
                ]),
            ]),
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
            return $model->forceFill(['resource' => static::class]);
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
