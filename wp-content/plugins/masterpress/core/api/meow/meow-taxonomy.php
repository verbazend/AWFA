<?php

class MEOW_Taxonomy extends WOOF_Taxonomy {

  public function json_href() {
    global $wf;
    $slug = $this->rewrite_slug(true);
    return rtrim( $wf->site->url(), "/" ) . "/" . trim( $wf->rest_api_slug(), "/" ) . "/" . $slug;
  }
  
}
