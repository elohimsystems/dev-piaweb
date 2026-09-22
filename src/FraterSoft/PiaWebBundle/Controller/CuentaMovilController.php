<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Paso intermedio cuando una cuenta se creo desde la app movil (bcrypt, sin
 * salt) y alguien intenta loguearse con ella en este sistema viejo. En vez
 * de mandar el correo de "fijar clave" automaticamente al primer intento
 * fallido, se le explica la situacion y se le pide confirmar el email de la
 * cuenta primero - si coincide, se manda el correo de revalidacion; si no,
 * error. CuentaSinSaltAuthenticationFailureHandler es quien redirige aca
 * (guarda el id del usuario encontrado en sesion, nunca el email).
 */
class CuentaMovilController extends Controller
{
    const SESSION_USER_ID = 'cuenta_movil_revalidar/user_id';

    public function revalidarAction(Request $request)
    {
        $session = $request->getSession();
        $userId = $session->get(self::SESSION_USER_ID);

        if (!$userId) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id' => $userId));

        if (!$user || null !== $user->getSalt()) {
            // La cuenta ya no aplica (se resolvio por otra via, o no existe mas).
            $session->remove(self::SESSION_USER_ID);
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $emailIngresado = trim($request->request->get('email', ''));
            if ($emailIngresado !== '' && 0 === strcasecmp($emailIngresado, $user->getEmail())) {
                $this->enviarCorreoRevalidacion($user);
                $session->remove(self::SESSION_USER_ID);
                $session->getFlashBag()->set(
                    'fos_user_success',
                    'Te enviamos un correo para que puedas fijar tu clave de acceso a este sistema.'
                );

                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
            $error = 'Ese correo no coincide con el de tu cuenta. Revisalo e intenta de nuevo.';
        }

        return $this->render('FraterSoftPiaWebBundle:Default:revalidar_cuenta_movil.html.twig', array(
            'error' => $error,
        ));
    }

    /**
     * Genera (si hace falta) el token de FOSUserBundle y manda el correo con
     * el link de "establecer clave" - mismo template/mecanismo que ya usaba
     * el handler de login. Si ya habia un token vigente (menos de token_ttl),
     * no reenvia uno nuevo, para no pisar el que la persona ya pudo haber
     * recibido.
     */
    private function enviarCorreoRevalidacion($user)
    {
        $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');

        if ($user->isPasswordRequestNonExpired($ttl)) {
            return;
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $enlace = $this->get('router')->generate(
            'fos_user_resetting_reset',
            array('token' => $user->getConfirmationToken()),
            true
        );

        $cuerpo = $this->renderView('FraterSoftPiaWebBundle:Default:email_establecer_clave.html.twig', array(
            'usuario' => $user,
            'enlace' => $enlace,
        ));

        $this->get('app.mail_controller')->enviar(
            $this->container->getParameter('mailer_user'),
            'Establece tu clave de acceso - SistemaPIA',
            $user->getEmail(),
            $cuerpo
        );

        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);
    }
}
