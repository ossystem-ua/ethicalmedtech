<?php

namespace OSSystem\EMTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ApiController extends Controller
{
    /*API*/
    /*Search*/

    /**
     * return the Response object associated to the create action
     *
     * @return Response
     */
    public function apiConrerencesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        $params = array();
        
        if($name = $request->get('name', false)) $params['name'] = (string)$name;
        if($organiser = $request->get('organiser', false)) $params['organiser'] = (string)$organiser;
        if($medical_area = $request->get('medical_area', false)) $params['medical_area'] = (string)$medical_area;
        if($country = $request->get('country', false)) $params['country'] = (string)$country;
        if($status = $request->get('status', false)) $params['status'] = (string)$status;
        if($date = $request->get('date', false)) $params['date'] = (string)$date;
        if($from_date = $request->get('from_date', false)) $params['from_date'] = (string)$from_date;
        if($to_date = $request->get('to_date', false)) $params['to_date'] = (string)$to_date;
        if($limit = $request->get('limit', false)) $params['limit'] = (int)$limit;

        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conferences = $conferenceRepository->searchConferences();
        
        foreach ($conferences as $key => $conference){
            if($conference['start_date']){
              
                $conference['start_date']=$conference['start_date']->format('Y-m-d');
                $conferences[$key] = $conference;
            };
            if($conference['end_date']){
                $conference['end_date']=$conference['end_date']->format('Y-m-d');
                $conferences[$key] = $conference;
            }; 
            if($conference['status']){
                $conference['status']= $this->getFrontendStatus($conference['status']);
                $conferences[$key] = $conference;
            }
        }

        $response = new JsonResponse();
        
        $response->setData($conferences);
        return $response;
    }
    
    /**
     * return the Response object associated to the create action
     *
     * @return Response
     */    
    public function apiConrerenceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        
        if($conferenceId = $request->get('id', false)) $id = (string)$conferenceId;
        $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conference = $conferenceRepository->searchConferenceById($id);
        $c = $conferenceRepository->find($id);
        
        $conference['start_date']      = $conference['start_date']      ? $conference['start_date']->format('Y-m-d')      : false;
        $conference['end_date']        = $conference['end_date']        ? $conference['end_date']->format('Y-m-d')        : false ;
        $conference['submission_date'] = $conference['submission_date'] ? $conference['submission_date']->format('Y-m-d') : false ;
        $conference['assessed_on']     = $conference['assessed_on']     ? $conference['assessed_on']->format('Y-m-d')     : false;
        $conference['status']          = $conference['status']          ? $conference['status'] : $this->getFrontendStatus($c->getConferenceStatus()->getTitle());
        $conference['status_comment']  = $conference['status_comment']  ? $conference['status_comment']                   : $c->getConferenceStatusText();
        $criterias = $em->getRepository('OSSystemEMTBundle:ConferenceCriteriaState')->findBy(array('conference' => $conferenceId));
        
        $conferenceCriteaState = array();
        foreach($criterias as $criteria){
            switch($criteria->getStatus()){
                case 0:
                    $isCompliant = 2;
                    break;
                case 1:
                default:    
                    $isCompliant = null;
                    break;
                case 2:
                    $isCompliant = 1;
                    break;
                case 3:
                    $isCompliant = 0;
                    break;
            }
            $conferenceCriteaState[] = array('id'=>$criteria->getCriteria()->getId(), 'title'=>$criteria->getCriteria()->getTitle(), 'is_compliant'=>$isCompliant  );
        }

        $conference['criteria'] = $conferenceCriteaState;
        $response = new JsonResponse();

        $response->setData($conference);
        
        return $response;
    }
    
    /*Calendar*/
    /**
     * return the Response object associated to the create action
     *
     * @return Response
     */
    public function apiCalendarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set('Europe/Berlin');
    }
		
        if(!$date = $request->get('date', false)){ $date = date('Y-m-d'); } //new \DateTime('now');
	$conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
        $conferences = $conferenceRepository->searchConferencesfindByMonth($date);
		
	$response = new JsonResponse();

        $response->setData($conferences);
        return $response;
    }
    
    
    public function getFrontendStatus($status){
        
        switch ($status)
        {
            case 'Pre-Clearance approved':
            case 'Pre clearance - compliant':
            case 'Compliant':
                return 'Compliant';
            case 'Non compliant for partial submission':
            case 'Not compliant':
            case 'Non compliant criteria':
                return 'Not compliant';
            case 'Not assessed':
                return 'Not assessed';
            case 'Compliant for partial submission':
            case 'Partially compliant':
            case 'Partial submission – compliant':
                  return 'Provisional';

            case 'To be reviewed for Pre-Clearance':
            case 'Non compliant criteria for partial submission':
            case 'Partial submission – not compliant';
            case 'To be reviewed for partial submission':
            case 'To be reviewed':
                return 'To be reviewed';
                
            case 'Info complete':
            case 'Info completed for partial submission':
            case 'Pre-Clearance not approved':
            default:
                return 'Hidden';
        }
    }

}
