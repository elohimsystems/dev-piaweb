<?php

namespace FraterSoft\PiaWebBundle\Security;

use FraterSoft\PiaWebBundle\Controller\CuentaMovilController;
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
 * y el usuario encontrado tiene salt = NULL, se lo manda a
 * CuentaMovilController::revalidarAction (solo se guarda el id en sesion,
 * nunca el email) para que confirme el email de su cuenta antes de que se le
 * mande el correo de "fijar clave nueva" - evita que alguien dispare ese
 * correo solo por adivinar un nombre de usuario.
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
                $request->getSession()->set(CuentaMovilController::SESSION_USER_ID, $user->getId());

                return new RedirectResponse(
                    $this->container->get('router')->generate('frater_soft_pia_web_cuenta_movil_revalidar')
                );
            }
        }

        return $this->fallo($request, $exception);
    }

    private function fallo(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(SecurityContext::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
    }
}
