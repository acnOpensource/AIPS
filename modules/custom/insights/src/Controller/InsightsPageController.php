<?php
/**
 *  Insights Class file
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
   * Insights Methods return Content for Analytics and Insights page
   * fetch data from Database based on Category provided in the content
   * If Sub category present in the content, Data will be fetch with Sub
   * Category Key Table involve -> 'node_field_data', 'node__body',
   * 'node__field_data_lake_url', 'node__field_thumbnail',
   *                  'node__field_insights_type',
   * 'node__field_insights_sub_type' Condition -> Content for content type =
   * 'analytics_and_insights' (Analytics and Insights) List sorting in Category
   * type in Ascending Order Fetch Data ->  'Title', 'Description', 'Link',
   * 'Thumbnail' DOB: 10/31/2017 Developer: Sachin Suryavanshi Custom Module
   * Method for AIPS Portal
   *
   * @return array
   */
  public function insights() {

    $content = \Drupal::database()->select('node_field_data', 'nfd');
    $content->fields('nfd', ['nid', 'title', 'status']);
    $content->addField('nb', 'body_value');
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

    $templateArray = [];
    if (is_object($contentData) || is_array($contentData)) {
      foreach ($contentData as $key => $value) {
        $insightsCategory = Term::load($value->field_insights_type_target_id)
          ->getName();
        if (isset($value->field_insights_sub_type_target_id)) {
          $insightsSubCategory = Term::load($value->field_insights_sub_type_target_id)
            ->getName();
          $templateArray[$insightsCategory][$insightsSubCategory][$key]['title'] = $value->title;
          $templateArray[$insightsCategory][$insightsSubCategory][$key]['description'] = $value->body_value;
          $templateArray[$insightsCategory][$insightsSubCategory][$key]['thumbnail'] = File::load($value->field_thumbnail_target_id)
            ->getFileUri();
          $templateArray[$insightsCategory][$insightsSubCategory][$key]['dataLakeURL'] = $value->field_data_lake_url_uri;
        }
        else {
          $templateArray[$insightsCategory][$key]['title'] = $value->title;
          $templateArray[$insightsCategory][$key]['description'] = $value->body_value;
          $templateArray[$insightsCategory][$key]['thumbnail'] = File::load($value->field_thumbnail_target_id)
            ->getFileUri();
          $templateArray[$insightsCategory][$key]['dataLakeURL'] = $value->field_data_lake_url_uri;
        }
      }
    }

    /*return array(
      '#type' => 'markup',
      '#markup' => 'Test',
    );*/

    echo '<pre>';
    print_r($templateArray);
    exit;
  }

}