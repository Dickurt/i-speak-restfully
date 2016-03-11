<?php
/**
 * Basic class that wraps CURL to call REST APIs and return the results
 */

class RestCaller {

  public function __construct() {
    // TODO: Set default options, which will be used by call_endpoint(), but are
    // overwritable by setting the parameters in call_endpoint()
  }

  public function call_endpoint($method = null, $url, $headers = null, $params = null, $params_type = null, $response_type = null) {
    // TODO: Consider putting all options in a single array with pre-determined keys

    $result = null;

    if(empty($url)) {
      return $result;
    }

    $curl = curl_init($url);

    $temp_headers = array();
    if(!empty($headers)) {
      array_merge($temp_headers, $headers);
    }

    switch(strtolower($params_type)) {
      case 'json'
        array_merge($temp_headers, array(
          'Content-Type: application/json',
          "Content-Length: strlen($params)"
        ));
        break;
    }

    switch(strtolower($method)) {
      case 'get':
        curl_setopt($curl, CURLOPT_GET, true);
        break;

      case 'post':
        curl_setopt($curl, CURLOPT_POST, true);

        if(!empty($params)) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $params)
        }
        break;

      // Unknown or empty
      default:
        // Determine which request method to use by looking at the presence of
        // parameters. If params is not empty, then we will assume this should
        // be a POST request. If there are no params, then we will assume it is
        // a GET request.
        if(!empty($params)) {
          //
        }
        break;
    }

    // Set headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, $temp_headers);

    $curl_result = curl_exec($curl);

    if(!empty($curl_result)) {
      $result = handle_response($curl_result, $response_type);
    }

    curl_close($curl);

    return $result;
  }

  // $type = content type, e.g. json
  private function handle_response($response, $type = null) {
    switch($type) {
      case 'json':
        return json_decode($response);
        break;

      default:
        return $response;
        break;
    }
  }
}

$base_url = 'localhost:3000/';

$rest_caller = new RestCaller;
$response = $rest_caller->call_endpoint('get', "$base_url/plays");

var_dump($response);
