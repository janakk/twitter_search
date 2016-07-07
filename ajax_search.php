<?php
include "TwitterSearch.php";
include "settings.php";

// Get the searchquery paramerter.
$q = isset($_REQUEST['searchquery']) ? $_REQUEST['searchquery'] : "";

if($q != "") {
  $result = getSearchResults($q);
  if($result) {
    echo json_encode($result);
  } else {
    echo json_encode("An error occurred. Try after some time");
  }
}

/**
 * Gets the search results from twitter
 * @param $search_param Search parameter submitted by the user
 * @return array Returns an array of search results
 */
function getSearchResults($search_param) {
  global $oauth_settings;
  global $rest_urls;
  $tw_search = new TwitterSearch(array(
    'oauth_settings' => $oauth_settings,
    'rest_urls' => $rest_urls));

  $result = $tw_search->getSearchResult($search_param);

  $jsonArray = json_decode($result, TRUE);
  $jsonArray = $jsonArray["statuses"];

  $return_result = array();
  foreach($jsonArray as $item) {
    $curr_text = createEntityLinks($item['text'], $item['entities']);
    try {
//      $curr_tz = date_default_timezone_get();
      date_default_timezone_set('UTC');
    } catch (Exception $e) {
    }

    $timestamp = date_timestamp_get(DateTime::createFromFormat('D M d H:i:s O Y', $item['created_at']));
//    date_default_timezone_set($curr_tz);

    $tweet = array(
      'id' => $item['id_str'],
      'tweet_text' => $curr_text,
      'tweet_date' => $timestamp,
      'user_name' => $item['user']['name'],
      'screen_name' => $item['user']['screen_name'],
      'user_image' => $item['user']['profile_image_url'],
    );
    $return_result[] = $tweet;
  }
  return $return_result;
}

/**
 * Replaces the hashtags, urls, user_mentions, media with relevant links display urls from entities
 * @param $text Tweet Text
 * @param $entities Entities in the text
 * @return string Returns a string of text after replacing hashtags, urls, user_mentions and media links with
 * relevant links from the entities and display urls.
 */
function createEntityLinks($text, $entities) {
  $text = utf8_encode($text); // need to encode text here to take care of special chars
  $curr_entity = array();
  foreach($entities as $k=>$entity) {
    switch ($k) {
      case "hashtags":
        foreach($entity as $e) {
          $hash_url = "https://twitter.com/search?q=" . rawurlencode('#'.$e['text']) . "&src=hash";
          $hash_link = makeLink('#'.$e['text'], $hash_url, 'hash-tags');
          $raw_key = rawurlencode(substr($text, $e['indices'][0], $e['indices'][1] - $e['indices'][0]));
          $curr_entity[$raw_key] = $hash_link;
        }
        break;
      case "symbols":
        break;
      case "urls":
        foreach($entity as $e) {
          $link_url = $e['url'];
          $link_display = $e['display_url'];
          $link = makeLink($link_display, $link_url, 'link-external');
          $raw_key = rawurlencode(substr($text, $e['indices'][0], $e['indices'][1] - $e['indices'][0]));
          $curr_entity[$raw_key] = $link;
        }

        break;
      case "user_mentions":
        foreach($entity as $e) {
          $user_url = "https://twitter.com/" . $e['screen_name'];
          $user_display = "@" . $e['screen_name'];
          $user_link = makeLink($user_display, $user_url, 'screen-name');
          $raw_key = rawurlencode(substr($text, $e['indices'][0], $e['indices'][1] - $e['indices'][0]));
          $curr_entity[$raw_key] = $user_link;
        }
        break;
      case "media":
        foreach($entity as $e) {
          $media_url = $e['url'];
          $media_display = $e['display_url'];
          $media_link = makeLink($media_display, $media_url, "link-external");
          $raw_key = rawurlencode(substr($text, $e['indices'][0], $e['indices'][1] - $e['indices'][0]));
          $curr_entity[$raw_key] = $media_link;
        }
    }
  }

  $text = rawurlencode($text);
  foreach($curr_entity as $k=>$v) {
    $text = str_replace($k, $v, $text);

  }

  return utf8_decode(rawurldecode($text));
}

function makeLink($display, $url, $class) {
  return "<a href='$url' target='_blank' class='$class'>" . $display . "</a>";
}
