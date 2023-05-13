<?php
/**
 * Created by PhpStorm.
 * User: LAMLAM
 * Date: 7/28/2019
 * Time: 10:17 PM
 */

namespace FoxEngineers\AdminCP\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Support\Str;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class SeoController extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;
    use AuthorizesRequests;
    use SEOTools;

    public function parseObjectSEO(array $data): \stdClass
    {
        $object = new \stdClass();
        foreach ($data as $k => $v) {
            $object->$k = $v;
        }
        return $object;
    }

    public function generateSEOByData($data,$alter = false, $type = 'object'){
        $desc = '';

        if(isset($data->title)){
            $desc = strip_tags($data->title);
        }

        if($type === 'array'){
            $data = $this->parseObjectSEO($data);
        }

        if(isset($data->description)){
            $desc = strip_tags($data->description);
        }
        else {
            if(isset($data->content)){
                $desc = Str::limit(strip_tags($data->content),200);
            }
        }

        //////////////////////////////////////////////
        if($alter && is_string($alter)){
            $this->seo()->setTitle(strip_tags($data->title. ' - '. $alter));
            $this->seo()->setDescription(strip_tags($desc . ' - '. $alter ));
            $this->seo()->opengraph()->setTitle(strip_tags($data->title . ' - '. $alter));
            $this->seo()->opengraph()->setDescription(strip_tags( $desc . ' - '. $alter));
            if(isset($data->thumbnail)){
                $this->seo()->opengraph()->addProperty('image', real_path($data->thumbnail));
            }
            else $this->seo()->opengraph()->addProperty('image', real_path($this->getThumbnail()));
        }
        else {
            $this->seo()->setTitle(strip_tags($data->title));
            $this->seo()->setDescription(strip_tags($desc));
            $this->seo()->opengraph()->setTitle(strip_tags($data->title));
            $this->seo()->opengraph()->setDescription(strip_tags($desc));
            if(isset($data->thumbnail)) {
                $this->seo()->opengraph()->addProperty('image', real_path($data->thumbnail));
            }
            else $this->seo()->opengraph()->addProperty('image', real_path($this->getThumbnail()));
            $this->seo()->twitter()->setTitle(strip_tags($data->title));
            $this->seo()->twitter()->setDescription(strip_tags($desc));
        }

        SEOMeta::addMeta('fb:app_id', env('FACEBOOK_CLIENT_ID', null), 'property');
    }

    abstract public function getThumbnail(): string;
}
