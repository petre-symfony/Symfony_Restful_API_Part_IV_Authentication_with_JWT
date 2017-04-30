<?php
namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends BaseController {
  /**
   * @Route("/api/tokens")
   * @Method("POST")
   */
  public function newTokenAction(Request $request){
    $user = $this->getDoctrine()
      ->getRepository('AppBundle:User')
      ->findOneBy(['username' => $request->getUser()]); 
    
    if (!$user){
      throw $this->createNotFoundException('No user');
    }
    
    $isValid = $this->get('security.password_encoder')
      ->isPasswordValid($user, $request->getPassword());
    
    if (!$isValid){
      throw new BadCredentialsException();
    }
  }
}
