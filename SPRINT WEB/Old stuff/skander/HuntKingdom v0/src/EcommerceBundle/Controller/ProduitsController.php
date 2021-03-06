<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Form\RechercheType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProduitsController extends Controller
{
    public function categorieAction($categorie)
    {
        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository('EcommerceBundle:Produits')->byCategorie($categorie);

        $categorie = $em->getRepository('EcommerceBundle:Categories')->find($categorie);
        if (!$categorie) throw $this->createNotFoundException('La page n\'existe pas.');
        return $this->render('@Ecommerce/Default/produits/layout/produits.html.twig', array('produits' => $produits));
    }


    public function produitsAction(Request $request)
    {   $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository('EcommerceBundle:Produits')->findBy(array('disponible' => 1));
        if ($session->has('panier'))
            $panier = $session->get('panier');
        else
            $panier = false;
        return $this->render('@Ecommerce/Default/produits/layout/produits.html.twig', array('produits' => $produits,
                                                                                                  'panier' => $panier));
    }

    public function presentationAction($id,Request $request)
    {   $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('EcommerceBundle:Produits')->find($id);
        if (!$produit) throw $this->createNotFoundException('La page n\'existe pas.');

        if ($session->has('panier'))
            $panier = $session->get('panier');
        else
            $panier = false;
        return $this->render('@Ecommerce/Default/produits/layout/presentation.html.twig', array('produit' => $produit,
                                                                                                     'panier' => $panier));
    }


    public function rechercheAction()
    {
        $form = $this->createForm(new RechercheType());
        return $this->render('@Ecommerce/Default/Recherche/modulesUsed/recherche.html.twig', array('form' => $form->createView()));
    }

    public function rechercheTraitementAction()
    {
        $form = $this->createForm(new RechercheType());

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));
            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository('EcommerceBundle:Produits')->recherche($form['recherche']->getData());
        } else {
            throw $this->createNotFoundException('La page n\'existe pas.');
        }

        return $this->render('@Ecommerce/Default/produits/layout/produits.html.twig', array('produits' => $produits));
    }
    public function renderAction()
    {
        return $this->render('@Ecommerce/Default/produits/layout/render.html.twig');
    }
}
