<?php

namespace FraterSoft\PiaWebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FraterSoft\PiaWebBundle\Entity\User;
use FraterSoft\PiaWebBundle\Form\UserType;

/**
 * Maestro de Usuarios (menu Maestros, solo ROLE_SUPER_ADMIN).
 * Rutas /maestros/usuarios/* protegidas en security.yml.
 */
class UserController extends commonPIAClass
{
    private function crearFormMaestro(User $entity, $action)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $action,
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Guardar'));
        return $form;
    }

    public function maestroIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FraterSoftPiaWebBundle:User')
                ->findBy(array(), array('username' => 'ASC'));

        return $this->render('FraterSoftPiaWebBundle:User:maestro_index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function maestroNewAction()
    {
        $entity = new User();
        $entity->setEnabled(true);
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_user_create'));

        return $this->render('FraterSoftPiaWebBundle:User:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Usuario',
        ));
    }

    public function maestroCreateAction(Request $request)
    {
        $entity = new User();
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_user_create'));
        $form->handleRequest($request);

        $plainPassword = $form->get('plainPassword')->getData();

        if ($form->isValid() && !$plainPassword) {
            $this->get('session')->getFlashBag()->add('error', 'Debe indicar una contraseña para el nuevo usuario');
        } elseif ($form->isValid()) {
            $entity->setPlainPassword($plainPassword);
            $userManager = $this->get('fos_user.user_manager');
            try {
                $userManager->updateUser($entity);
                $this->get('session')->getFlashBag()->add('success', 'Usuario creado');
                return $this->redirect($this->generateUrl('maestro_user'));
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'No se pudo crear el usuario: el nombre de usuario o el correo ya estan en uso.');
            }
        }

        return $this->render('FraterSoftPiaWebBundle:User:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Nuevo Usuario',
        ));
    }

    public function maestroEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_user_update', array('id' => $id)));

        return $this->render('FraterSoftPiaWebBundle:User:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Usuario',
        ));
    }

    public function maestroUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        $form = $this->crearFormMaestro($entity, $this->generateUrl('maestro_user_update', array('id' => $id)));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $entity->setPlainPassword($plainPassword);
            }
            $userManager = $this->get('fos_user.user_manager');
            try {
                $userManager->updateUser($entity);
                $this->get('session')->getFlashBag()->add('success', 'Usuario actualizado');
                return $this->redirect($this->generateUrl('maestro_user'));
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', 'No se pudo actualizar el usuario: el nombre de usuario o el correo ya estan en uso.');
            }
        }

        return $this->render('FraterSoftPiaWebBundle:User:maestro_form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'titulo' => 'Editar Usuario',
        ));
    }

    public function maestroEliminarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FraterSoftPiaWebBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        if ($entity->getUsername() === $this->getUser()->getUsername()) {
            $this->get('session')->getFlashBag()->add('error', 'No puede eliminar su propio usuario.');
            return $this->redirect($this->generateUrl('maestro_user'));
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Usuario eliminado');
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', 'No se pudo eliminar: el usuario tiene registros asociados. Puede marcarlo como Inactivo.');
        }
        return $this->redirect($this->generateUrl('maestro_user'));
    }
}
