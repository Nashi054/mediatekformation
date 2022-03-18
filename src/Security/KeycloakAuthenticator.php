<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Gère la récupération du code et la gestion de l'authentification
 *
 * @author Niels-Patrick
 */
class KeycloakAuthenticator extends SocialAuthenticator {
    
    // gestionaire de clients OAuth
    private $clientRegistry;
    // permet de gérer la BDD (lire/écrire)
    private $em;
    // permet de lire une route
    private $router;
    
    function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    
    public function getCredentials(Request $request) {
        return $this->fetchAccessToken($this->getKeycloakClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $keycloakUser = $this->getKeycloakClient()->fetchUserFromToken($credentials);
        
        // recherche du user dans la BDD à partir de son id Keycloak
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['keycloakId' => $keycloakUser->getId()]);
        if($existingUser){
            return $existingUser;
        }
        
        // le user existe mais n'est pas encore connecté avec Keycloak
        $email = $keycloakUser->getEmail();
        /** @var User $userInDatabase */
        $userInDatabase = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if($userInDatabase){
            $userInDatabase->setKeycloakId($keycloakUser->getId());
            $this->em->persist($userInDatabase);
            $this->em->flush();
            return $userInDatabase;
        }
        
        // le user n'existe pas encore dans la BDD
        $user = new User();
        $user->setKeycloakId($keycloakUser->getId());
        $user->setEmail($keycloakUser->getEmail());
        $user->setPassword("");
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        $targetUrl = $this->router->generate('admin.formations');
        return new RedirectResponse($targetUrl);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response {
        return new RedirectResponse('/oauth/login', Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function supports(Request $request): bool {
        return $request->attributes->get('_route') === 'oauth_check';
    }
    
    private function getKeycloakClient(){
        return $this->clientRegistry->getClient('keycloak');
    }

}
