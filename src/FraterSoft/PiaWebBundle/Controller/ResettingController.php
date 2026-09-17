<?php

namespace FraterSoft\PiaWebBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Tras restablecer la contrasena, en vez de autenticar al usuario
 * automaticamente, lo manda al login para que ingrese la nueva contrasena,
 * junto con el mensaje de exito (resetting.flash.success).
 */
class ResettingController extends BaseResettingController
{
    /**
     * Copia de BaseResettingController::resetAction (misma logica), con un
     * solo agregado: si el usuario no tiene salt (cuenta creada desde la app
     * movil con bcrypt, ver CuentaSinSaltAuthenticationFailureHandler), le
     * genera uno nuevo ANTES de que el form handler guarde la clave - sin
     * esto, UserManager::updatePassword() codifica con el salt actual (NULL)
     * y la cuenta queda igual de imposible de verificar que antes.
     */
    public function resetAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        if (null === $user->getSalt()) {
            $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

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
