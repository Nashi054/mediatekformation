<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    /**
     * @Route("/oauth/login", name="oauth_login")
     */
    public function index(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var KeycloakClient $client */
        $client = $clientRegistry->getClient('keycloak');
        return $client->redirect();
    }
    
    /**
     * @Route("/oauth/callback", name="oauth_check")
     */
    public function check(){}
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}
}
