<?php

namespace OSSystem\EMTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Connection;

use OSSystem\EMTBundle\Entity\Conference;
use OSSystem\EMTBundle\Entity\Message;
use OSSystem\EMTBundle\Entity\User;
use OSSystem\EMTBundle\Entity\TimeNotifications;

class ExportController extends Controller
{
    public function exportAllConfAction() {
        
       $sc = $this->get('security.context');
	$user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $arch = false;
        
        
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
        if($id = $request->query->get('filter_id', false)){ $params['id'] = (int)$id; }
        if($medical_area = $request->query->get('filter_area', false)){ $params['area'] = (string)$medical_area;}
        if($city = $request->query->get('filter_city', false)){ $params['city'] = (string)$city;}
        if($status = $request->query->get('filter_status', false)){ $params['status'] = (string)$status;}
        if($name = $request->query->get('filter_name', false)){ $params['name'] = (string)$name;}
        
        $repo = $em->getRepository('OSSystemEMTBundle:Conference');
        $conferences = $conferenceRepository->filterConferences($loc, $arch, $params);
        
        foreach ($conferences as &$conference){
            if($conference['startConferenceDate']){
              
                $conference['startConferenceDate']=$conference['startConferenceDate']->format('Y-m-d');
            };
            if($conference['endConferenceDate']){
                $conference['endConferenceDate']=$conference['endConferenceDate']->format('Y-m-d');
            };
            if($conference['createdAt']){
                $conference['createdAt']=$conference['createdAt']->format('Y-m-d');
            };
            
            if($conference['submissionDate']){
                $conference['submissionDate']=$conference['submissionDate']->format('Y-m-d');
            };
            if($conference['assessedOnDate']){
                $conference['assessedOnDate']=$conference['assessedOnDate']->format('Y-m-d');
            };
            if($conference['conferenceStatusDate']){
                $conference['conferenceStatusDate']=$conference['conferenceStatusDate']->format('Y-m-d');
            };
            
            //Documents
            $conferenceEntity = $repo->getConferenceById($conference['id'])->getSingleResult();
            $accomodationFile = $conferenceEntity->getAssesmentProposedAccomodationDocument();
            if ($accomodationFile){
                $conference['AssesmentProposedAccomodationDocumentFile'] = 'http://'.$request->getHost().'/'.$accomodationFile->getWebPath();
            }
            $documents = $conferenceEntity->getDocuments();
            
            $conference['documents'] = '';
            foreach ($documents as $doc){
                $conference['documents'] = $conference['documents']. 'http://'.$request->getHost().'/'.$doc->getWebPath() . ' , ';
            }
            if (strlen($conference['documents'])){
                $conference['documents'] = substr($conference['documents'],0,-3);
            }
        }
        
        $conf = $conferences;
        
        $container = $this->container;
        $response = new StreamedResponse(function() use($container, $conf) {

            $em = $container->get('doctrine')->getManager();
            
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, array_keys($conf[0]),';');
            foreach($conf as $results){            
                fputcsv($handle, array_values($results),';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
    
    public function exportExelAction($conferenceId) {
        
        $container = $this->container;
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('OSSystemEMTBundle:Conference')->getConferenceById($conferenceId)->getSingleResult();
        $conf = array();
        $conf['id'] = $results->getId();
        $conf['title'] = $results->getTitle();
        $conf['acronym'] = $results->getAcronym();
        if($results->getStartConferenceDate()){
            $conf['startConferenceDate'] = $results->getStartConferenceDate()->format('Y-m-d');
        }
        if($results->getEndConferenceDate()){
            $conf['endConferenceDate'] = $results->getEndConferenceDate()->format('Y-m-d');
        }
        $conf['organizingCompaniesNames'] = $results->getOrganizingCompaniesNames();
        $conf['contactPersonsNames'] = $results->getContactPersonsNames();
        $conf['organizingEmails'] = $results->getOrganizingEmails();
        $conf['website'] = $results->getWebsite();
        $conf['autopublishing'] = $results->getAutopublishing();
        $conf['createdAt'] = $results->getCreatedAt();
        $conf['updateAt'] = $results->getUpdateAt();
        $conf['delegatesMoreThan1Country'] = $results->getDelegatesMoreThan1Country();
        $conf['delegatesAnticipate'] = $results->getDelegatesAnticipate();
        $conf['assesmentNameVenue'] = $results->getAssesmentNameVenue();
        $conf['assesmentCategoryVenue'] = $results->getAssesmentCategoryVenue();
        $conf['assesmentCity'] = $results->getAssesmentCity();
        $conf['assesmentLocalNA'] = $results->getAssesmentLocalNA();
        $conf['assesmentProposedAccomodation'] = $results->getAssesmentProposedAccomodation();
        $conf['eventProgramme'] = $results->getEventProgramme();
        if($results->getAssesmentCountry()){
            $conf['assesmentCountry_id'] = $results->getAssesmentCountry()->getTitle();
        }
        $conf['published'] = $results->getPublished();
        $conf['startConferenceTime'] = $results->getStartConferenceTime();        
        $conf['endConferenceTime'] = $results->getEndConferenceTime();
        $conf['user_id'] = $results->getUser()->getLName();

        if($results->getConferenceStatus()){
            $conf['conferenceStatus_id'] = $results->getConferenceStatus()->getTitle();
        }
        $conf['submission_date'] = $results->getSubmissionDate();
        $conf['criterias_id'] = $results->getCriterias();
        
        $container = $this->container;
        $response = new StreamedResponse(function() use($container, $conf) {

            $em = $container->get('doctrine')->getManager();
            
            $results = $conf;
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, array_keys($results),';');
            fputcsv($handle, array_values($results),';');

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
    
    public function exportToExcelAction ($conferenceId) {
        
        $em = $this->getDoctrine()->getManager();
        $conf = array();
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $container = $this->container;
            $results = $em->getRepository('OSSystemEMTBundle:Conference')->getConferenceById($conferenceId)->getSingleResult();

            $conf = array();
            $conf['id'] = $results->getId();
            $conf['title'] = $results->getTitle();
            $conf['acronym'] = $results->getAcronym();
            $conf['createdAt'] = $results->getCreatedAt();
            $conf['organizingCompaniesNames'] = $results->getOrganizingCompaniesNames();
            $conf['contactPersonsNames'] = $results->getContactPersonsNames();
            $conf['organizingEmails'] = $results->getOrganizingEmails();
            $conf['website'] = $results->getWebsite();
            $conf['autopublishing'] = $results->getAutopublishing();
            $conf['delegatesMoreThan1Country'] = $results->getDelegatesMoreThan1Country();
            $conf['delegatesAnticipate'] = $results->getDelegatesAnticipate();
            $conf['assesmentNameVenue'] = $results->getAssesmentNameVenue();
            $conf['assesmentCategoryVenue'] = $results->getAssesmentCategoryVenue();
            $conf['assesmentCity'] = $results->getAssesmentCity();
            $conf['assesmentLocalNA'] = $results->getAssesmentLocalNA();
            $conf['assesmentProposedAccomodation'] = $results->getAssesmentProposedAccomodation();
            $conf['eventProgramme'] = $results->getEventProgramme();
            $conf['commentsText'] = $results->getComments();
            $conf['isNew'] = $results->getIsNew();
            if (!is_null($results->getTherapeuticArea())) {
                $conf['therapeuticArea'] = $results->getTherapeuticArea()->getTitle();
                $conf['therapeuticAreaOther'] = $results->getTherapeuticAreaOther();
            }
            $conf['isPreClearance'] = $results->getIsPreClearance();
            $conf['assessedOnDate'] = $results->getAssessedOnDate();
            $conf['conferenceStatusText'] = $results->getConferenceStatusText();
            $conf['isCORecommendation'] = $results->getIsCORecommendation();
            $conf['isPublished'] = $results->getIsPublished();
            $conf['archive'] = $results->getArchive();
            if($results->getStartConferenceDate()){
                $conf['startConferenceDate'] = $results->getStartConferenceDate()->format('Y-m-d');
            }
            if($results->getEndConferenceDate()){
                $conf['endConferenceDate'] = $results->getEndConferenceDate()->format('Y-m-d');
            }
            if($results->getAssesmentCountry()){
                $conf['assesmentCountry_id'] = $results->getAssesmentCountry()->getTitle();
            }
            if($results->getConferenceStatus()){
                $conf['conferenceStatus'] = $results->getConferenceStatus()->getTitle();
                $conf['conferenceStatus_id'] = $results->getConferenceStatus()->getId();
            }
            
            $repo = $em->getRepository('OSSystemEMTBundle:Conference');
            $conferenceEntity = $repo->getConferenceById($conferenceId)->getSingleResult();
            $accomodationFile = $conferenceEntity->getAssesmentProposedAccomodationDocument();
            if ($accomodationFile){
                $conference['AssesmentProposedAccomodationDocumentFile'] = 'http://'.$request->getHost().'/'.$accomodationFile->getWebPath();
            }
            $documents = $conferenceEntity->getDocuments();
            
            $conf['documents'] = '';
            foreach ($documents as $doc){
                $conf['documents'] = $conf['documents']. 'http://'.$request->getHost().'/'.$doc->getWebPath() . ' , ';
            }
            if (strlen($conf['documents'])){
                $conf['documents'] = substr($conf['documents'],0,-3);
            }
            
            if (!is_null($request->request->get("submit-selected"))) {
                foreach($conf as $name => $param) {
                    if ('on' === $request->request->get($name)) {
                        continue;
                    }
                    unset($conf[$name]);
                }
            }
            $container = $this->container;
            $response = new StreamedResponse(function() use($container, $conf) {

                $em = $container->get('doctrine')->getManager();

                $results = $conf;
                $handle = fopen('php://output', 'w+');
                fputcsv($handle, array_keys($results),';');
                fputcsv($handle, array_values($results),';');

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

            return $response;
        }
        
        return $this->render('OSSystemEMTBundle:Default:exportToExcel.html.twig', array('conferences' => $conf));
    }
    
    public function exportTBSAction () {
        
        $sc = $this->get('security.context');
        $user = $sc->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $arch = false;
        
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
        $cDeadline = $em->getRepository('OSSystemEMTBundle:TimeNotifications');
        $cMessages = $em->getRepository('OSSystemEMTBundle:Message');

        $request = $this->getRequest();
        
        $params = array();
        if($id = $request->query->get('filter_id', false)){ $params['id'] = (int)$id; }
        if($medical_area = $request->query->get('filter_area', false)){ $params['area'] = (string)$medical_area;}
        if($city = $request->query->get('filter_city', false)){ $params['city'] = (string)$city;}
        if($status = $request->query->get('filter_status', false)){ $params['status'] = (string)$status;}
        if($name = $request->query->get('filter_name', false)){ $params['name'] = (string)$name;}
        
        $repo = $em->getRepository('OSSystemEMTBundle:Conference');
        $conferences = $conferenceRepository->filterConferences($loc, $arch, $params);

        $count = 0;
        foreach ($conferences as $conference) {
            $submitter = $em->getRepository('OSSystemEMTBundle:User')->find($conference['uId']);
            $conf = $conferenceRepository->getConferenceById($conference['id'])->getSingleResult();
            if (is_null($conf->getTitle()) && is_null($conf->getAssesmentCountry()) && is_null($conf->getOrganizingEmails()) && is_null($conf->getStartConferenceDate())) {
                continue;
            }
            
            $messages = $cMessages->findBy(array('conference' => $conf, 'target' => array(Message::MESSAGE_TARGET_APPEAL_BOARD)));
            $appealBool = $appealFillingDate = '';
            if (!empty($messages)) {
                $appealBool = 'Yes';
                $message = $messages[0];
                $appealFillingDate = !is_null($message->getCreatedAt())?$message->getCreatedAt()->format('d-M-y'):'';
            }
            
            foreach ($conf->getCriterias() as $key => $criterias) {
                $criteria[$this->getCriteriaKey($key)] = $this->getCriteria($criterias->getStatus());
            }
            $correctionDeadline = $cDeadline->findOneBy(array('conference' => $conf));

            $st = $conference['statusId'];
            $data[] = array('submissionDate' => !is_null($conference['submissionDate'])?$conference['submissionDate']->format('d-M-y'):'',
                            'id' => !is_null($conference['id'])?$conference['id']:'',
                            'name' => !is_null($conference['title'])?$conference['title']:'',
                            'startDate' => !is_null($conference['startConferenceDate'])?$conference['startConferenceDate']->format('d-M-y'):'',
                            'endDate' => !is_null($conference['endConferenceDate'])?$conference['endConferenceDate']->format('d-M-y'):'',
                            'city' => $conference['assesmentCity']?$conference['assesmentCity']:'',
                            'country' => $conference['country']?$conference['country']:'',
                            'international' => $conference['delegatesMoreThan1Country']?'yes':'no',
                            'submitterName' => $submitter->getFName().' '.$submitter->getLName(),
                            'submitterEmail' => $submitter->getEmail(),
                            'submitterCategory' => $submitter->getCategory(),
                            'email' => $conference['organizingEmails'],
                            'ackowledgement' => !is_null($conference['submissionDate'])?$conference['submissionDate']->format('d-M-y'):'',
                            'geoLocation' => $criteria['geographicLocation'],
                            'confVenue' => $criteria['conferenceVenue'],
                            'accommodation' => $criteria['accommodation'],
                            'commSupport' => $criteria['communicationSupport'],
                            'spouseOrGuest' => $criteria['accompanyingPersonsSpouses'],
                            'scientificProgramme' => $criteria['scientificProgramme'],
                            'entartainment' => $criteria['scientificProgramme'],
                            'finalDecision' => is_null($conference['status'])?$st:$conference['status'],
                            'correctionDeadline' => is_null($correctionDeadline)?'':$correctionDeadline->getDeadlineDate()->format('d-M-y'),
                            'decisionPublicationDate' => !is_null($conference['conferenceStatusDate'])?$conference['conferenceStatusDate']->format('d-M-y'):'',
                            'statusChange' => !is_null($conference['conferenceStatusText'])?preg_replace("/&ndash;/"," - ", (preg_replace("/&nbsp;/"," ", (strip_tags($conference['conferenceStatusText']))))):'',
                            'publicationDate' => !is_null($conference['conferenceStatusDate'])?$conference['conferenceStatusDate']->format('d-M-y'):'',
                            'appealBool' => $appealBool,
                            'appealFillingDate' => $appealFillingDate,
                            'appealWhat' => ''
                );
        }
        unset($conferences);
        unset($correctionDeadline);
        unset($messages);
        unset($submitter);
        // get the service
        $TBS = $this->container->get('opentbs');
        // load your template
        $TBS->LoadTemplate('resources/TBSTemplate/template.xlsx');
        // replace variables
        $TBS->MergeBlock('conference', $data);
        // send the file
        $TBS->Show(OPENTBS_DOWNLOAD, 'export '.date('d-m-Y').'.xlsx');
    }
    
    public function getCriteria($status = 0) {
        switch($status){
            case 0:
                return "Not applicable"; break;
            case 1:
                return "To be reviewed"; break;
            case 2:
                return "Compliant"; break;
            case 3:
              return "Not compliant"; break;
            case 4:
                return "Missing"; break;
            case 5:
                return "Under correction"; break;
            default:
                return "To be reviewed";
        }
        return "To be reviewed";
    }
    public function getCriteriaKey($key = 1) {
        switch($key){
            case 1:
                return "scientificProgramme"; break;
            case 2:
                return "geographicLocation"; break;
            case 3:
                return "conferenceVenue"; break;
            case 4:
                return "hospitality"; break;
            case 5:
                return "accommodation"; break;
            case 6:
                return "accompanyingPersonsSpouses"; break;
            case 7:
                return "communicationSupport"; break;
            case 8:
                return "socialProgramme"; break;
            default:
                return "scientificProgramme";
        }
        return "scientificProgramme";
    }
    
    public function exportToPDFAction ($conferenceId, $appeal = 0) {
        
        $sc = $this->get('security.context');
        $user = $sc->getToken()->getUser();
                
        $em = $this->getDoctrine()->getManager();
        
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->getConferenceById($conferenceId)->getSingleResult();

        $conferenceLocation = $conference->getAssesmentCountry()->getLocation();
        switch($conferenceLocation){
            case 'ME':
                $loc = 'ME';
                break;
            default: 
                $loc = '';
        };
        
        
        
        $messageRepository = $em->getRepository('OSSystemEMTBundle:Message');
        if (1 == $appeal) {
            $listMessages = $messageRepository->findBy(array('conference'=>$conference, 'target'=> array(Message::MESSAGE_TARGET_APPEAL_BOARD)));
        } else {
            $listMessages = $messageRepository->findBy(array('conference'=>$conference, 'target'=> array(Message::MESSAGE_TARGET_MESSAGE_BOARD,Message::MESSAGE_TARGET_MAIL_BOARD)));
        }
        $content = $this->renderView('OSSystemEMTBundle:Default:templateExportToPDF'.$loc.'.html.twig', array('messages' => $listMessages));

        $pdfData = $this->get('obtao.pdf.generator')->outputPdf($content,array('font'=>'Arial','format'=>'P'));

        $response = new Response($pdfData);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
    
    public function toArray()
    {
        return array(
            $this->id,
            $this->title,
            $this->acronym,
        );
   }
 
}
