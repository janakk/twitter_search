<?php
/**
 * Class TwitterSearch
 * @author Janak Kapadia
 *
 * A wrapper class that does application only authorization.
 * It also calls the twitter search api.
 */
class TwitterSearch {
  private $oauth_settings;
  private $rest_urls;
  private $encoded_keys;

  private $header; // Curl header
  private $post_fields; // Post fields for curl
  private $curl_url;
  private $is_post = true;

  private $bearer_token = false;

  public function __construct(array $settings) {
    $this->oauth_settings = $settings['oauth_settings'];
    $this->rest_urls = $settings['rest_urls'];
    $this->encoded_keys = base64_encode(rawurlencode($this->oauth_settings['api_key']) . ":"
                                      . rawurlencode($this->oauth_settings['api_secret']));
  }

  /**
   * Assembled the data for App only authentication and gets
   * the bearer_token
   * @return bool Returns true if bearer_token is set
   */
  private function getBearerToken() {
    $this->post_fields = 'grant_type=client_credentials';
    $this->is_post = true;
    $this->header = array(
      "Authorization: Basic " . $this->encoded_keys,
      "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
    );
    $this->curl_url = $this->rest_urls['oauth_url'];

    $response = null;
    try {
      $response = $this->makeRequest();
      if($response) {
        $response = json_decode($response, true);
        if ($response['token_type'] != "bearer") {
          return false;
        } else {
          $this->bearer_token = $response['access_token'];
        }
      } else {
        return false;
      }
    } catch(Exception $e) {
      return false;
    }

    return true;
  }

  /**
   * Assembled the data to make a call to the twitter search api.
   * Makes sure a bearer token is available, if not, then it makes
   * an app only authentication request through getBearerToken().
   * @param $search_param Takes in a search parameter
   * @return bool|mixed Returns false if there was a problem communicating else returns the
   *                    the search results
   */
  public function getSearchResult($search_param) {
    // Check if bearer_token available
    if(!$this->bearer_token) {
      try {
        $this->getBearerToken();
      } catch(Exception $e) {
        return false;
      }
    }
    $this->is_post = false;
    $this->header = array(
      "Authorization: Bearer " . $this->bearer_token,
      "Content-Type: application/json",
    );
    $this->curl_url = $this->rest_urls['search_url'] . "?q=" . rawurlencode($search_param);

    try {
      $response = $this->makeRequest();
      if(!$response) {
        return false;
      }
    } catch(Exception $e) {
      return false;
    }
    return $response;
  }


  /**
   * Makes the actual curl request to the twitter resource url
   * and returns it's response.
   * @return mixed Returns the response received from curl request
   */
  private function makeRequest() {
    $options = array(
      CURLOPT_HTTPHEADER => $this->header,
      CURLOPT_URL => $this->curl_url,
      CURLOPT_HEADER => false,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
      CURLOPT_CAINFO => "cacert.pem",
      CURLOPT_RETURNTRANSFER => true,
    );

    // If the request is post
    if($this->is_post) {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = $this->post_fields;
    }

    $curl_request = curl_init();
    curl_setopt_array($curl_request, $options);
    $curl_response = curl_exec($curl_request);
    curl_close($curl_request);
    return $curl_response;
  }
} 