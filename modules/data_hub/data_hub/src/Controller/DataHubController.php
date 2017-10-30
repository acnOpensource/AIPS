<?php
namespace Drupal\data_hub\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Class DatahubController
 * @package Drupal\datahub\Controller
 */
class DataHubController extends ControllerBase {   
    
  public function datahublist(){ 
	\Drupal::service('page_cache_kill_switch')->trigger();
    //echo 's,jcbhsbcjvsbc';exit;
	//return array();
	echo "i am here";die;
    
  }
}