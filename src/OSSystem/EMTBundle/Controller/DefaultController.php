<?php

namespace OSSystem\EMTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\QueryBuilder;

use OSSystem\EMTBundle\Entity\User;
use OSSystem\EMTBundle\Entity\StaticPage;
use OSSystem\EMTBundle\Entity\Document;
use OSSystem\EMTBundle\Entity\Criteria;
use OSSystem\EMTBundle\Entity\Message;
use OSSystem\EMTBundle\Entity\Conference;
use OSSystem\EMTBundle\Entity\TimeNotifications;
use OSSystem\EMTBundle\Entity\Country;

use OSSystem\EMTBundle\Form\conferenceTerms;
use OSSystem\EMTBundle\Form\ConferenceOfficerType;
use OSSystem\EMTBundle\Form\ConferenceType;
use OSSystem\EMTBundle\Form\ConferenceTypeStep2;
use OSSystem\EMTBundle\Form\ConferenceTypeStep3;
use OSSystem\EMTBundle\Form\ConferenceTypeStep4;
use OSSystem\EMTBundle\Form\ConferenceStatusChange;
use OSSystem\EMTBundle\Form\MessageType;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
      
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        
        die($user);
        if($user->getOrganization()!=''){
            return $this->render('FOSUserBundle:Profile:edit.html.twig');
        };
        return $this->render('OSSystemEMTBundle:Default:index.html.twig');
    }
    
    public function helloAction($name)
    {
        die();
        return $this->render('OSSystemEMTBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function myConferencesAction()
    {
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if(!$user->getOrganization()){
            return $this->redirect($this->generateUrl('fos_user_profile_edit'));
        } else{
            
            /*Get list conferences*/
            $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
            $conferences = $conferenceRepository->getAllConferencesByUser($user)->getArrayResult();
            
            return $this->render('OSSystemEMTBundle:Default:myconferences.html.twig', array(
                'conferences' => $conferences));
        }
        
        
        
        return $this->render('OSSystemEMTBundle:Default:myconferences.html.twig', array('user' => $user));
    }
    
    public function conferencesListAction($archives = null, $dblentries = null)
    {
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $arch = false;
        if($archives){
            $arch = true;
        };
        
        $conferenceLocation = $user->getProfileType();
            switch($conferenceLocation){
                    case User::PROFILE_TYPE_EUCOMED:
                        $loc = 'EU';
                        break;
                    case User::PROFILE_TYPE_MIDDLE_EAST:
                        $loc = 'ME';
                        break;
                    default: 
                        $loc = '';
                };
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $areas = $conferenceRepository->getTherapeuticArea();
        $statuses = $conferenceRepository->getAllStatus();
        $request = $this->getRequest();

        $params = array();
        if ('POST' === $request->getMethod()) {           

            if($id = $request->request->get('filters_id', false)){ $params['id'] = (int)$id; }
            if($medical_area = $request->request->get('filters_area', false)){ $params['area'] = (string)$medical_area;}
            if($city = $request->request->get('filters_city', false)){ $params['city'] = (string)$city;}
            if($status = $request->request->get('filters_status', false)){ $params['status'] = (string)$status;}
            if($name = $request->request->get('filters_name', false)){ $params['name'] = (string)$name;}
           
            if($dblentries){
                $params['status'] = 9;
            }
           $conferences = $conferenceRepository->filterConferences($loc, $arch, $params);
           return $this->render('OSSystemEMTBundle:Default:conferences_all.html.twig', array(
                    'conferences' => $conferences,
                    'areas' => $areas,
                    'statuses' => $statuses,
                    'filter' => $params,
                    'arch' => $arch,
                    'dblentries' => $dblentries
                   ));
        }else{
            if($dblentries){
                $params['status'] = 9; 
                $conferences = $conferenceRepository->filterConferences($loc, $arch, $params);
            } else{
                $conferences = $conferenceRepository->getAllConferencesBy($loc, $arch)->getArrayResult();
            }

            return $this->render('OSSystemEMTBundle:Default:conferences_all.html.twig', array(
                    'conferences' => $conferences,
                    'areas' => $areas,
                    'statuses' => $statuses,
                    'arch' => $arch,
                    'dblentries' => $dblentries
                    ));            
        }
        
    }
    
    public function conferenceViewAction($conferenceId = null, Request $request){
        $sc = $this->get('security.context');
        $user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        /*Get conferences*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        $countryId = $conference->getAssesmentCountry()->getId();

        $flagCreateForm = false;
        $flagIsRecommendation = false;
        
        if ('EU' == $conference->getAssesmentCountry()->getLocation() || '' == $conference->getAssesmentCountry()->getLocation()){
            if ($user->getProfileType() == User::PROFILE_TYPE_EUCOMED){
                $flagCreateForm = true;
            }
        }elseif($conference->getAssesmentCountry()->getLocation() == 'ME'){
            if ($user->getProfileType() == User::PROFILE_TYPE_MIDDLE_EAST){
                $flagCreateForm = true;
            }
        }
        
            $form = $this->createFormBuilder();

            foreach ($conference->getCriterias() as $crit){
                $form->add('state_'.$crit->getId(),'choice',array(
                                'required' => true,
                                'choices' => array( Criteria::CRITERIA_STATE_NA  => 'Not applicable', 
                                    Criteria::CRITERIA_STATE_TOBEREVIEWED => 'To be reviewed',
                                    Criteria::CRITERIA_STATE_COMPLIANT => 'Compliant',
                                    Criteria::CRITERIA_STATE_NOTCOMPLIANT => 'Not compliant',
                                    Criteria::CRITERIA_STATE_MISSING => 'Missing',
                                    Criteria::CRITERIA_STATE_UNDERCORRECTION => 'Under correction',
                                    ),
                                'data' => $crit->getStatus(),
                                'label' => $crit->getCriteria()->getTitle(),
                            ));
                $form->add('comment_'.$crit->getId(),'text',array(
                                'required' => false,
                                'data' => $crit->getComment(),
                                'translation_domain' => 'OSSystemEMTBundle', 
                                'attr' => array('placeholder' => 'criteria_status_comment',
                                'maxlength' => 255)

                            ));
                $form->add('document_'.$crit->getId(),'file',array(
                                'required' => false,
                            ));
            }
            $form = $form->getForm();
            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);
                $data = $form->getData();
                foreach ($conference->getCriterias() as $crit){          
                    if ($data["state_" . $crit->getId()] !== null ){
                        $status = $data["state_" . $crit->getId()]; 
                        $crit->setStatus($status);
                    }
                    if ($comment = $data["comment_" . $crit->getId()]){
                        $crit->setComment($comment);
                    }
                        $i = 'document_'.$crit->getId();
                        $uploadedFile = $request->files->get("form");
                        $uploadedFile = $uploadedFile[$i];
                        if (!is_null($uploadedFile)) {
                            $document = new \OSSystem\EMTBundle\Entity\Document();
                            $document->setConference($conference);
                            $document->setTarget(Document::DOCUMENT_TARGET_NA);
                            $document->setFile($uploadedFile, $conference->getId());
                            $crit->setDocument($document);
                            
                            $em->persist($document);
                        }
                }
                $conference->setIsCORecommendation($flagIsRecommendation);
                $em->flush();
            }
            $form = $form->createView(); 

        return $this->render('OSSystemEMTBundle:Default:conferenceView.html.twig', array(
                'conference' => $conference,
                'user' => $user,
                'form' => $form,
                'countryId' => $countryId
            ));

    }
    
    public function conferenceDeleteCriteriaFileAction($conferenceId = null, $criteriaId = null) {
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        $conferenceCriteriaStateRepository = $em->getRepository('OSSystemEMTBundle:ConferenceCriteriaState');
        $criteria = $conferenceCriteriaStateRepository->find($criteriaId);
        
        $document = $criteria->getDocument();
        $document->remove();
        
        $em->persist($document);
        $em->flush();
        
        return $this->render('OSSystemEMTBundle:Default:conferenceDeleteCriteriaFile.html.twig', array(
                'conference' => $conferenceId,
                'criteria' => $criteriaId,
            ));
    }
    
    public function conferenceDeleteAction($conferenceId = null){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conference*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        
        $conference = $conferenceRepository->find($conferenceId);
        
        /*clear dependencies*/
        $documents = $em->getRepository('OSSystemEMTBundle:Document')->findBy(array('conference' => $conference));
        
        foreach($documents as $document){
             $document->setConference(null);
             $em->remove($document);
        }
        
        $messages = $em->getRepository('OSSystemEMTBundle:Message')->findBy(array('conference' => $conference));
        
        foreach($messages as $message){
            $message->setConference(null);
            $em->remove($message);
        }
        
        /*Delete conference*/
        $em->remove($conference);
        $em->flush();
        
        return $this->redirect($this->generateUrl('os_system_emt_home'));

    }
    
    public function conferenceTermsAction($preclearance = false){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        
        $isPreClearance = (bool)$preclearance;
        
        $form = $this->createForm(new conferenceTerms());
        
        $conference = $conferenceRepository->getOpenConference($user, $isPreClearance);
        if (!$conference){
            $conference = $conferenceRepository->createOpenConference($user);
            $conferenceRepository->initializeCriterias($conference);
            if ($isPreClearance){
                $conference->setIsPreclearance(true);
            }else{
                $conference->setIsPreclearance(false);
            }
            $em->persist($conference);
            $em->flush();
        }
        $conferenceId =  $conference->getId();
        
        if ($isPreClearance){
            $template = 'OSSystemEMTBundle:Default:ConferenceEdit/conferencesTermsPreclearance.html.twig';
        }else{
            $template = 'OSSystemEMTBundle:Default:ConferenceEdit/conferencesTerms.html.twig';
        }
        
        return $this->render($template, 
                array(
                    'conferenceId' => $conferenceId,
                    'form' => $form->createView(),
                ));
    }
    
    public function conferenceOfficerAction($conferenceId = null){
        
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $request = $this->getRequest();
        
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        };
        

        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        
        if (!$conferenceId){
            $conference = $conferenceRepository->getOpenConference($user);
            if (!$conference){
                $conference = $conferenceRepository->createOpenConference($user);
            }
        }else{
            $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        }    
        
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_home'));

        }
        
        
        $form = $this->createForm(new ConferenceOfficerType(), $conference);
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                
                $conference->setSnapshot($conferenceRepository->generateSnapshot($conference));
                $conference->setChangedFields(array());
                
                $em->persist($conference);
                $em->flush();
            }
            if ($request->get('updateAndBack')) {
                return $this->redirectToRoute('os_system_emt_conference_view', array('conferenceId' => $conferenceId));
            }
            
        }

        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/edit_conf_officer.html.twig', array(
                'conference' => $conference,
                'conferenceId' => $conferenceId,
                'step' => 1,
                'form' => $form->createView(),
        ));
    }
    
    public function conferenceEdit1Action($conferenceId = null, $country = 0){

        
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $request = $this->getRequest();
        
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        };
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');

        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_home'));

        }
        if (0 != $country) {
            $countries = $em->getRepository('OSSystemEMTBundle:Country');
            $countries = $countries->findOneBy(array('id'=>$country));
            $conference->setAssesmentCountry($countries);
            $em->flush();
        } else {
            $countries = $conference->getAssesmentCountry();
        }
        
        $lockedFields = array();
        
        if ($conferenceRepository->doLockFields($conference)){
            if ($conference->getTitle()){
                $lockedFields[] = 'title';
            }
            if ($conference->getAcronym()){
                $lockedFields[] = 'acronym';
            }
            if ($conference->getTherapeuticArea()){
                $lockedFields[] = 'therapeuticArea';
            }
            if ($conference->getTherapeuticAreaOther()){
                $lockedFields[] = 'therapeuticAreaOther';
            }
            if ($conference->getStartConferenceDate()){
                $lockedFields[] = 'startConferenceDate';
            }
            if ($conference->getEndConferenceDate()){
                $lockedFields[] = 'endConferenceDate';
            }
            if ($conference->getOrganizingCompaniesNames()){
                $lockedFields[] = 'organizingCompaniesNames';
            }
            if ($conference->getContactPersonsNames()){
                $lockedFields[] = 'contactPersonsNames';
            }
            if ($conference->getOrganizingEmails()){
                $lockedFields[] = 'organizingEmails';
            }
            if ($conference->getWebsite()){
                $lockedFields[] = 'website';
            }
            if ($conference->getEmail()){
                $lockedFields[] = 'email';
            }
            if ($conference->getAutopublishing() !== null){
                $lockedFields[] = 'autopublishing';
            }
            
            //step 3 merge
            if ($conference->getAssesmentNameVenue() !== null ){
                $lockedFields[] = 'assesmentNameVenue';
            }
            if (count($conference->getAssesmentCategoryVenue())){
                $lockedFields[] = 'assesmentCategoryVenue';
            }
            if ($conference->getAssesmentCity()){
                $lockedFields[] = 'assesmentCity';
            }
            if ($conference->getAssesmentCountry() || $country != 0){
                $lockedFields[] = 'assesmentCountry';
            }
            if ($conference->getAssesmentLocalNA()){
                $lockedFields[] = 'assesmentLocalNA';
            }
            if ($conference->getAssesmentProposedAccomodation()){
                $lockedFields[] = 'assesmentProposedAccomodation';
            }
            if ($conference->getAssesmentProposedAccomodationDocument()){
                $lockedFields[] = 'assesmentProposedAccomodationDocumentFile';
            }
            
        }
        if ($country != 0){
            $lockedFields[] = 'assesmentCountry';
        }
        
        $options = array('lockedFields' => $lockedFields);
        
        $form = $this->createForm(new ConferenceType(), $conference, $options);
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                $flagFileError = false;
                        
                $assesmentProposedAccomodationDocumentFile =  $form['assesmentProposedAccomodationDocumentFile']->getData();
                
                if ($assesmentProposedAccomodationDocumentFile){
                    $response = $conference->setAssesmentProposedAccomodationDocument($assesmentProposedAccomodationDocumentFile);
                    if ($response == 'os_emt_fileextension_error'){
                        $request->getSession()->getFlashBag()->add(
                            'error',
                            'Uploading this filetype is forbidden!'
                        );
                        $flagFileError = true;
                    }
                }
                
                $em->persist($conference);
                $em->flush();
                
                if (false ==  $flagFileError)
                    return $this->redirect($this->generateUrl('os_system_emt_conference_edit_step2', array('conferenceId' => $conference->getId())));
            }
        }

        if ($conference->getIsPreClearance()){
            $mindate = strtotime("+180 day");
            $mindays = 180;
        }else{    
            $mindate = strtotime("+75 day");
            $mindays = 75;
            if ("ME" == $countries->getLocation()) {
                $mindate = 0;
                $mindays = 0;
            }
        }
        $maxdate = strtotime("+6 year");

        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/step1.html.twig', 
                array('conference' => $conference, 
                    'step' => 1,
                    'form' => $form->createView(),
                    'maxdate' => $maxdate,
                    'mindate' => $mindate,
                    'mindays' => $mindays,
                    'country' => $country,
                    'countryes' => $countries
                    ));
    }

    public function conferenceEdit2Action($conferenceId){
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        }
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_homes'));
            
        }
        
        $lockedFields = array();
        if ($conferenceRepository->doLockFields($conference)){
            if ($conference->getDelegatesMoreThan1Country() !== null ){
                $lockedFields[] = 'delegatesMoreThan1Country';
            }
            if (count($conference->getDelegatesCountries())){
                $lockedFields[] = 'delegatesCountries';
            }
            if ($conference->getDelegatesAnticipate()){
                $lockedFields[] = 'delegatesAnticipate';
            }
        }
        $options = array('lockedFields' => $lockedFields);
        
        $form = $this->createForm(new ConferenceTypeStep2(), $conference, $options);
        
        
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                $em->persist($conference);
                $em->flush();
                
                return $this->redirect($this->generateUrl('os_system_emt_conference_edit_step3', array('conferenceId' => $conference->getId())));
            }
        }
        
        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/step2.html.twig', 
                array('conference' => $conference, 
                    'step' => 2,
                    'form' => $form->createView(),
                    ));
    }
    
    public function conferenceEdit3OldAction($conferenceId){
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        }
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_homes'));
            
        }
        
        $lockedFields = array();
        if ($conferenceRepository->doLockFields($conference)){
            if ($conference->getAssesmentNameVenue() !== null ){
                $lockedFields[] = 'assesmentNameVenue';
            }
            if (count($conference->getAssesmentCategoryVenue())){
                $lockedFields[] = 'assesmentCategoryVenue';
            }
            if ($conference->getAssesmentCity()){
                $lockedFields[] = 'assesmentCity';
            }
            if ($conference->getAssesmentCountry()){
                $lockedFields[] = 'assesmentCountry';
            }
            if ($conference->getAssesmentLocalNA()){
                $lockedFields[] = 'assesmentLocalNA';
            }
            if ($conference->getAssesmentProposedAccomodation()){
                $lockedFields[] = 'assesmentProposedAccomodation';
            }
            if ($conference->getAssesmentProposedAccomodationDocument()){
                $lockedFields[] = 'assesmentProposedAccomodationDocumentFile';
            }
        }
        $options = array('lockedFields' => $lockedFields);
        
        $form = $this->createForm(new ConferenceTypeStep3(), $conference, $options);
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                
                $flagFileError = false;
                        
                $assesmentProposedAccomodationDocumentFile =  $form['assesmentProposedAccomodationDocumentFile']->getData();
                
                if ($assesmentProposedAccomodationDocumentFile){
                    $response = $conference->setAssesmentProposedAccomodationDocument($assesmentProposedAccomodationDocumentFile);
                    if ($response == 'os_emt_fileextension_error'){
                        $request->getSession()->getFlashBag()->add(
                            'error',
                            'Uploading this filetype is forbidden!'
                        );
                        $flagFileError = true;
                    }
                }
                
                $em->persist($conference);
                $em->flush();
                
                if (false ==  $flagFileError)
                    return $this->redirect($this->generateUrl('os_system_emt_conference_edit_step4', array('conferenceId' => $conference->getId())));
                
            }
        }
        
        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/step3.html.twig', 
                array('conference' => $conference, 
                    'step' => 3,
                    'form' => $form->createView(),
                    ));
    }
    
    public function conferenceEdit3Action($conferenceId){
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        }
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        $countryId = $conference->getAssesmentCountry()->getId();
        
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_homes'));
            
        }
        
        $lockedFields = array();
        $lockMode = $conferenceRepository->doLockFields($conference);
        if ($lockMode){
            if ($conference->getEventProgramme()){
                $lockedFields[] = 'eventProgramme';
            }
            if (count($conference->getComments())){
                $lockedFields[] = 'comments';
            }
        }
        $options = array('lockedFields' => $lockedFields);
        
        $form = $this->createForm(new ConferenceTypeStep3(), $conference, $options);
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                
                $i = 0;
                foreach($request->files->get("additional_files") as $uploadedFile) {
                    if (null != $uploadedFile){
                        $files_comment = $request->get("additional_files_comment");
                        $comment = $files_comment[$i++];
                        $conference->uploadNewDocument($uploadedFile, $comment);
                    }
                }
                
                
                if (!$lockMode){
                    /*
                     * we'll not allow user to delete documents, if conference in review state
                     */
                    $deletedocuments = $request->get('deletelistdocuments');
                    if ($deletedocuments){
                        foreach ($deletedocuments as $deleteDocument){
                            $document = $em->getRepository("OSSystemEMTBundle:Document")->find($deleteDocument);
                            if (!$document){
                                continue;
                            }
                            if ($conference->getDocuments()->contains($document)){
                                $conference->removeDocument($document);
                                $em->remove($document);
                            }
                        }
                    }
                }
                
                
                if ($request->get('save') == 'Submit for review'){
                    $profile_type = $conference->getUser()->getProfileType();
                    $conferenceRepository->initializeCriterias($conference);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('os_system_emt_conference_preview', array('conferenceId' => $conference->getId(), 'countryId' => $countryId)));
                
                }else{
                    if ($conference->getIsPreclearance()){
                        $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",10) ); //Saved for preclearance
                    }else{
                        $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",1) ); //Saved
                    }
                    $conference->setIsNew(false);
    
                }
                
                $em->persist($conference);
                $em->flush();
                
                return $this->redirect($this->generateUrl('os_system_emt_conference_preview', array('conferenceId' => $conference->getId(), 'countryId' => $countryId)));
            }
        }
        
        
        
        
        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/step3.html.twig', 
                array('conference' => $conference, 
                    'step' => 3,
                    'form' => $form->createView(),
                    'lockMode' => $lockMode,
                    ));
    }
    
    public function conferencePreviewAction($conferenceId){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conferences*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        $countryId = $conference->getAssesmentCountry()->getId();

        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/preview.html.twig', 
                array('conference' => $conference,
                    'user' => $user,
                    'step' => 4,
                    'errors' => array(),
                    'successfull' => false,
                    'countryId' => $countryId
                    ));
    }
    
    public function conferenceVaultAction($conferenceId){
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $message = false;
    
        if ($user == 'anon.'){
            return $this->redirect($this->generateUrl('os_system_emt_home'));
        }
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        if (!$conferenceId){
            $conference = $conferenceRepository->getOpenConference($user);
            if (!$conference){
                $conference = $conferenceRepository->createOpenConference($user);
            }
        }else{
            $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        }  
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Error creating conference!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_homes'));

        }
        
        $documents = $em->getRepository('OSSystemEMTBundle:Document')->findBy(array('conference' => $conference));
        
        $form = $this->createFormBuilder()->getForm();
         
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) { 
                $i = 0;
                foreach($request->files->get("additional_files") as $uploadedFile) {
                    
                    if (null != $uploadedFile){
                        
                        $document = new \OSSystem\EMTBundle\Entity\Document();
                        
                        $document->setConference($conference);
                        $document->setTarget( Document::DOCUMENT_TARGET_VAULT );
                        $document->setFile($uploadedFile, $conference->getId());
                        $files_comment = $request->get("additional_files_comment");
                        $comment = $files_comment[$i++];
                        if ($comment){
                            $document->setComment($comment);
                        }
                        
                        $em->persist($document);
                        $message = "1";
                    }

                }
                
                $em->flush();
            
                $deletedocuments = $request->get('deletelistdocuments');
                if ($deletedocuments){
                    
                    foreach ($deletedocuments as $deleteDocument){
                        $document = $em->getRepository("OSSystemEMTBundle:Document")->find($deleteDocument);
                        
                        if (!$document){
                            continue;
                        };
                        
                        if ($document->getConference() == $conference){
                            $document->setConference(null);
                            $em->remove($document);
                            $em->flush();
                        }
                        
                        if ($conference->getDocuments()->contains($document)){
                            $conference->removeDocument($document);
                            $em->remove($document);
                        }
                    }
                }
                return $this->redirect($this->generateUrl('os_system_emt_conference_vault', array('conferenceId' => $conferenceId, 'message' => $message)));
            }
        }
        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/vault.html.twig',
                array('conference' => $conference, 
                    'step' => 10,
                    'documents' => $documents,
                    'form' => $form->createView(),
                    'message' => $message
                    ));
    }
    
    public function conferenceSubmitAction($conferenceId, $partialSubmission = false){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conferences*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        $countryId = $conference->getAssesmentCountry()->getId();
        
        if ($conference->getIsNew()){
            if ($conference->getIsPreclearance()){
                $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",10) ); //Saved for preclearance
            }else{
                $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",1) ); //Saved
            }
            $conference->setIsNew(false);
        }

        $errors = $this->validateMandatoryFieldsOfConference($conference, $user);

        if (($conference->getConferenceStatus() != $em->find("OSSystemEMTBundle:Status",1))&&
            ($conference->getConferenceStatus() != $em->find("OSSystemEMTBundle:Status",10))&&
            ($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWED)) &&
            ($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWEDFORPARTIALSUBMISSION)) &&
            ($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWEDFORPRECLEARANCE))
            )
        {
            $errors[] = array("step" => null, "message" => "Sorry, but status of conference (" . $conference->getConferenceStatus() . ") does not allow you to submit it");
        }

        $successful = false;
        
        if (!count($errors)){
            $conference->setSubmissionDate(new \DateTime('now'));
            if ($conference->getIsPreclearance()){
                $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",11) ); //To be reviewed for preclearance
                /* send info to email user*/
                $templatename = 'OSSystemEMTBundle:Default:Notifications/PreClearance/_msgPAReceipt.html.twig';
                $subject = 'Acknowledgement of receipt conference Pre-clearance';
                $to = $user->getEmail();
                $params = $conference;
                $this->container->get('emt.notification')->sendNotificationEmail($subject,$to,$templatename,$params);
                $this->container->get('emt.savemail')->MailToMessage($subject,$to,$templatename,$params);
            }elseif ($partialSubmission){
                $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",Conference::CONFERENCE_STATUS_TOBEREVIEWEDFORPARTIALSUBMISSION) ); //To be reviewed as partial submission
                $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSReceipt.html.twig';
                $subject = 'Ethical Med Tech - Conference Vetting System - Acknowledgement of receipt conference(Partial Submission) ';
                $to = $user->getEmail();
                $params = $conference;
                $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename,$params);
                $this->container->get('emt.savemail')->MailToMessage($subject,$to,$templatename,$params);
            }else{
                $conference->setConferenceStatus( $em->find("OSSystemEMTBundle:Status",2) ); //To be reviewed
                /* send info to email user*/
                $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/_msgReceipt.html.twig';
                $subject = 'Ethical Med Tech - Conference Vetting System - Request for Assessment';
                $to = $conference->getOrganizingEmails();
                $params = $conference;
                $this->container->get('emt.notification')->sendNotificationEmail($subject,$to,$templatename,$params);
                $this->container->get('emt.savemail')->MailToMessage($subject,$to,$templatename,$params);
            }
            $conferenceRepository->initializeCriterias($conference);
            $conferenceRepository->traceUpdatedFields($conference);
            $em->flush();
            $successful = true;
        }
        
        return $this->render('OSSystemEMTBundle:Default:ConferenceEdit/preview.html.twig', 
                array('conference' => $conference,
                    'user' => $user,
                    'step' => 4,
                    'errors' => $errors,
                    'successfull' => $successful,
                    'countryId' => $countryId
                    ));
    }
    
    public function conferenceTransformAction($conferenceId){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conferences*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getOpenConferenceById($user, $conferenceId);
        $countryId = $conference->getAssesmentCountry()->getId();
        
        if (!$conference){
            $request->getSession()->getFlashBag()->add(
                'error',
                'Conference not found!'
            );
            return $this->redirect($this->generateUrl('os_system_emt_conference_preview', array('conferenceId' => $conferenceId, 'countryId' => $countryId)));
        }
        
        $conference->setIsPreclearance(false);
        $em->flush();
        
        return $this->redirect($this->generateUrl('os_system_emt_conference_preview', array('conferenceId' => $conference->getId(), 'countryId' => $countryId)));
    }
    
    public function conferenceStatusChangeAction($conferenceId){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        if (!(
                ($user->getProfileType() == User::PROFILE_TYPE_EUCOMED) ||
                ($user->getProfileType() == User::PROFILE_TYPE_MIDDLE_EAST)
              )
            )
        {
            
            return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
        }
        
        
        /*Get conference*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference',$conferenceId);
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $messageLocation = "";
        if ("ME" === $conference->getAssesmentCountry()->getLocation()) {
            $messageLocation = "ME/";
        } else {
            $messageLocation = "";
        }
        
        $form = $this->createForm(new ConferenceStatusChange(), $conference);
        
        $request = $this->getRequest();
        $status_old = $conference->getConferenceStatus()?$conference->getConferenceStatus()->getId():null;
               
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            $data = $form->getData();
            if ($form->isValid()) { 
                
                $conference->setSnapshot($conferenceRepository->generateSnapshot($conference));
                $conference->setChangedFields(array());
                $conference->setConferenceStatusDate(new \DateTime('now'));
                
                $em->persist($conference);
                $em->flush();

            }
              /* send info to regularSubmmition email user*/
            
            $params = $conference;
            switch ($conference->getConferenceStatus()->getId()) {
                    case '13': /* Status Pre clearance – compliant */
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/PreClearance/_msgPACompliant.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Notification to PCO';
                        $to = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject,$to, $templatename, $params);
                        break;
                    case '12': /* Status Pre clearance – not compliant */
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/PreClearance/_msgPANotCompliant.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Notification to PCO';
                        $to = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to, $templatename, $params);
                        break;
                    case '3':
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgNotYetAssessed.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Notification of assessment';
                        $to_app = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject, $to_app, $templatename, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename, $params);
                        break;
                    case '4': /* Status Compliant*/
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgCompliantApplicant.html.twig';
                        $templatename_pco = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgCompliantPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Assessment final decision';
                        $to_app = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        
                        //subject repeats
                        $to_pco = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_pco, $templatename_pco, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_pco, $templatename_pco, $params);
                        break;
                    case '18': /* Status Partial submission – compliant */
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSCompliantApplicant.html.twig';
                        $templatename_pco = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSCompliantPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Compliance notice to applicant(Partial Submission)';
                        $to_app = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        $subject = 'Ethical Med Tech - Conference Vetting System - Compliance notice to PCO(Partial Submission)';
                        $to_pco = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_pco, $templatename_pco, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_pco, $templatename_pco, $params);
                        break;
                    case '19': /* Status Partial submission – not compliant */
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSCorrectionApplicant.html.twig';
                        $templatename_pco = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSCorrectionPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Conference information is complete and not compliant(Partial Submission)';
                        $to_app = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        $to_pco = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_pco, $templatename_pco, $params);
                        /* write Time Notifications */
                        $this->TimeSend($conference);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_pco, $templatename_pco, $params);
                        break;
                    case '5': /* Status Not compliant */
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgCorrectionApplicant.html.twig';
                        $templatename_pco = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgCorrectionPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System – Assessment provisional status';
                        $to_app = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        $subject = 'Ethical Med Tech - Conference Vetting System – Correction Notice';
                        $to_pco = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_pco, $templatename_pco, $params);
                        /* write Time Notifications */
                        $this->TimeSend($conference);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_pco, $templatename_pco, $params);
                        break;
                    case '6': /* Status Non compliant criteria */
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgCorrectionApplicant.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System – Assessment provisional status';
                        $to_app = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        break;
                    case '2':
                        /* send info to email user*/
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgReceipt.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Request for Assessment';
                        $to = $conference->getOrganizingEmails();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to, $templatename, $params);
                        break;
                    case '20': /*Status Under appeal*/
                        $messageTarget = 1;
                        $templatenameCO = 'OSSystemEMTBundle:Default:Notifications/AppealBoard/_msgAppealUser.html.twig';
                        $templatenameUser = 'OSSystemEMTBundle:Default:Notifications/AppealBoard/_msgAppealCompany.html.twig';
                        $subjectCO = 'Ethical Med Tech - Conference Vetting System – Appeal acknowledgement';
                        $subjectUser = 'Ethical Med Tech - Conference Vetting System – Appeal notification';
                        $toCO = $conference->getOrganizingEmails();
                        $toUser = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subjectCO,$toCO,$templatenameCO,$params);
                        $this->container->get('emt.notification')->sendNotificationEmail($subjectUser,$toUser,$templatenameUser,$params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subjectCO,$toCO,$templatenameCO,$params,$messageTarget);
                        $this->container->get('emt.savemail')->MailToMessage($subjectUser,$toUser,$templatenameUser,$params,$messageTarget);
                        break;
                    case '9': /*Status Double Entry*/
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/DoubleEntry/_msgDoubleEntry.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System – Duplicate submission';
                        $to = $conference->getUser()->getEmail();
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to,$templatename,$params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject,$to,$templatename,$params);
                        break;
                    case '7': /* Status Not Assessed and substatus */
                        $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgNotYetAssessed.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Notification of assessment';
                        $to = $conference->getUser()->getEmail();
                        
                        if ($status_old == 16) {
                            switch($request->get('sub_status')) {
                            case 'Insufficient information':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSRejection3.html.twig';
                                break;
                            case 'Less than 75 days':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSRejection1.html.twig';
                                break;
                            case 'Outside EU':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSRejection2.html.twig';
                                break;
                            default:
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSNotYetAssessed.html.twig';
                            }
                        } elseif ($status_old != 7 && $status_old != 16) {
                            switch($request->get('sub_status')){
                            case 'Insufficient information':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgRejection3.html.twig';
                                $subject = 'Ethical Med Tech - Conference Vetting System – Insufficient Information';
                                break;
                            case 'Outside of the Scope':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgRejection2.html.twig';
                                $subject = 'Ethical Med Tech - Conference Vetting System – Submission out of Scope';
                                break;
                            case 'Less than 75 days':
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgRejection1.html.twig';
                                $subject = 'Ethical Med Tech - Conference Vetting System – Submission rejection';
                                break;
                            default:
                                $templatename = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/'.$messageLocation.'_msgNotYetAssessed.html.twig';
                            }
                        }
                        
                        $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                        /* Save mail to Message board */
                        $this->container->get('emt.savemail')->MailToMessage($subject, $to, $templatename, $params);
                        break;
                        
            }

            return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
            
        }
        
        return $this->render('OSSystemEMTBundle:Default\ConferenceEdit:conferenceStatusChange.html.twig', array(
            'conference' => $conference,
            'form' => $form->createView(),
        ));
    }


    public function conferencePublishAction($conferenceId, $doPublish = true){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        if (!(
                ($user->getProfileType() == User::PROFILE_TYPE_EUCOMED) ||
                ($user->getProfileType() == User::PROFILE_TYPE_MIDDLE_EAST)
              )
            )
        {
            return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
        }
        
        /*Get conference*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference',$conferenceId);
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $conference->setIsPublished($doPublish);
        $em->flush();
        
        return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
    }
    
    public function conferenceArchiveAction($conferenceId, $doArchive = true){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        if (!(
                ($user->getProfileType() == User::PROFILE_TYPE_EUCOMED) ||
                ($user->getProfileType() == User::PROFILE_TYPE_MIDDLE_EAST))
            )
        {
            return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
        }
        
        /*Get conference*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference',$conferenceId);
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $conference->setArchive($doArchive);
        $em->flush();
        
        return $this->redirect($this->generateUrl('os_system_emt_conference_view', array(
                  'conferenceId' => $conferenceId
              )));
    }
    
    public function conferencePreDoubleEntryAction($conferenceId) {
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference',$conferenceId);
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $conferenceLocation = $user->getProfileType();
            switch($conferenceLocation){
                    case User::PROFILE_TYPE_EUCOMED:
                        $loc = 'EU';
                        break;
                    case User::PROFILE_TYPE_MIDDLE_EAST:
                        $loc = 'ME';
                        break;
                    default: 
                        $loc = '';
                };
                
        $allConferences = $conferenceRepository->getAllConferencesForDoubleEntry($loc, false)->getArrayResult();
        $allConf = array();
        foreach ($allConferences as $conf) {
            if (!is_null($conf['title'])) {
                $allConf[$conf['id']] = $conf['title'];
            } else { continue; }
        }
        
        $form = $this->createFormBuilder()
            ->add('DoubleEntry', 'choice', array(
                'choices' => $allConf,
                'label' => 'Select duplicate conference',
                'required' => false,
                'multiple' => false
                )
            )
            ->getForm();
        
            if ('POST' === $request->getMethod()) {
                return $this->redirect($this->generateUrl('os_system_emt_conference_doubleentry', array(
                  'conferenceId' => $conferenceId
              )));
        }
        
        return $this->render('OSSystemEMTBundle:Default:conferencePreDoubleEntry.html.twig', array(
            'conference' => $conference,
            'form' => $form->createView()
        ));
    }
    
    public function conferenceDoubleEntryAction($conferenceId){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        if (!(
                ($user->getProfileType() == User::PROFILE_TYPE_EUCOMED) ||
                ($user->getProfileType() == User::PROFILE_TYPE_MIDDLE_EAST)
              )
            )
        {
            return $this->redirect($this->generateUrl('os_system_emt_conference_dblentries'));
        }
        
        /*Get conference*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference',$conferenceId);
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $conference->setConferenceStatus($em->find("OSSystemEMTBundle:Status",9));
        $em->flush();
        
        return $this->redirect($this->generateUrl('os_system_emt_conference_dblentries'));
    }
    
    /*
     * function to validate required fields in Conference entity depending of user type (PCO/Company - preclearence/full)
     * user is not always an author - here can be a CO
     */
    public function validateMandatoryFieldsOfConference($conference, $user){
        $errors = array();
        $profileType = $user->getProfileType();
        
        if ($conference->getIsPreClearance()){
            if ($profileType != User::PROFILE_TYPE_PCO){
                $errors[] = array("step" => null, "message" => "Sorry, but you are not able to submit a Pre-clearance, only Full submission");
            }
            
        }else{ //full submission
            
            
        }
        if (! $conference->getStartConferenceDate()){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Start conference date\" field ");
        }
        
        if (! $conference->getEndConferenceDate()){
            $errors[] = array("step" => 1, "message" => "Please, fill \"End conference date\" field ");
        }
        
        $dateNow = new \DateTime();
        if ($conference->getStartConferenceDate() < $dateNow){
            $errors[] = array("step" => 1, "message" => "Conference start date has passed");
        }else{
            $country = $conference->getAssesmentCountry()->getLocation();
            $d75 =  strtotime("-75 days", $conference->getStartConferenceDate()->getTimestamp());
            if ("ME" == $country) {
                $d75 =  strtotime("-0 days", $conference->getStartConferenceDate()->getTimestamp());
            }
            $d180 =  strtotime("-180 days", $conference->getStartConferenceDate()->getTimestamp());
            $d6y = strtotime("+6 year");
                   
            if ($conference->getIsPreClearance()){
                if ($d180 < time()){
                    $errors[] = array("step" => 1, "message" => "Pre-clearance conference start date have to be up to 180 days");
                }
                if ($d6y < $conference->getStartConferenceDate()->getTimestamp()){
                    $errors[] = array("step" => 1, "message" => "Pre-clearance conference start date have to be before 6 years");
                }
            }else{
                if ($d75 < time()){
                    $errors[] = array("step" => 1, "message" => "Conference start date have to be up to 75 days");
                }
            }
            
        }
        
        if ($conference->getEndConferenceDate() < $conference->getStartConferenceDate()){
            $errors[] = array("step" => 1, "message" => "Start conference date can not be newer than end conference date");
        }
        

        if ($conference->getTitle() == ''){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Name of the conference\" field ");
        }
        
        if ($conference->getOrganizingCompaniesNames() == ''){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Name of Conference Organizing Party(ies)\" field ");
        }
        
        if ($conference->getContactPersonsNames() == ''){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Name(s) of contact person(s)\" field ");
        }
        
        if ($conference->getOrganizingEmails() == ''){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Organiser’s e-mail address\" field ");
        }
        
        if (!$conference->getAssesmentCountry()){
            $errors[] = array("step" => 1, "message" => "Please, fill \"Country\" field ");
        }
        
        
        return $errors;
    }

    /*Messages*/
    public function messagesPageConferenceAction($conferenceId, Request $request){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conference, user, message*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $messageRepository = $em->getRepository('OSSystemEMTBundle:Message');
        $userRepository = $em->getRepository('OSSystemEMTBundle:User');

        $message= new \OSSystem\EMTBundle\Entity\Message();
        $formAddMessage = $this->createForm(new MessageType(), $message);
        
        $conferenceLocation = $conference->getAssesmentCountry()->getLocation();

        if(($user->getProfileType() != User::PROFILE_TYPE_EUCOMED)&&($user->getProfileType() != User::PROFILE_TYPE_MIDDLE_EAST)){      
                switch($conferenceLocation){
                    case 'EU':
                    default:
                        $recipient = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_EUCOMED));
                        
                        break;
                    case 'ME':
                        $recipient = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_MIDDLE_EAST));    
                };
                $templatename = 'OSSystemEMTBundle:Default:Notifications/MessageBoard/_msgNewMessageCO.html.twig';
        }else {
            $recipient = $userRepository->findOneBy(array('id'=>$conference->getUser()->getId()));    
            $templatename = 'OSSystemEMTBundle:Default:Notifications/MessageBoard/_msgNewMessagePCO.html.twig';
        };
             

        if ('POST' === $request->getMethod()) {
            $formAddMessage->handleRequest($request);
            
            if ($formAddMessage ->isValid() && $formAddMessage->get('content')->getData() ) { 
                $i = 0;
                $comments = $request->get("additional_files_comment");
                
                foreach($request->files->get("additional_files") as $uploadedFile) {
                    
                    if (null != $uploadedFile){
                        $comment = $comments[$i++];
                        $document = new Document();
                        
                        $document->setConference($conference);
                        $document->setTarget( Document::DOCUMENT_TARGET_MESSAGES );
                        $document->setFile($uploadedFile, $conference->getId());
                        $document->setComment($comment);
                        $message->addDocument($document);
                        
                        $em->persist($document);
                    }
                }
                
                $message->setSender($user);
                $message->setRecipient($recipient);
                $message->setConference($conference);
                $message->setMailto($recipient->getEmail());
                $message->setTarget(Message::MESSAGE_TARGET_MESSAGE_BOARD);
                
                $em->persist($message);
                $em->flush();
                
                $to = $recipient->getEmail();
                $from = $user->getEmail();
                $subject = "EMT".$conferenceId." – EthicalMedTech: A new comment has been posted.";
                $params = $conference;
                 
                $this->container->get('emt.notification')->sendNotificationEmail($subject,$to,$templatename,$params,$from);
                
              return $this->redirect($this->generateUrl('os_system_emt_conference_messages', array(
                  'conferenceId' => $conferenceId
              )));
            };
            
            if (!$formAddMessage->get('content')->getData()){
                $formAddMessage->addError(new FormError('Message cannot be empty'));
            }
        }
        
        $listMessages = $messageRepository->findBy(array('conference'=>$conference, 'target'=> array(Message::MESSAGE_TARGET_MESSAGE_BOARD,Message::MESSAGE_TARGET_MAIL_BOARD)));
        
        return $this->render('OSSystemEMTBundle:Default:messages.html.twig', array(
                'conference' => $conference,
                'formAddMessage'=> $formAddMessage->createView(),
                'messages' => $listMessages,
                'appeal' => 0
            ));
    }
    
    /*Appeal Board*/
    public function appealPageConferenceAction($conferenceId, Request $request){
        $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        /*Get conference, user, message*/
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();
        
        $messageRepository = $em->getRepository('OSSystemEMTBundle:Message');
        $userRepository = $em->getRepository('OSSystemEMTBundle:User');

        $message= new \OSSystem\EMTBundle\Entity\Message();
        $formAddMessage = $this->createForm(new MessageType(), $message);
        
        $conferenceLocation = $conference->getAssesmentCountry()->getLocation();

        if(($user->getProfileType() != User::PROFILE_TYPE_EUCOMED)&&($user->getProfileType() != User::PROFILE_TYPE_MIDDLE_EAST)){      
            switch($conferenceLocation){
                case 'EU':
                default:
                    $recipient = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_EUCOMED));
                    break;
                case 'ME':
                    $recipient = $userRepository->findOneBy(array('profileType'=> User::PROFILE_TYPE_MIDDLE_EAST));    
            };
            $templatename = 'OSSystemEMTBundle:Default:Notifications/AppealBoard/_msgAppealCO.html.twig';
            $subject = 'Ethical Med Tech - Conference Vetting System – Appeal notification';
        }else {
            $recipient = $userRepository->findOneBy(array('id'=>$conference->getUser()->getId())); 
            $templatename = 'OSSystemEMTBundle:Default:Notifications/AppealBoard/_msgAppealUser.html.twig';
            $subject = 'Ethical Med Tech - Conference Vetting System – Appeal acknowledgement';
        };
             

        if ('POST' === $request->getMethod()) {
            $formAddMessage->handleRequest($request);
            
            if ($formAddMessage ->isValid() && $formAddMessage->get('content')->getData()) { 
                $i = 0;
                $comments = $request->get("additional_files_comment");
                
                foreach($request->files->get("additional_files") as $uploadedFile) {
                    
                    if (null != $uploadedFile){
                        $comment = $comments[$i++];
                        $document = new Document();
                        
                        $document->setConference($conference);
                        $document->setTarget( Document::DOCUMENT_TARGET_APPEAL );
                        $document->setFile($uploadedFile, $conference->getId());
                        $document->setComment($comment);
                        $message->addDocument($document);
                        
                        $em->persist($document);
                    }
                }
                
                $message->setSender($user);
                $message->setRecipient($recipient);
                $message->setConference($conference);
                $message->setMailto($recipient->getEmail());
                $message->setTarget(Message::MESSAGE_TARGET_APPEAL_BOARD);
                $conference->setConferenceStatus($em->getRepository('OSSystemEMTBundle:Status')->find(20));
                
                $em->persist($message);
                $em->flush();
                
                //$to = $conference->getUser()->getEmail();
                
                
                $to = $recipient->getEmail();
                $from = $user->getEmail();
                $params = $conference;

                $this->container->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params, $from);
                
              return $this->redirect($this->generateUrl('os_system_emt_conference_appeal', array(
                  'conferenceId' => $conferenceId
              )));
            }
            
            if (!$formAddMessage->get('content')->getData()){
                $formAddMessage->addError(new FormError('Message cannot be empty'));
            }
        };
        
        
        
        $listMessages = $messageRepository->findBy(array('conference'=>$conference, 'target'=> Message::MESSAGE_TARGET_APPEAL_BOARD));
        
        return $this->render('OSSystemEMTBundle:Default:messages.html.twig', array(
                'conference' => $conference,
                'formAddMessage'=> $formAddMessage->createView(),
                'messages' => $listMessages,
                'appeal' => 1
            ));
    }
    
    /*Static Page*/
    public function pageShowAction($url)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('OSSystemEMTBundle:StaticPage')->findContent($url);
        if($content == false){
              return $this->redirect($this->generateUrl('os_system_emt_home'));
        }else{
              return $this->render('OSSystemEMTBundle:Default:page_show.html.twig', 
                array('content' => $content ));
        }        
    }
     
    public function TimeSend($conference)
    {    
        $em = $this->getDoctrine()->getManager();
        $dateNow = new \DateTime('00:00:00');
        $timeRepository = $em->getRepository('OSSystemEMTBundle:TimeNotifications');
        $deadline = $timeRepository->findOneBy(array('conference'=>$conference));
        
        if($deadline){
            $time_send = $deadline;
            $time_send->setDeadlineDate($dateNow->modify('+10 day'));
            
        }else{
            $time_send = new TimeNotifications();
            $time_send->setConference($conference);
            $time_send->setDeadlineDate($dateNow->modify('+10 day'));
            $em->persist($time_send);
        }
        
        $em->flush();
    }
    
    public function testRouteAction() {
    }
}
