<?php

namespace Armincms\Home; 

use Illuminate\Database\Eloquent\{Model, Builder, SoftDeletes}; 
use Armincms\Concerns\{IntractsWithMedia, Authorization};
use Armincms\Contracts\Authorizable;
use Armincms\Targomaan\Concerns\InteractsWithTargomaan;
use Armincms\Targomaan\Contracts\Translatable; 
use Armincms\Markable\Publishable; 
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Cviebrock\EloquentSluggable\Sluggable;
use Core\HttpSite\Concerns\{IntractsWithSite, HasPermalink}; 
use Core\HttpSite\Component;   
use Zareismail\NovaPolicy\Contracts\Ownable;

class Page extends Model implements HasMedia, Authorizable, Translatable, Ownable
{ 
	use SoftDeletes, IntractsWithMedia, Authorization, InteractsWithTargomaan; 
    use IntractsWithSite, HasPermalink, Publishable;
    use Sluggable {
		scopeFindSimilarSlugs as sluggableSimilarSlugs;
	}

	const LOCALE_KEY = 'language';

	protected $casts = [
		'seo' => 'json', 
	];

	protected $medias = [
		'image' => [ 
			'disk' => 'armin.image',
			'schemas' => [
				'*', 'page', 'page.list'
			],
		]
	]; 


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ]; 
    } 

    public function component() : Component
    { 
    	return new Components\Page;
    }

    /**
     * Query scope for finding "similar" slugs, used to determine uniqueness.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $attribute
     * @param array $config
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindSimilarSlugs(Builder $query, string $attribute, array $config, string $slug): Builder
    { 
    	return $this->sluggableSimilarSlugs($query, $attribute, $config, $slug)
    				->where('resource', $this->resource);
    }

	/**
	 * Driver name of the targomaan.
	 * 
	 * @return [type] [description]
	 */
	public function translator(): string 
	{
		return 'sequential';
	}

    public function featuredImage(string $schema = 'main')
    {
        return $this->featuredImages()->get($schema);
    }

    public function featuredImages()
    {
        return $this->getConversions(
            $this->getFirstMedia('image'), config('home.schemas', ['main', 'thumbnail'])
        );
    }
    /**
     * Indicate Model Authenticatable.
     * 
     * @return mixed
     */
    public function owner()
    {
        return $this->user();
    }
}
