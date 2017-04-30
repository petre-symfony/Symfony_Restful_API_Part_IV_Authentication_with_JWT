<?php
namespace AppBundle\Tests\Controller\Api;

use AppBundle\Test\ApiTestCase;

class TokenControllerTest extends ApiTestCase {
  public function testPOSTCreateToken(){
     $this->createUser('weaverryan', 'I<3Pizza');
     
     $response = $this->client->post('/api/tokens', [
       'auth' => ['weaverryan', 'I<3Pizza']
     ]);
     $this->assertEquals(200, $response->getStatusCode());
  }
}
