<?php

namespace Tigon\DmsConnect\Admin\New;

use ErrorException;
use Tigon\DmsConnect\Admin\Attributes;

use Tigon\DmsConnect\Core;
use WP_Error;

class Cart extends \Tigon\DmsConnect\Abstracts\Abstract_Cart
{
    private $not_default = false;

    /**
     * Check if input cart is valid
     *
     * @return true|\WP_Error
     */
    protected function verify_data()
    {
        $this->sku = strtoupper(
            preg_replace('/\s/', '', substr($this->cart['cartType']['make'], 0, 3)) .
            preg_replace('/\s/', '', substr($this->cart['cartType']['model'], 0, 3)) .
            preg_replace('/\s/', '', substr($this->cart['cartAttributes']['cartColor'], 0, 3)) .
            preg_replace('/\s/', '', substr($this->cart['cartAttributes']['seatColor'], 0, 3)) .
            preg_replace('/\s/', '', substr(Attributes::$locations[$this->cart['cartLocation']['locationId']]['city'], 0, 3))
        );
        if ($this->cart['isInStock'] && !$this->cart['isInBoneyard']) {
            if ($this->cart['serialNo']) {
                $this->sku = preg_replace('/\s/', '', $this->cart['serialNo']);
                $this->not_default = true;
            }
            if ($this->cart['vinNo']) {
                $this->sku = preg_replace('/\s/', '', $this->cart['vinNo']);
                $this->not_default = true;
            }
        }

        if(!$this->not_default) {
            $this->cart['_id'] = null;
        }

        return true;
    }
    
    /**
     * Initialize Slug,
     * define brand_hyphenated, pattern_hyphenated, color_hyphenated, location_hyphenated
     *
     * @return void
     */
    protected function create_slug()
    {
        $this->brand_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartType']['make']);
        $this->pattern_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartType']['model']);
        $this->color_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartAttributes']['cartColor']);
        $this->seat_color_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartAttributes']['seatColor']);
        $this->location_hyphenated = preg_replace('/\s+/', '-', Attributes::$locations[$this->location_id]['city'] . "-" . Attributes::$locations[$this->location_id]['st']);

        //DMS generated
        if($this->cart['advertising']['websiteUrl']) {
            $url_parts = explode('/', $this->cart['advertising']['websiteUrl']);
            $this->slug = end($url_parts);
            $this->slug = preg_replace('/\+/', '-plus-', $this->slug);
            if(substr($this->slug,-1) === '/') $this->slug = substr_replace($this->slug,'',-1);
        } else {
            $this->slug = strtolower(implode('-', [
                $this->brand_hyphenated,
                $this->pattern_hyphenated,
                $this->color_hyphenated,
                'seat',
                $this->seat_color_hyphenated,
                $this->location_hyphenated
            ]));
        }
        // throw new ErrorException($this->slug);
    }

    // fetch_images() inherited from Abstract_Cart — downloads images from S3

    protected function field_overrides()
    {
        $this->published = 'publish';
        if (!$this->not_default) {
            $this->in_stock = 'outofstock';
            $this->monroney_sticker = '[pdf-embedder url=""]';
        }
    }
}
