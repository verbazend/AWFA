<?php

/* 
  MEOW_VirtualFieldSetCollection: A set collection representing the results of a filter or sort operation
  "Virtual" in the sense that it doesn't directly represent the data, but rather a manipulation of the data.
  This is better than returning an array, since it allows sorting AND filtering via chained calls.
*/

class MEOW_VirtualFieldSetCollection extends MEOW_FieldSetCollection {
  function __construct($items) {
    // here the constructor simply overrides the traditional constructor, 
    // so that we can provide the items data (in different order, or filtered)
    $this->_items = $items;
  }
}