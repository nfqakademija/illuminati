<?php

namespace Illuminati\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Illuminati\ProductBundle\Entity\Supplier;
use Illuminati\ProductBundle\Form\SupplierType;

/**
 * Supplier controller.
 *
 */
class SupplierController extends Controller
{

    /**
     * Lists all Supplier entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProductBundle:Supplier')->findAll();

        return $this->render('ProductBundle:Supplier:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Supplier entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Supplier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('supplier_show', array('id' => $entity->getId())));
        }

        return $this->render('ProductBundle:Supplier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Supplier entity.
     *
     * @param Supplier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Supplier $entity)
    {
        $form = $this->createForm(new SupplierType(), $entity, array(
            'action' => $this->generateUrl('supplier_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Supplier entity.
     *
     */
    public function newAction()
    {
        $entity = new Supplier();
        $form   = $this->createCreateForm($entity);

        return $this->render('ProductBundle:Supplier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Supplier entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductBundle:Supplier')->find($id);
        $ProductEntities = $em->getRepository('ProductBundle:Product')->findBy(array(
            'supplier' => $entity
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductBundle:Supplier:show.html.twig', array(
            'entity' => $entity,
            'ProductEntities' => $ProductEntities,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Supplier entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductBundle:Supplier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductBundle:Supplier:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Supplier entity.
    *
    * @param Supplier $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Supplier $entity)
    {
        $form = $this->createForm(new SupplierType(), $entity, array(
            'action' => $this->generateUrl('supplier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Supplier entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductBundle:Supplier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('supplier_edit', array('id' => $id)));
        }

        return $this->render('ProductBundle:Supplier:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Supplier entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProductBundle:Supplier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Supplier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('supplier'));
    }

    /**
     * Creates a form to delete a Supplier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supplier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
