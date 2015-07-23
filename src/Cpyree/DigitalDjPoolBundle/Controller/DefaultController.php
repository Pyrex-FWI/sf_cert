<?php

namespace Cpyree\DigitalDjPoolBundle\Controller;

use Cpyree\DigitalDjPoolBundle\DigitalDjPoolEvents;
use Cpyree\DigitalDjPoolBundle\Entity\Track;
use Cpyree\DigitalDjPoolBundle\Event\TrackEventDispatcher;
use Cpyree\DigitalDjPoolBundle\Form\Type\TrackExpertSearchType;
use Cpyree\DigitalDjPoolBundle\Form\Type\TrackGlobalActionType;
use Cpyree\DigitalDjPoolBundle\Form\Type\TrackSearchType;
use Cpyree\DigitalDjPoolBundle\Service\CleanDir;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DefaultController extends Controller
{
    /**
     * @return \Cpyree\DigitalDjPoolBundle\Entity\TrackRepository
     */
    private function getTrackRepository(){
        /** @var \Cpyree\DigitalDjPoolBundle\Entity\TrackRepository $trackRepo */
        $trackRepo = $this->getDoctrine()->getRepository("CpyreeDigitalDjPoolBundle:Track", "ddp_manager");
        return $trackRepo;
    }

    /**
     * @Template("CpyreeDigitalDjPoolBundle:Default:v2/index.html.twig")
     */
    public function indexAction(){
        return array(
            'allCount'          => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->count(),
            'approbationAverage'=> $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->approbationAverage() ,
            'allGenreCount'     => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Trackgenre','ddp_manager')->count(),
            'positiveApp'       => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->countApproved(),
            'negativeApp'       => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->countNotApproved(),
            'neutralApp'        => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->countNeutralApproved(),
            'unApp'             => $this->getDoctrine()->getRepository('CpyreeDigitalDjPoolBundle:Track','ddp_manager')->countNotYetApproved()
        );
    }

    /**
     * @param int $page
     * @param string $sort
     * @param string $direction
     * @Template()
     */
    public function notApprovedTrackAction($page = 1, $sort = '', $direction = ''){


    }
    /**
     * @Template()
     */
    public function approvedTrackAction(){}
    /**
     * @Template()
     */
    public function neutralTrackAction(){}
    /**
     * @Template()
     */
    public function notCheckedTrackAction(){}

    /**
     * Build and init TrackExpert Search Form
     * @return TrackExpertSearchType
     */
    public function trackExpertSearchType(){
        $trackRepo = $this->getTrackRepository();
        /** @var TrackExpertSearchType $trackSearchType */
        $trackSearchType = $this->get('cpyree_digital_dj_pool.track_expert_search_type');
        $trackSearchType
            ->setUidMax($trackRepo->maxUid())
            ->setUidMin($trackRepo->minUid())
            ->setUidCurMin($trackRepo->minUid())
            ->setUidCurMax($trackRepo->maxUid())
            ->setTrackIdMin($trackRepo->minTrackId())
            ->setTrackIdMax($trackRepo->maxTrackId())
            ->setTrackIdCurMin($trackRepo->minTrackId())
            ->setTrackIdCurMax($trackRepo->maxTrackId())
            ->setDateReleaseMin($trackRepo->minReleaseDate())
            ->setDateReleaseMax($trackRepo->maxReleaseDate())
        ;
        return $trackSearchType;
    }

    /**
     * @param int $page
     * @param string $sort
     * @param string $direction
     * @internal param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     */
    public function listAction($page = 1, $sort = '', $direction = '', Request $reqest)
    {
        /** @var \Cpyree\DigitalDjPoolBundle\Entity\TrackRepository $trackRepo */
        $trackRepo = $this->getTrackRepository();

        $trackSearchType = $this->trackExpertSearchType();
        $trackSearchType->setApprovalMode(false);
        $form = $this->createForm(new TrackSearchType(), array());
        $form->handleRequest($reqest);

        $expertSearch = $this->createForm($trackSearchType);

        $expertSearch->handleRequest($reqest);

        $qb = $trackRepo->getTrackExpertSearchQuery($expertSearch);


        $paginator  = $this->get('knp_paginator');
        /** @var Paginator $paginator */
        $pagination = $paginator->paginate(
            //$trackRepo->searchQuery($form->getData()),
            $qb->getQuery(),
            $page,
            20/*limit per page*/,
            array(
                'defaultSortFieldName' => $sort,
                'defaultSortDirection' => $direction
            )
        );
        $pagination->setUsedRoute('cpyree_digital_dj_pool_track');
        $pagination->setPageRange(10);

        return array(
            'pagination' => $pagination,
            'searchForm' => $form->createView(),
            'expertSearchForm' => $expertSearch->createView(),
            'query' =>  $qb->getDQL(),
            //'actionOnAllTrack' => $this->actionOnAllTrackForm()->createView()

        );
    }

	/**
	 * @Template("CpyreeDigitalDjPoolBundle:Default:v2/approve_list.html.twig")
	 */
	public function approveViewAction($page = 1, $sort = '', $direction = '', Request $request){

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager("ddp_manager");
        $trackRepo = $this->getTrackRepository();
        $paginator  = $this->get('knp_paginator');

        $trackGlobalActionType = new TrackGlobalActionType();
        $globalActionsForm = $this->createForm($trackGlobalActionType);
        $globalActionsForm->handleRequest($request);

        if($this->handleMultipleTracksAction($globalActionsForm)){
            return $this->redirect($request->headers->get('referer'));
        }
        $trackSearchType = $this->trackExpertSearchType();
        $trackExpertSearchForm = $this->createForm($trackSearchType);
        $trackExpertSearchForm->handleRequest($request);

        $expertSearchQuery = $trackRepo->getTrackExpertSearchQuery($trackExpertSearchForm);

        if($trackExpertSearchForm->get('export_all')->getData() == 1){
            return $this->forward('CpyreeDigitalDjPoolBundle:Default:zip', array('qB' => $expertSearchQuery));
        }
        /** @var Paginator $paginator */
		$pagination = $paginator->paginate(
            $expertSearchQuery,
			$page,
			20/*limit per page*/,
			array(
				'defaultSortFieldName' => $sort,
				'defaultSortDirection' => $direction
			)
		);



		$pagination->setUsedRoute('cpyree_digital_dj_pool_approve_view');
		$pagination->setPageRange(10);

		return array(
            'pagination' => $pagination,
            'expertSearchForm' => $trackExpertSearchForm->createView(),
            'query' =>  $expertSearchQuery->getDQL(),
            'globalActionsForm' => $globalActionsForm->createView()
        );

    }
    /**
     * @param Track $track
     * @internal param $name
     * @Template(template="CpyreeDigitalDjPoolBundle:Default:index.html.twig")
     * @ParamConverter("track", class="CpyreeDigitalDjPoolBundle:Track", options={"id": "track_uid", "entity_manager":"ddp_manager"}, )
     * @return array
     */
    //public function flagForDeleteAction(Track $track){
    public function approveAction(Track $track, Request $request){
        $em = $this->getDoctrine()->getManager('ddp_manager');
        $track->setApproval(1);
        $track->setApprovalDate(time());
        $em->persist($track);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('Your changes have been saved')
        );
        $trackEventDispatcher = new TrackEventDispatcher($track);
        $this->get('event_dispatcher')->dispatch(DigitalDjPoolEvents::TRACK_APPROVAL, $trackEventDispatcher);
        return $this->approbationReturn(true, $request);
    }

    /**
     * @param Track $track
     * @internal param $name
     * @ParamConverter("track", class="CpyreeDigitalDjPoolBundle:Track", options={"id": "track_uid", "entity_manager":"ddp_manager"})
     * @return array
     */
    public function disapproveAction(Track $track, Request $request){

        $em = $this->getDoctrine()->getManager('ddp_manager');
        $track->setApproval(-1);
        $track->setApprovalDate(time());
        $em->persist($track);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('Your changes have been saved')
        );
        $trackEventDispatcher = new TrackEventDispatcher($track);
        $this->get('event_dispatcher')->dispatch(DigitalDjPoolEvents::TRACK_APPROVAL, $trackEventDispatcher);
        return $this->approbationReturn(true, $request);
    }

    /**
     * @param $uccess
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function approbationReturn($uccess, Request $request){
        if($request->getMethod() === "POST"){
            return;
        }
        if($request->isXmlHttpRequest()){
            $response = new JsonResponse();
            $response->setData(array(
                'success' => true,
                'message' => $this->get('translator')->trans('Your changes have been saved')
            ));
            return $response;
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Track $track
     * @internal param $name
     * @ParamConverter("track", class="CpyreeDigitalDjPoolBundle:Track", options={"id": "track_uid", "entity_manager":"ddp_manager"})
     * @return array
     */
    public function neutralapproveAction(Track $track, Request $request){
        $em = $this->getDoctrine()->getManager('ddp_manager');
        $track->setApproval(0);
        $track->setApprovalDate(time());
        $em->persist($track);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('Your changes have been saved')
        );

        $trackEventDispatcher = new TrackEventDispatcher($track);
        $this->get('event_dispatcher')->dispatch(DigitalDjPoolEvents::TRACK_APPROVAL, $trackEventDispatcher);
        return $this->approbationReturn(true, $request);
    }


    /**
     * Handle global approbation
     * @param \Symfony\Component\Form\Form $globalActionsForm
     * @return bool
     */
    private function handleMultipleTracksAction(\Symfony\Component\Form\Form  $globalActionsForm)
    {
        if($globalActionsForm->get('tracks')->getData()){
            $tracks = $globalActionsForm->get('tracks')->getData();
            $_controller = null;
            if($globalActionsForm->get('approve')->isClicked()){ $_controller = "CpyreeDigitalDjPoolBundle:Default:approve";}
            if($globalActionsForm->get('neutral')->isClicked()){ $_controller = "CpyreeDigitalDjPoolBundle:Default:neutralapprove";}
            if($globalActionsForm->get('disapprove')->isClicked()){ $_controller = "CpyreeDigitalDjPoolBundle:Default:disapprove";}
            if($_controller === null) return false;
            foreach($tracks as $id) {
                $this->forward($_controller, array(
                    'track_uid' => $id
                ));
            }
            return true;
        }
    }

    /**
     * @param Track $track
     * @ParamConverter("track", class="CpyreeDigitalDjPoolBundle:Track", options={"entity_manager":"ddp_manager"})
     * @return \Symfony\Component\HttpFoundation\am
     * amedResponse|void
     */
    public function streamAction(Track $track){
        $response = new StreamedResponse();

        if(!is_file($track->getFullPath())){
            $response->isNotFound();
        }else {
            $trackEventDispatcher = new TrackEventDispatcher($track);
            $this->get('event_dispatcher')->dispatch(DigitalDjPoolEvents::TRACK_PLAY, $trackEventDispatcher);
            $response->headers->add(array(
                'Cache-Control' => 'max-age=604800',
                'Content-type' => 'audio/mpeg',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length ' => filesize($track->getFullPath())
            ));
            $response->setCallback(function () use ($track) {
                echo readfile($track->getFullPath());
            });

        }
        return $response;
    }
    /**
     * @param Track $track
     * @ParamConverter("track", class="CpyreeDigitalDjPoolBundle:Track", options={"entity_manager":"ddp_manager"})
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function downloadAction(Track $track){
        $response = new StreamedResponse();

        if(!is_file($track->getFullPath())){
            $response->isNotFound();
        }else {
            $fileInfo = pathinfo($track->getFullPath());
            $response->headers->add(array(
                'Cache-Control' => 'max-age=604800',
                'Content-type' => 'audio/mpeg',
                'Content-Disposition' => 'attachment; filename="'. $fileInfo['filename'] .'"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length ' => filesize($track->getFullPath())
            ));
            $response->setCallback(function () use ($track) {
                $fp = fopen($track->getFullPath(), "r");
                while (!feof($fp))
                {
                    echo fread($fp, 65536);
                    flush(); // this is essential for large downloads
                }
                fclose($fp);
            });
        }
        return $response;
    }

    /**
     * @return Response
     */
    public function zipAction(\Doctrine\ORM\QueryBuilder $qB){
        ignore_user_abort(true);
        set_time_limit(0);
        $dataSet = $qB->getQuery()->execute();
        $filepaths = array();
        $allFilesSize = 0;
        foreach($dataSet as $track){
            /** @var Track $track */
            if(!is_file($track->getFullPath())) continue;
            $allFilesSize += filesize($track->getFullPath());
            $filepaths[] = $track->getFullPath();
            if($allFilesSize >= $this->container->getParameter('ddp.extract.max_size')){
                break;
            }
        }

        $file = tempnam("/volume1/_Archives/_DigitaldjPool/", "zip");
        $zip = new \ZipArchive();
        $zip->open($file, \ZipArchive::OVERWRITE);
        foreach($filepaths as $filepath){
            $zip->addFile($filepath);
        }
        $zip->close();
        header('Content-type: application/zip');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($file));
        header('Content-disposition: attachment; filename=DDP_Zip_Export_'.count($filepaths).'.zip');
        $f=fopen($file,'rb');
        while(!feof($f)) {
            echo fread($f, 1024);
        }
        fclose($f);
        unlink($file);


        /*
        $filesList = tempnam("/volume1/_Archives/_DigitaldjPool/", "files.txt");
        $finalZip = "/volume1/_Archives/_DigitaldjPool/DDP_Extract.zip";
        file_put_contents($filesList, implode(PHP_EOL,$filepaths));
        $cmd = "cat '$filesList'  | zip '$finalZip' -@";
        exec($cmd);
        $f=fopen($finalZip,'rb');
        while(!feof($f)) {
            echo fread($f, 1024);
        }
        fclose($f);
        unlink($filesList);
        unlink($finalZip);*/
        die();
    }
    
}
