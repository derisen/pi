<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Caption;
use AppBundle\Entity\ProfileElement;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Video controller.
 *
 * @Route("/video")
 * @Security("has_role('ROLE_USER')")
 */
class VideoController extends Controller {

    /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Video e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 25, array(
            'defaultSortFieldName' => 'e.id',
            'defaultSortDirection' => 'asc',
        ));

        return array(
            'videos' => $videos,
        );
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}", name="video_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Video $video
     */
    public function showAction(Video $video) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ));
        if (!$videoProfile) {
            $videoProfile = new VideoProfile();
        }

        $elements = $em->getRepository(ProfileElement::class)->findAll();

        return array(
            'video' => $video,
            'elements' => $elements,
            'videoProfile' => $videoProfile,
        );
    }

    /**
     * @Route("/{id}/refresh", name="video_refresh")
     * @Method("GET")
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Video $video
     */
    public function refreshAction(Video $video) {
        $em = $this->getDoctrine()->getManager();
        $client = $this->get('yt.client');
        $client->updateVideos(array($video));
        $em->flush();
        $this->addFlash('success', 'The video data has been updated.');
        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }

    /**
     * @Route("/{id}/captions", name="video_captions")
     * @Method("GET")
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Video $video
     */
    public function captionsAction(Video $video) {
        $oldCaptions = $video->getCaptions()->toArray();
        $em = $this->getDoctrine()->getManager();
        $captionRepo = $em->getRepository(Caption::class);
        $client = $this->get('yt.client');

        $captionIds = $client->captionIds($video);

        foreach ($captionIds as $captionId) {
            $caption = $captionRepo->findOneBy(array('youtubeId' => $captionId));
            if (!$caption) {
                $caption = new Caption();
                $caption->setYoutubeId($captionId);
                $em->persist($caption);
            }
            $video->addCaption($caption);
            $caption->setVideo($video);
        }
        $em->flush();
        $this->addFlash('success', 'The video captions have been updated.');
        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }

}
