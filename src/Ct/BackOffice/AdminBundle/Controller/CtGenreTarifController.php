<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtGenreTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtGenreTarifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtGenre;

/**
 * Class GenreController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtGenreTarifController extends Controller
{
    /**
     * Afficher tout les genres
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_genre_tarif_manager = $this->get(ServiceName::SRV_METIER_GENRE_TARIF);

        // Récupérer tout les tarifs genres
        $ret_tarif_genres = $_genre_tarif_manager->getAllCtGenreTarifByGenre();

        return $this->render('AdminBundle:CtGenreTarif:index.html.twig', array(
            'genres' => $ret_tarif_genres,
        ));
    }

    /**
     * Affichage page modification genre
     *
     * @param CtGenreTarif $_genre_tarif_
     *
     * @return Render page
     */
    public function editAction(CtGenreTarif $_genre_tarif)
    {
        if (!$_genre_tarif) {
            throw $this->createNotFoundException('Unable to find GenreTarif entity.');
        }

        $_edit_form = $this->createEditForm($_genre_tarif);

        return $this->render('AdminBundle:CtGenreTarif:edit.html.twig', array(
            'genre' => $_genre_tarif,
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
        $_genre_tarif_manager = $this->get(ServiceName::SRV_METIER_GENRE_TARIF);

        $_genre_tarif = new CtGenreTarif();
        $_form       = $this->createCreateForm($_genre_tarif);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            $_date_time = new \DateTime("now");
            $_current_year = $_date_time->format('Y');
            $_genre_tarif->setGrtAnnee($_current_year);
            // Enregistrement genre_tarif
            $_return = $_genre_tarif_manager->saveCtGenreTarif($_genre_tarif, 'new');

            if ($_return) {
                $_genre_tarif_manager->setFlash('success', "Genre Tarif ajoutée");

                return $this->redirect($this->generateUrl('genre_tarif_index'));
            }

        }

        return $this->render('AdminBundle:CtGenreTarif:add.html.twig', array(
            'genre_tarif' => $_genre_tarif,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification genre
     * @param Request $_request requête
     * @param CtGenreTarif $_genre_tarif
     * @return Render page
     */
    public function updateAction(Request $_request, CtGenreTarif $_genre_tarif)
    {
        // Récupérer manager
        $_genre_tarif_manager = $this->get(ServiceName::SRV_METIER_GENRE_TARIF);

        if (!$_genre_tarif) {
            throw $this->createNotFoundException('Unable to find Genre entity.');
        }

        $_edit_form = $this->createEditForm($_genre_tarif);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_genre_tarif_manager->saveCtGenreTarif($_genre_tarif, 'update');

            $_genre_tarif_manager->setFlash('success', "GenreTarif modifiée");

            return $this->redirect($this->generateUrl('genre_tarif_index'));
        }

        return $this->render('AdminBundle:CtGenreTarif:edit.html.twig', array(
            'genre' => $_genre_tarif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition genre
     * @param CtGenreTarif $_genre_tarif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtGenreTarif $_genre_tarif)
    {
        $_form = $this->createForm(CtGenreTarifType::class, $_genre_tarif, array(
            'action' => $this->generateUrl('genre_tarif_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création genre
     * @param CtGenreTarif $_genre_tarif_ The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtGenreTarif $_genre_tarif)
    {
        $_form = $this->createForm(CtGenreTarifType::class, $_genre_tarif, array(
            'action' => $this->generateUrl('genre_tarif_update', array('id' => $_genre_tarif->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression genre
     * @param Request $_request requête
     * @param CtGenreTarif $_genre_tarif
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtGenreTarif $_genre_tarif)
    {
        // Récupérer manager
        $_genre_tarif_manager = $this->get(ServiceName::SRV_METIER_GENRE_TARIF);

        $_form = $this->createDeleteForm($_genre_tarif);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression genre
            $_genre_tarif_manager->deleteCtGenreTarif($_genre_tarif);

            $_genre_tarif_manager->setFlash('success', 'Genre tarif supprimée');
        }

        return $this->redirectToRoute('genre_tarif_index');
    }

    /**
     * Création formulaire de suppression genre
     * @param CtGenreTarif $_genre_tarif The Genre entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtGenreTarif $_genre_tarif)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('genre_tarif_delete', array('id' => $_genre_tarif->getId())))
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
        $_genre_tarif_manager = $this->get(ServiceName::SRV_METIER_GENRE_TARIF);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_genre_tarif_manager->deleteGroupCtGenreTarif($_ids);
        }

        $_genre_tarif_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('genre_tarif_index'));
    }
}
