<?php
namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends BaseController {
  /**
   * @Route("/api/tokens")
   * @Method("POST")
   */
  public function newTokenAction(){
    return new  Response('TOKEN'); 
  }
}
