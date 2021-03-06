<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\ProfileKeyword;
use AppBundle\Form\ProfileKeywordType;

/**
 * ProfileKeyword controller.
 *
 * @Route("/profile_keyword")
 * @Security("has_role('ROLE_USER')")
 */
class ProfileKeywordController extends Controller {

    /**
     * Lists all ProfileKeyword entities.
     *
     * @Route("/", name="profile_keyword_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:ProfileKeyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $profileKeywords = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'profileKeywords' => $profileKeywords,
        );
    }

    /**
     * Finds and displays a ProfileKeyword entity.
     *
     * @Route("/{id}", name="profile_keyword_show")
     * @Method("GET")
     * @Template()
     * @param ProfileKeyword $profileKeyword
     */
    public function showAction(ProfileKeyword $profileKeyword) {

        return array(
            'profileKeyword' => $profileKeyword,
        );
    }

}
