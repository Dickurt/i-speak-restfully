<?php
/**
 * Basic class that wraps CURL to call REST APIs and return the results
 */

class RestCaller {
  private $config = null;

  public function __construct($config = null) {
    // TODO: Set default options, which will be used by call_endpoint(), but are
    // overwritable by setting the parameters in call_endpoint()
    $this->config = array();

    if(!empty($config)) {
      // set_headers();
    }
  }

  public function call_endpoint($method = null, $url, $headers = null, $params = null, $params_type = null, $response_type = null) {
    // TODO: Consider putting all options in a single array with pre-determined keys

    // TODO: Instead of calling curl_setopt() again and again, just use curl_setopt_array (http://stackoverflow.com/a/6518125/2929693)

    $result = null;

    if(empty($url)) {
      return $result;
    }

    $curl = curl_init($url);

    $temp_headers = array();
    if(!empty($headers)) {
      $temp_headers += $headers;
    }


    switch(strtolower($params_type)) {
      case 'json':
        $param_length = strlen($params);
        $temp_headers += array(
          'Content-Type: application/json',
          "Content-Length: $param_length"
        );
        break;
    }

    switch(strtolower($method)) {
      // case 'get':
      //   curl_setopt($curl, CURLOPT_GET, true);
      //   break;

      case 'post':
        curl_setopt($curl, CURLOPT_POST, true);

        if(!empty($params)) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
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
    // curl_setopt($curl, CURLOPT_HEADER, true); // this also returns headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, $temp_headers);

    // Ensure we can return the response
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);

    if(!empty($result)) {
      // If the response type is not given, then perhaps we can derive a
      // response type from the params type
      if(empty($response_type)) {
        $response_type = $params_type;
      }

      $result = $this->handle_response($result, $response_type);
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

  public function set_headers() {
    //
  }
}
