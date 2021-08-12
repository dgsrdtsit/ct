<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtGenreTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtGenreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtGenre;

/**
 * Class GenreController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtGenreController extends Controller
{
    /**
     * Afficher tout les genres
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_genre_manager = $this->get(ServiceName::SRV_METIER_GENRE);

        // Récupérer tout les genres

        $_genres = $_genre_manager->getAllCtGenre();

        return $this->render('AdminBundle:CtGenre:index.html.twig', array(
            'genres' => $_genres,
        ));
    }

    /**
     * Affichage page modification genre
     *
     * @param CtGenre $_genre
     *
     * @return Render page
     */
    public function editAction(CtGenre $_genre)
    {
        if (!$_genre) {
            throw $this->createNotFoundException('Unable to find Genre entity.');
        }

        $_edit_form = $this->createEditForm($_genre);

        return $this->render('AdminBundle:CtGenre:edit.html.twig', array(
            'genre' => $_genre,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création genre
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_genre_manager = $this->get(ServiceName::SRV_METIER_GENRE);

        $_genre = new CtGenre();
        $_form       = $this->createCreateForm($_genre);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement genre
            $_genre_manager->saveCtGenre($_genre, 'new');

            $_genre_manager->setFlash('success', "Genre ajoutée");

            return $this->redirect($this->generateUrl('genre_index'));
        }

        return $this->render('AdminBundle:CtGenre:add.html.twig', array(
            'genre' => $_genre,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification genre
     * @param Request $_request requête
     * @param CtGenre $_genre
     * @return Render page
     */
    public function updateAction(Request $_request, CtGenre $_genre)
    {
        // Récupérer manager
        $_genre_manager = $this->get(ServiceName::SRV_METIER_GENRE);

        if (!$_genre) {
            throw $this->createNotFoundException('Unable to find Genre entity.');
        }

        $_edit_form = $this->createEditForm($_genre);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_genre_manager->saveCtGenre($_genre, 'update');

            $_genre_manager->setFlash('success', "Genre modifiée");

            return $this->redirect($this->generateUrl('genre_index'));
        }

        return $this->render('AdminBundle:CtGenre:edit.html.twig', array(
            'genre' => $_genre,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition genre
     * @param CtGenre $_genre The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtGenre $_genre)
    {
        $_form = $this->createForm(CtGenreType::class, $_genre, array(
            'action' => $this->generateUrl('genre_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création genre
     * @param CtGenre $_genre The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtGenre $_genre)
    {
        $_form = $this->createForm(CtGenreType::class, $_genre, array(
            'action' => $this->generateUrl('genre_update', array('id' => $_genre->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression genre
     * @param Request $_request requête
     * @param CtGenre $_genre
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtGenre $_genre)
    {
        // Récupérer manager
        $_genre_manager = $this->get(ServiceName::SRV_METIER_GENRE);

        $_form = $this->createDeleteForm($_genre);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression genre
            $_genre_manager->deleteCtGenre($_genre);

            $_genre_manager->setFlash('success', 'Genre supprimée');
        }

        return $this->redirectToRoute('genre_index');
    }

    /**
     * Création formulaire de suppression genre
     * @param CtGenre $_genre The Genre entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtGenre $_genre)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('genre_delete', array('id' => $_genre->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste genre
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_genre_manager = $this->get(ServiceName::SRV_METIER_GENRE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_genre_manager->deleteGroupCtGenre($_ids);
        }

        $_genre_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('genre_index'));
    }
}
