<?php
namespace AppBundle\Security;

use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use AppBundle\Api\ApiProblem;
use AppBundle\Api\ResponseFactory;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator{
  /**
   *
   * @var JWTEncoderInterface
   */
  private $jwtEncoder;
  /**
   *
   * @var EntityManager 
   */
  private $em;
  
  /**
   *
   * @var ResponseFactory 
   */
  private $responseFactory;
  public function __construct(JWTEncoderInterface $jwtEncoder, EntityManager $em,ResponseFactory $responseFactory) {
    $this->jwtEncoder = $jwtEncoder;
    $this->em = $em;
    $this->responseFactory = $responseFactory;
  }


  public function getCredentials(Request $request) {
    $extractor = new AuthorizationHeaderTokenExtractor(
      'Bearer',
      'Authorization'
    );
    $token = $extractor->extract($request);
    
    if (!$token){
      return;
    }
    
    return $token;
  }
  public function getUser($credentials, UserProviderInterface $userProvider) {
    try{
      $data = $this->jwtEncoder->decode($credentials);
    } catch(JWTDecodeFailureException $e) {
      // if you want to, use can use $e->getReason() to find out which of the 3 possible things went wrong
      // and tweak the message accordingly
      // https://github.com/lexik/LexikJWTAuthenticationBundle/blob/05e15967f4dab94c8a75b275692d928a2fbf6d18/Exception/JWTDecodeFailureException.php
      
      throw new CustomUserMessageAuthenticationException('Invalid Token');
    }
    
    $username = $data['username'];
    
    return $this->em->getRepository('AppBundle:User')
      ->findOneBy(['username' => $username]);
  }
  
  public function checkCredentials($credentials, UserInterface $user) {
    return true;
  }
  
  public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
    return new Response('Hello!', 401);
  }
  
  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
    //do nothing;
  }
  
  public function supportsRememberMe() {
    return false;
  }
  
  public function start(Request $request, AuthenticationException $authException = null) {
    $apiProblem = new ApiProblem(401);
    $message = $authException ? $authException->getMessageKey() : 'Missing Credential';
    $apiProblem->set('detail', $message);
    
    return $this->responseFactory->createResponse($apiProblem);
  }
}
