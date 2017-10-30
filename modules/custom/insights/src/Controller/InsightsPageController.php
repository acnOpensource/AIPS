<?php
/**
 * Data Lake Controller Class file
 */
namespace Drupal\insights\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

/**
 * Class InsightsPageController
 *
 * @package Drupal\data_lake\Controller
 */
class InsightsPageController extends ControllerBase {

  /**
   * @return array
   */
  public function insights(){

    $content = \Drupal::database()->select('node_field_data', 'nfd');
    $content->fields('nfd', ['nid', 'title', 'status']);
    $content->addField('nb', 'body_summary');
    $content->addField('nfu', 'field_data_lake_url_uri');
    $content->addField('nfi', 'field_thumbnail_target_id');
    $content->addField('nft', 'field_insights_type_target_id');
    $content->addField('nfs', 'field_insights_sub_type_target_id');
    $content->leftJoin('node__body', 'nb', 'nb.entity_id = nfd.nid');
    $content->leftJoin('node__field_data_lake_url', 'nfu', 'nfu.entity_id = nfd.nid');
    $content->leftJoin('node__field_thumbnail', 'nfi', 'nfi.entity_id = nfd.nid');
    $content->Join('node__field_insights_type', 'nft', 'nft.entity_id = nfd.nid');
    $content->leftJoin('node__field_insights_sub_type', 'nfs', 'nfs.entity_id = nfd.nid');
    $content->condition('nfd.type', 'analytics_and_insights');
    $content->orderBy('field_insights_type_target_id');
    $contentData = $content->execute()->fetchAllAssoc('nid');

    $templateArray = array();
    if(is_object($contentData) || is_array($contentData)){
      foreach ($contentData as $key=>$value){
        $templateArray[$key]['title'] = $value->title;
        $templateArray[$key]['description'] = $value->body_summary;
        $templateArray[$key]['thumbnail'] = File::load($value->field_thumbnail_target_id)->getFileUri();
        $templateArray[$key]['taxonomyName'] = Term::load($value->field_insights_type_target_id)->getName();
        //$templateArray[$key]['subTaxonomyName'] = Term::load($value->field_insights_sub_type_target_id)->getName();
        $templateArray[$key]['dataLakeURL'] = $value->field_data_lake_url_uri;

      }
    }

    echo '<pre>';print_r($templateArray);exit;
  }

}