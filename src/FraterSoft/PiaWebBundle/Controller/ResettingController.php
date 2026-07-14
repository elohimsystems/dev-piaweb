<?php

namespace FraterSoft\PiaWebBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tras restablecer la contrasena, en vez de autenticar al usuario
 * automaticamente, lo manda al login para que ingrese la nueva contrasena,
 * junto con el mensaje de exito (resetting.flash.success).
 */
class ResettingController extends BaseResettingController
{
    protected function authenticateUser(UserInterface $user, Response $response)
    {
        // No autenticar automaticamente: el usuario debe iniciar sesion
        // con su nueva contrasena.
    }

    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('fos_user_security_login');
    }
}
