<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtGenreCategorie;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtGenreCategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GenreCategorieController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtGenreCategorieController extends Controller
{
    /**
     * Afficher tout les genre_categories
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_genre_categorie_manager = $this->get(ServiceName::SRV_METIER_GENRE_CATEGORIE);

        // Récupérer tout les genre_categories

        $_genre_categories = $_genre_categorie_manager->getAllCtGenreCategorie();

        return $this->render('AdminBundle:CtGenreCategorie:index.html.twig', array(
            'genre_categories' => $_genre_categories,
        ));
    }

    /**
     * Affichage page modification genre_categorie
     *
     * @param CtGenreCategorie $_genre_categorie
     *
     * @return Render page
     */
    public function editAction(CtGenreCategorie $_genre_categorie)
    {
        if (!$_genre_categorie) {
            throw $this->createNotFoundException('Unable to find GenreCategorie entity.');
        }

        $_edit_form = $this->createEditForm($_genre_categorie);

        return $this->render('AdminBundle:CtGenreCategorie:edit.html.twig', array(
            'genre_categorie' => $_genre_categorie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création genre_categorie
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_genre_categorie_manager = $this->get(ServiceName::SRV_METIER_GENRE_CATEGORIE);

        $_genre_categorie = new CtGenreCategorie();
        $_form       = $this->createCreateForm($_genre_categorie);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement genre_categorie
            $_genre_categorie_manager->saveCtGenreCategorie($_genre_categorie, 'new');

            $_genre_categorie_manager->setFlash('success', "Genre Categorie ajoutée");

            return $this->redirect($this->generateUrl('genre_categorie_index'));
        }

        return $this->render('AdminBundle:CtGenreCategorie:add.html.twig', array(
            'genre_categorie' => $_genre_categorie,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification genre_categorie
     * @param Request $_request requête
     * @param CtGenreCategorie $_genre_categorie
     * @return Render page
     */
    public function updateAction(Request $_request, CtGenreCategorie $_genre_categorie)
    {
        // Récupérer manager
        $_genre_categorie_manager = $this->get(ServiceName::SRV_METIER_GENRE_CATEGORIE);

        if (!$_genre_categorie) {
            throw $this->createNotFoundException('Unable to find GenreCategorie entity.');
        }

        $_edit_form = $this->createEditForm($_genre_categorie);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_genre_categorie_manager->saveCtGenreCategorie($_genre_categorie, 'update');

            $_genre_categorie_manager->setFlash('success', "Genre Categorie modifiée");

            return $this->redirect($this->generateUrl('genre_categorie_index'));
        }

        return $this->render('AdminBundle:CtGenreCategorie:edit.html.twig', array(
            'genre_categorie' => $_genre_categorie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition genre_categorie
     * @param CtGenreCategorie $_genre_categorie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtGenreCategorie $_genre_categorie)
    {
        $_form = $this->createForm(CtGenreCategorieType::class, $_genre_categorie, array(
            'action' => $this->generateUrl('genre_categorie_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création genre_categorie
     * @param CtGenreCategorie $_genre_categorie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtGenreCategorie $_genre_categorie)
    {
        $_form = $this->createForm(CtGenreCategorieType::class, $_genre_categorie, array(
            'action' => $this->generateUrl('genre_categorie_update', array('id' => $_genre_categorie->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression genre_categorie
     * @param Request $_request requête
     * @param CtGenreCategorie $_genre_categorie
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtGenreCategorie $_genre_categorie)
    {
        // Récupérer manager
        $_genre_categorie_manager = $this->get(ServiceName::SRV_METIER_GENRE_CATEGORIE);

        $_form = $this->createDeleteForm($_genre_categorie);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression genre_categorie
            $_genre_categorie_manager->deleteCtGenreCategorie($_genre_categorie);

            $_genre_categorie_manager->setFlash('success', 'Genre Categorie supprimée');
        }

        return $this->redirectToRoute('genre_categorie_index');
    }

    /**
     * Création formulaire de suppression genre_categorie
     * @param CtGenreCategorie $_genre_categorie The GenreCategorie entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtGenreCategorie $_genre_categorie)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('genre_categorie_delete', array('id' => $_genre_categorie->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste genre_categorie
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_genre_categorie_manager = $this->get(ServiceName::SRV_METIER_GENRE_CATEGORIE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_genre_categorie_manager->deleteGroupCtGenreCategorie($_ids);
        }

        $_genre_categorie_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('genre_categorie_index'));
    }
}
