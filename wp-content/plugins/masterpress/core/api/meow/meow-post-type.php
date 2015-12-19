<?php

class MEOW_PostType extends WOOF_PostType {

  // allows "incoming" terms into the mix, so that posts with field values for these terms are included
  
  function in_a($terms, $taxonomy = NULL, $args = array(), $operator = "IN", $relation = "OR") {
    
    global $wf;

    $posts = parent::in_a($terms, $taxonomy, $args, $operator, $relation);
    
    
    if ($wf->regard_field_terms()) {
      
      if (!is_array($terms) && is_string($terms)) {
        $terms = explode(",", $terms);
      }

      if (!isset($taxonomy)) {

        $term_objects = $terms;
        
        if (WOOF::is_or_extends($terms, "WOOF_Term")) {
          $terms = $wf->collection( array($terms) );
        }
      
      } else {
        
        $term_objects = array();
        
        foreach ($terms as $term) {
          
          $obj = $wf->term($term, $taxonomy);
      
          if (!is_woof_silent($obj)) {
            $term_objects[] = $obj;
          }

        }
    
        $term_objects = $wf->collection( $term_objects );

      }
      
      
      if (is_woof_collection($term_objects, "WOOF_Term") && count($term_objects)) {
        
        $grouped = $term_objects->group_by( "taxonomy_name" );

        $matching_posts = $wf->collection();
        
        foreach ($grouped as $taxonomy_name => $terms) {
          
          $tax_posts = $wf->collection();
          
          foreach ($terms as $term) {

            if ($operator == "IN") {
              $tax_posts->merge( $term->incoming( array("post_type" => $this->name ) ), false );
            } else if ($operator == "AND") {
              $tax_posts = $tax_posts->intersect( $term->incoming( array("post_type" => $this->name ) ), "slug" );
            }
          
          }

          if ($relation == "OR") {
            $matching_posts = $matching_posts->merge( $tax_posts, false );
          } else {
            $matching_posts = $matching_posts->intersect( $tax_posts, "slug" );
          }

          
        }
        
        $posts->merge( $matching_posts, false );

        $posts->dedupe();
        
      }
      
    }
    
    return $posts;
    
  }
  
  
  public function json_href() {
    global $wf;
    $slug = $this->rewrite_slug(true);
    return rtrim( $wf->site->url(), "/" ) . "/" . trim( $wf->rest_api_slug(), "/" ) . "/" . $slug;
  }
  
  
}
