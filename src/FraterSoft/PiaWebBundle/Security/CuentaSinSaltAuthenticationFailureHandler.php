<?php

namespace FraterSoft\PiaWebBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Las cuentas dadas de alta desde la app movil (SistemaPIA nuevo, NestJS)
 * guardan la clave con bcrypt y dejan "salt" en NULL - el encoder configurado
 * en security.yml (sha512, MessageDigestPasswordEncoder) no sabe verificar
 * eso, asi que esas cuentas nunca pueden loguearse en este sistema viejo, sin
 * importar que la clave este bien escrita.
 *
 * En vez del error generico "credenciales invalidas", cuando el login falla
 * y el usuario encontrado tiene salt = NULL, se le manda un correo para que
 * fije una clave nueva (reutiliza el flujo de "olvide mi clave" de
 * FOSUserBundle: mismo confirmation_token/password_requested_at, mismo
 * token_ttl). Al fijarla, ResettingController::resetAction (override de
 * este bundle) genera un salt nuevo antes de guardar - de ahi en adelante la
 * cuenta puede loguearse aca Y sigue pudiendo loguearse desde la app nueva
 * (que ya sabe verificar cuentas con salt via el mismo algoritmo sha512).
 */
class CuentaSinSaltAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $username = $request->request->get('_username');
        if ($username) {
            $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
            if (null !== $user && null === $user->getSalt()) {
                return $this->manejarCuentaSinSalt($user);
            }
        }

        return $this->fallo($request, $exception);
    }

    private function manejarCuentaSinSalt($user)
    {
        $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');

        if ($user->isPasswordRequestNonExpired($ttl)) {
            return $this->redirigirConMensaje(
                'Ya te enviamos un correo para que puedas ingresar desde este sistema. ' .
                'Revisa tu bandeja de entrada (tambien spam).'
            );
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $enlace = $this->container->get('router')->generate(
            'fos_user_resetting_reset',
            array('token' => $user->getConfirmationToken()),
            true
        );

        $cuerpo = $this->container->get('templating')->render(
            'FraterSoftPiaWebBundle:Default:email_establecer_clave.html.twig',
            array('usuario' => $user, 'enlace' => $enlace)
        );

        $this->container->get('app.mail_controller')->enviar(
            $this->container->getParameter('mailer_user'),
            'Establece tu clave de acceso - SistemaPIA',
            $user->getEmail(),
            $cuerpo
        );

        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return $this->redirigirConMensaje(
            'Tu cuenta fue creada desde la app movil. Te enviamos un correo para que ' .
            'puedas fijar una clave y entrar tambien desde aca.'
        );
    }

    private function redirigirConMensaje($mensaje)
    {
        $this->container->get('session')->set(
            SecurityContext::AUTHENTICATION_ERROR,
            new AuthenticationException($mensaje)
        );

        return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
    }

    private function fallo(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(SecurityContext::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
    }
}
