<?php

namespace Drupal\Tests\general\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Tests\rest\Functional\BasicAuthResourceTestTrait;
use Drupal\Tests\rest\Functional\BcTimestampNormalizerUnixTestTrait;
use Drupal\Tests\rest\Functional\ResourceTestBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class GeneralResourceTestBase
 *
 * @package Drupal\Tests\general\Functional
 * @author R. Hasan <hasan@company.com>
 * @since 08.06.2018
 */
abstract class GeneralResourceTestBase extends ResourceTestBase {

  use BcTimestampNormalizerUnixTestTrait;
  use BasicAuthResourceTestTrait;

  private $options = [];

  protected static $client;
  protected static $entityType;
  protected static $getAsPost = '{"id":[{"value":@id}]}';
  protected static $scenarioPath;

  public function setUp() {
    parent::setUp();

    static::$scenarioPath = getcwd() . '/../test/scenario';
    static::$client = new Client();
  }

  /**
   * It executes all test cases (methods). All methods which start with
   * runArcTest_ will be recognized and automatically executed.
   *
   * @param \Drupal\Tests\general\Functional\GeneralResourceTestBase $class
   */
  protected function runAllArcTests(GeneralResourceTestBase $class) {

    $testMethods = preg_grep('/^runArcTest_/', get_class_methods($class));

    foreach ($testMethods as $method) {
      $class->{$method}();
    }
  }

  /**
   * It takes the file name and tries to return the content of the file.
   *
   * @param string $fileName
   *
   * @return bool|string
   */
  protected function getScenarioFile($fileName) {
    $file = static::$scenarioPath . '/' . $fileName;
    if (is_file($file)) {
      return file_get_contents($file);
    }

    throw new FileNotFoundException("File is not available: {$file}!");
  }

  /**
   * @param \GuzzleHttp\Client $client
   * @param $userInput
   * @param string $method
   *
   * @return mixed|null|\Psr\Http\Message\ResponseInterface
   */
  protected function doRequest(Client $client, $userInput, $method='GET') {

    $url = Url::fromUserInput($userInput)
    ->setAbsolute(true)
      ->toString();

    try {
      return $client->request($method, $url, $this->options);
    } catch (GuzzleException $e) {
      echo "\n\n{$e->getMessage()}";
    }
    return null;
  }

  protected function setOptions($options=[]) {

    $this->options = [];
    $this->options[RequestOptions::AUTH] = ['arcrestapi', '7171arc'];
    $this->options[RequestOptions::HEADERS]['Accept'] = static::$mimeType;
    $this->options[RequestOptions::HEADERS]['Content-Type'] = static::$mimeType;

    if(isset($options['body']) && is_string($options['body'])) {
      //$this->options[RequestOptions::HEADERS]['X-CSRF-Token'] = $this->getCSRFToken();
      // we are decoding than encoding to avoid wrong chars like german ü,ä etc.
      $this->options[RequestOptions::BODY] = Json::encode(Json::decode($options['body']));
      unset($options['body']);
    } elseif($options && count($options) > 0) {
      $this->options[RequestOptions::QUERY] = $options;
    }
    $this->options[RequestOptions::QUERY]['_format'] = 'json';

    //$this->options[RequestOptions::HTTP_ERRORS] = FALSE;
    //$this->options[RequestOptions::DEBUG] = TRUE;
  }

  /*protected function getCSRFToken() {
    $url = Url::fromUserInput('/rest/session/token')
      ->setAbsolute(true)
      ->toString();

    $client = new Client();
    $token = $client->get($url);
    return $token->getBody()->getContents();
  }*/

  protected function setAuthentication($method = 'GET') {
    $this->initAuthentication();
    $this->setUpAuthorization($method);
  }

  protected function getRequest(Client $client, $resource, $options = []) {
    $method = 'GET';
    $this->setAuthentication($method);
    $this->setOptions($options);

    try {
      return $this->doRequest($client, "/api/v1/{$resource}/0/1/id/desc/null", $method);
    } catch (RequestException $e) {
      echo "\n\n{$e->getMessage()}";
    }
  }

  protected function postRequest(Client $client, $resource, $options = []) {
    $method = 'POST';
    $this->setAuthentication($method);
    $this->setOptions($options);

    try {
      return $this->doRequest($client, "/api/v1/{$resource}", $method);
    } catch (RequestException $e) {
      echo "\n\n{$e->getMessage()}";
    }
  }

  protected function getAsPostRequest(Client $client, $resource, $options = []) {
    $method = 'POST';
    $this->setAuthentication($method);
    $this->setOptions($options);

    try {
      return $this->doRequest($client, "/api/v1/{$resource}/null", $method);
    } catch (RequestException $e) {
      echo "\n\n{$e->getMessage()}";
    }
  }

  protected function patchRequest(Client $client, $resource, $id, $options = []) {
    $method = 'PATCH';
    $this->setAuthentication($method);
    $this->setOptions($options);

    try {
      return $this->doRequest($client, "/api/v1/{$resource}/{$id}", $method);
    } catch (RequestException $e) {
      echo "\n\n{$e->getMessage()}";
    }
  }

  /**
   * Sets up the necessary authorization.
   *
   * In case of a test verifying publicly accessible REST resources: grant
   * permissions to the anonymous user role.
   *
   * In case of a test verifying behavior when using a particular authentication
   * provider: create a user with a particular set of permissions.
   *
   * Because of the $method parameter, it's possible to first set up
   * authentication for only GET, then add POST, et cetera. This then also
   * allows for verifying a 403 in case of missing authorization.
   *
   * @param string $method
   *   The HTTP method for which to set up authentication.
   *
   * @see ::grantPermissionsToAnonymousRole()
   * @see ::grantPermissionsToAuthenticatedRole()
   */
  protected function setUpAuthorization($method) {
    $entityType = static::$entityType;
    switch ($method) {
      case 'GET':
        $this->grantPermissionsToTestedRole(["administer {$entityType}"]);
        break;
      case 'POST':
        $this->grantPermissionsToTestedRole(["view published {$entityType} entities","add {$entityType} entities"]);
        break;
      case 'PATCH':
        $this->grantPermissionsToTestedRole(["edit {$entityType} entities"]);
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function assertNormalizationEdgeCases($method, Url $url, array $request_options) {}

  /**
   * {@inheritdoc}
   */
  protected function getExpectedUnauthorizedAccessCacheability() {}


  /**
   * @param string $resourceName
   * @param array $filter
   *
   * @return array
   */
  protected function getAssertion($resourceName, $filter=[]) {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    $response = $this->getRequest(static::$client, $resourceName, $filter);
    $dataJson = $response->getBody()->getContents();
    $dataArray = Json::decode($dataJson);

    $this->checkDataArray($dataArray);

    $contentType = $response->getHeaders()["Content-Type"][0];

    $this->assertSame(200, $response->getStatusCode());
    $this->assertJson($dataJson);
    $this->assertEquals("application/json", $contentType);

    return [$response, $dataJson, $dataArray];
  }

  /**
   * @param string $resourceName
   * @param string $body as JSON
   *
   * @return array
   */
  protected function postAssertion($resourceName, $body) {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    $response = $this->postRequest(static::$client, $resourceName, ['body' => $body]);
    $dataJson = $response->getBody()->getContents();
    $dataArray = Json::decode($dataJson);

    $this->checkDataArray($dataArray);

    $this->assertSame(200, $response->getStatusCode());
    $this->assertJson($dataJson);

    return [$response, $dataJson, $dataArray];
  }

  /**
   * @param string $resourceName
   * @param string $body as JSON
   * @param int $id
   *
   * @return array
   */
  protected function getAsPostAssertion($resourceName, $body, $id) {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    $response = $this->getAsPostRequest(static::$client, $resourceName, ['body' => $body]);
    $dataJson = $response->getBody()->getContents();
    $dataArray = Json::decode($dataJson);

    $this->checkDataArray($dataArray);

    $this->assertSame(200, $response->getStatusCode());
    $this->assertJson($dataJson);
    $this->assertEquals($id, $dataArray['items'][0]['id'][0]['value']);

    return [$response, $dataJson, $dataArray];
  }

  /**
   * @param string $resourceName
   * @param string $body as JSON
   * @param int $id
   *
   * @return array
   */
  protected function patchAssertion($resourceName, $body, $id, $message) {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    $response = $this->patchRequest(static::$client, $resourceName, $id, ['body' => $body]);
    $dataJson = $response->getBody()->getContents();
    $dataArray = Json::decode($dataJson);

    $this->checkDataArray($dataArray);

    $this->assertSame(200, $response->getStatusCode());
    $this->assertJson($dataJson);
    $this->arcAssertArrayHasKey($dataArray, ['id', 0, 'value'], $message);
    $this->assertEquals($id, $dataArray['id'][0]['value']);

    return [$response, $dataJson, $dataArray];
  }

  protected function checkDataArray($dataArray) {
    // If ID is missing, print the output to see the details in case of any error
    if(isset($dataArray['record'])) {
      print "\n";
      if(is_array($dataArray)) {
        print_r($dataArray);
      } else {
        var_dump($dataArray);
      }
    }
  }

  /**
   * It will assert all given keys as an array with the given array to assert.
   *
   * @param array $array
   * @param array $keys
   * @param string $message
   */
  protected function arcAssertArrayHasKey(array $array, array $keys = [], $message = '') {
    foreach ($keys as $key) {
      $this->assertArrayHasKey($key, $array, $message);

      // If key does exist, go deeper in the array to assert next $key
      if(array_key_exists($key,$array)) {
        $array = $array[$key];
      }
    }
  }
}