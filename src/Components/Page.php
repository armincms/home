<?php 
namespace Armincms\Home\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document;
use Armincms\Home\Page as Model;

class Page extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = '{slug}';

	/**
	 * Route Conditions of Component.
	 * 
	 * @var null
	 */
	protected $wheres = []; 

	private $type = null;

	public function toHtml(Request $request, Document $docuemnt) : string
	{       
		$page = Model::published()->where('url', $request->relativeUrl())->firstOrFail(); 

		$this->resource($page);  

		$docuemnt->title(data_get($page, 'seo.title') ?: $page->title); 
		$docuemnt->description(
			data_get($page, 'seo.description') ?: mb_substr(strip_tags($page->description), 0, 100) 
		); 

		return (string) $this
							->firstLayout($docuemnt, $this->config('layout'), 'clean-blog')
							->display($page->toArray(), $docuemnt->component->config('layout', [])); 
	}    

	public function featuredImage(string $schema = 'main')
	{  
		return $this->resource->featuredImage($schema);
	}

	public function tags()
	{
		return $this->resource->tags;
	} 

	public function categories()
	{
		return collect();
	}

	public function author()
	{
		return $this->resource->owner;
	}
}
