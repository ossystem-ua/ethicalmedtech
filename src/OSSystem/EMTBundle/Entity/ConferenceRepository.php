<?php

namespace OSSystem\EMTBundle\Entity;

use Doctrine\ORM\EntityRepository;
use OSSystem\EMTBundle\Entity\Conference;
use Doctrine\ORM\Query;

class ConferenceRepository extends EntityRepository
{
    public function getAllConferencesByUser($user, $desc = true){
        $em = $this->getEntityManager();
    
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->select("conf.id, "
                   ."conf.title, "
                   ."conf.startConferenceDate, "
                   ."conf.endConferenceDate, "
                   ."conf.isNew, "
                   ."s.title  as status, "
                   ."s.id  as statusId, "
                   ."c.title as country"
                   );
        $QB->andWhere('conf.user = :user')->setParameter('user',$user);
        if ($desc){
            $QB->orderBy("conf.id", "DESC");
        }
        
        //OrderBy
        $query = $QB->getQuery();
         
        return $query;
    }
    
    public function getAllConferencesBy($loc, $arch){
        $em = $this->getEntityManager();
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->leftJoin("conf.user", 'fs');
        $QB->select("conf.id, "
                    ."conf.title, "
                    ."conf.acronym, "
                    ."conf.createdAt, "
                    ."conf.organizingEmails, "
                    ."conf.isPublished, "
                    ."conf.archive, "
                    ."conf.startConferenceDate, "
                    ."conf.endConferenceDate,"
                    ."conf.submissionDate, "
                    ."conf.assesmentCity,"
                    ."fs.alternativeEmail as altEmail,"
                    ."c.title as country,"
                    ."s.title as status, "
                    ."s.id as statusId "
                   );
        
        $QB->andWhere('conf.archive = :arch')->setParameter('arch',$arch); 
        if($loc !== 'EU'){
            $QB->andWhere('c.location = :loc')->setParameter('loc', 'ME');
        }else{
//             $QB->andWhere('c.location = :loc')->setParameter('loc',$loc);
         }
        //$QB->orderBy("s.title");
        $QB->orderBy("conf.id", "DESC");
        $QB->addOrderBy("conf.createdAt", "DESC");
        //OrderBy
        $query = $QB->getQuery();

        return $query;
    }
    
    public function getConferenceById($id){
        $em = $this->getEntityManager();
    
        $QB = $this->createQueryBuilder('conf');
        $QB->select("conf");
        $QB->andWhere('conf.id = :id')->setParameter('id',$id);
         //OrderBy
        $query = $QB->getQuery();
         
        return $query;
    }
    
    
    
    public function getOpenConference($user, $isPreClearance){
        $em = $this->getEntityManager();
        $query =  $em->createQuery("SELECT conf "
                                . "FROM OSSystemEMTBundle:Conference conf "
                                . "WHERE conf.isNew = true "
                                . "AND conf.user = :user "
                                .($isPreClearance ? " AND conf.isPreClearance = true ": " AND conf.isPreClearance = false ") 
                                . "ORDER BY conf.createdAt DESC "
                        );
        $query->setParameter('user', $user);
        $query->setMaxResults(1);
        $res = $query->getResult();
        if (count($res))
            return $res[0];
        else
            return false;
    }
    
    public function initializeCriterias(&$conference) {
        $em = $this->getEntityManager();
        foreach ($conference->getCriterias() as $criteria){
            $conference->removeCriteria($criteria);
            $em->remove($criteria);
        }
        
        $criterias = $em->getRepository('OSSystemEMTBundle:Criteria')->findAll();
        
        foreach ($criterias as $criteria){
            $newStatus = new ConferenceCriteriaState();
            $newStatus->setCriteria($criteria);
            $newStatus->setStatus( Criteria::CRITERIA_STATE_TOBEREVIEWED );
            $newStatus->setConference($conference);
            $conference->addCriteria($newStatus);
        }        
        
    }
    
    /**/
    /*
    gets an empy conference by user and id
    
     *      */
    public function getOpenConferenceById($user, $conferenceId){
        if ($user->getProfileType() === "3" || $user->getProfileType() === "4") {
            $conference = $this->getConferenceById($conferenceId)->getOneOrNullResult();
            $user = $conference->getUser();
        }

        $em = $this->getEntityManager();
        $query =  $em->createQuery("SELECT conf "
                                . "FROM OSSystemEMTBundle:Conference conf "
                                . "WHERE conf.id = :conference_id "
                                . "AND conf.user = :user "
                                . "ORDER BY conf.createdAt DESC "
                        );
        $query->setParameter('user', $user);
        $query->setParameter('conference_id', $conferenceId);
        $query->setMaxResults(1);
        $res = $query->getResult();
        if (count($res))
            return $res[0];
        else
            return false;
    }
    
    
    public function createOpenConference($user){
        $em = $this->getEntityManager();
        $conference = new Conference();
        $conference->setUser($user);
        $conference->setIsNew(true);
        $em->persist($conference);
        $em->flush();
        return $conference;
    }
    
    public function filterConferences($loc, $arch, array $params = array()){
        $em = $this->getEntityManager();
    
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->leftjoin("conf.therapeuticArea", 'ta');
        $QB->leftJoin("conf.user", 'u');
        $QB->select("conf.id, "
                    ."conf.title, "
                    ."conf.acronym, "
                    ."conf.createdAt, "
                    ."conf.organizingCompaniesNames, "
                    ."conf.contactPersonsNames, "
                    ."conf.organizingEmails, "
                    ."conf.website, "
                    ."conf.email, "
                    ."conf.autopublishing, "
                    ."conf.delegatesMoreThan1Country, "
                    ."conf.delegatesAnticipate, "
                    ."conf.assesmentNameVenue, "
                    ."conf.assesmentCategoryVenue, "
                    ."conf.assesmentCity, "
                    ."conf.assesmentLocalNA, "
                    ."conf.assesmentProposedAccomodation, "
                    ."conf.eventProgramme, "
                    ."conf.comments, "
                    ."conf.isNew, "
                    ."u.id as uId, "
                    ."u.alternativeEmail as altEmail, "
                    ."ta.title as therapeuticArea, "
                    ."conf.therapeuticAreaOther, "
                    ."conf.isPreClearance, "
                    ."conf.submissionDate, "
                    ."conf.assessedOnDate, "
                    ."conf.conferenceStatusText, "
                    ."conf.isCORecommendation, "
                    ."conf.conferenceStatusDate, "
                    ."conf.isPublished, "
                    ."conf.archive, "
                    ."conf.startConferenceDate, "
                    ."conf.endConferenceDate,"
                    ."c.title as country,"
                    ."s.title as status, "
                    ."s.id as statusId "
                   );
    
        $QB->andWhere('conf.archive = :arch')->setParameter('arch',$arch);
        if($loc !== 'EU'){
            $QB->andWhere('c.location = :loc')->setParameter('loc', 'ME');
        }else{
//            $QB->andWhere('c.location = :loc')->setParameter('loc',$loc);
         }
                    /*And so on*/ 
        //$QB->andWhere('conf.published = false');
        
        if ($params) {
            foreach ($params as $name => $value) {
                switch ($name) {
                    case 'id':
                        $QB->andWhere('conf.id = :'.$name)->setParameter($name,$value);
                        break;
                    case 'name':
                        $QB->andWhere('conf.title LIKE :'.$name)->setParameter($name, '%'.$value.'%');
                        break;                    
                    case 'city':
                        $QB->andWhere('conf.assesmentCity = :'.$name)->setParameter($name,$value);
                        break;
                    case 'status':
                        $QB->andWhere('conf.conferenceStatus = :'.$name)->setParameter($name,$value);
                        break;
                    case 'area':
                        $QB->andWhere('conf.therapeuticArea = :'.$name)->setParameter($name,$value);
                        break;
                    /*End*/
                                       
                }
            }
        };
        $QB->orderBy("s.title");
        $QB->addOrderBy("conf.createdAt", "DESC");

//        $result = $QB->getQuery()->getSQL(); //
//        dump($result); exit;
        
        $result = $QB->getQuery()->getArrayResult();
        return $result;
    }
    
    /**Api**/
    public function searchConferences(array $params = array()){
        $em = $this->getEntityManager();
    
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->leftjoin("conf.therapeuticArea", 'tA');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->select("conf.id as id, "
                   ."conf.title as name, "
                   ."tA.title as medical_area, "
                   ."conf.createdAt as submission_date, "
                   ."conf.startConferenceDate as start_date, "
                   ."conf.startConferenceTime as start_time, "
                   ."conf.endConferenceDate as end_date, "
                   ."conf.endConferenceTime as end_time, "
                   ."conf.organizingCompaniesNames as organiser, "
                   ."conf.contactPersonsNames as contact_person, "
                   ."conf.organizingEmails as email, "
                   ."conf.website as website, "
                   ."conf.email as general_email, "
                   ."c.title as country, "
                   ."s.title as status "
                   );
                    /*And so on*/ 
        $QB->andWhere('conf.isPublished = 1');
        
        if ($params) {
            foreach ($params as $name => $value) {
                switch ($name) {
                    case 'name':
                        $QB->andWhere('conf.title LIKE :'.$name)->setParameter($name, '%'.$value.'%');
                        break;
                    case 'organiser':
                        $QB->andWhere('conf.organizingCompaniesNames LIKE :'.$name)->setParameter($name, '%'.$value.'%');
                        break;
                    case 'medical_area':
                        $QB->andWhere('conf.therapeuticArea = :'.$name)->setParameter($name,$value);
                        break;
                    case 'country':
                        $QB->andWhere('conf.assesmentCountry = :'.$name)->setParameter($name,$value);
                        break;
                    case 'date':
                       // die(var_dump($value->format('Y-m-d H:i:s')));
                        $QB->andWhere('conf.startConferenceDate <=:'.$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
                        $QB->andWhere('conf.endConferenceDate >=:'.$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
                        break;
                    case 'from_date':
                        $QB->andWhere('conf.startConferenceDate <=:'.$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
                        break;
                    case 'to_date':
                        $QB->andWhere('conf.endConferenceDate >=:'.$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
                        break;
                    /*May be in future theese fields will be deleted*/
//                    case 'begined':
//                        $QB->andWhere("conf.startConferenceDate >=:".$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
//                        break;
//                    case 'ended':
//                         $QB->andWhere("conf.endConferenceDate <=:".$name)->setParameter($name,$value->format('Y-m-d H:i:s'));
                        
                        break;
                    /*End*/
                    case 'limit':
                        echo
                        $QB->setMaxResults($value);
                        break;
                    
                }
            }
            if($params['begined'] && $params['ended']){
                $QB->andWhere("( ( conf.startConferenceDate BETWEEN :start1 and :end1 ) OR (conf.endConferenceDate BETWEEN :start1 and :end1) OR ((conf.startConferenceDate <= :start1) AND (conf.endConferenceDate >= :end1)) )")
                    ->setParameter('start1',$params['begined']->format('Y-m-d H:i:s'))
                    ->setParameter('end1',$params['ended']->format('Y-m-d H:i:s'));
            }
        }
       
        $result = $QB->getQuery()->getResult();
//        $result = $QB->getQuery()->getSQL(); //
//        dump($result, $params); exit;
         return $result;
    }
    
    public function searchConferenceById($conferenceId){
        $em = $this->getEntityManager();
        
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->leftjoin("conf.therapeuticArea", 'tA');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->select("conf.title as name, "
                   ."tA.title as medical_area, "
                   ."conf.therapeuticAreaOther as medical_area_other , "
                   ."conf.submissionDate as submission_date, "
                   ."conf.assessedOnDate as assessed_on, "
                   ."conf.startConferenceDate as start_date, "
                   ."conf.startConferenceTime as start_time, "
                   ."conf.endConferenceDate as end_date, "
                   ."conf.endConferenceTime as end_time, "
                   ."conf.organizingCompaniesNames as organiser, "
                   ."conf.contactPersonsNames as contact_person, "
                   ."conf.organizingEmails as email, "
                   ."conf.website as website, "
                   ."conf.email as general_email, "
                   ."conf.isCORecommendation as recommendation, "
                   ."conf.assesmentCity as venue_city, "
                   ."c.code as venue_country,"
                   ."conf.conferenceStatusText as status_comment, "
                   ."s.title as status"
                   );
                    /*And so on*/ 
        $QB->andWhere('conf.id = :id')->setParameter('id',$conferenceId);
        
        return  $QB->getQuery()->getSingleResult();
    }
    
    public function searchConferencesfindByMonth($date){
        $em = $this->getEntityManager();

        //die(var_dump($date));
        $start_date = $this->retrieveFirstDay($date);
        $end_date = $this->retrieveLastDay($date);

        $QB = $this->createQueryBuilder('conf');
        $QB->select("conf.id as id, "
                   ."conf.title as name, "
                   ."conf.conferenceStatus as status"
                   ."conf.createdAt as submission_date, "
                   ."conf.startConferenceDate as start_date, "
                   ."conf.startConferenceTime as start_time, "
                   ."conf.endConferenceDate as end_date, "
                   ."conf.endConferenceTime as end_time "
                   );
        

        $params = array();
        $params = array('begined' => new \DateTime(date('Y-m-d', $start_date)),'ended' => new \DateTime(date('Y-m-d', $end_date)));
//        die(var_dump($params));
        $conferences = $this->searchConferences($params);
//        dump($params);
//foreach ($conferences as $key=>$conf) {
//}die;
        $conferences_by_date = $this->conferencesByStartDate($conferences);

        $calendar = array();

        $ts = $start_date;

        while ($ts <= $end_date) {
            if (isset($conferences_by_date[date('Y', $ts)][date('m', $ts)][date('j', $ts)])) {
                $calendar[date('W', $ts)][date('j', $ts)] = array(
                    'day' => $ts,
                    'conferences' => $conferences_by_date[date('Y', $ts)][date('m', $ts)][date('j', $ts)]
                );
            } else {
                $calendar[date('W', $ts)][date('j', $ts)] = array(
                    'day' => $ts,
                    'conferences' => array()
                );
            }

            $ts = strtotime('+1 day', $ts);
        }

        return $calendar;
    }
    
    private function retrieveFirstDay($date, $calendar = true, $first_day = 1)
    {
        $ts = strtotime($date/*->format('Y-m-d')*/);

        $start_date = mktime(0, 0, 0, date('n', $ts), 1, date('Y', $ts));

        $day = date('N', $start_date);
        if ($calendar && ($day != $first_day)) {
            if ($day < $first_day) {
                $start_date = strtotime(($first_day - $day - 7) . ' days', $start_date);
            } else {
                $start_date = strtotime(($first_day - $day) . ' days', $start_date);
            }
        }

        return $start_date;
    }

    private function retrieveLastDay($date, $calendar = true, $first_day = 1)
    {
        $ts = strtotime($date/*->format('Y-m-d')*/);
        $end_date = mktime(0, 0, 0, date('n', $ts), date('t', $ts), date('Y', $ts));

        $last_day = $first_day - 1;
        if ($last_day == 0) $last_day = 7;
        $day = date('N', $end_date);

        if ($calendar && ($day != $last_day)) {
            if ($day < $last_day) {
                $end_date = strtotime(($last_day - $day) . ' days', $end_date);
            } else {
                $end_date = strtotime(($last_day - $day + 7) . ' days', $end_date);
            }
        }

        return $end_date;
    }
    
    public function conferencesByStartDate($conferences)
    {
        $calendar = array();
        foreach ($conferences as $conference) {
           // die('dddd'.var_dump($conference['start_date']->format('Y-m-d')));
            $start = strtotime($conference['start_date']->format('Y-m-d'));
            $end = strtotime($conference['end_date']->format('Y-m-d'));
            
            $array =  json_decode(json_encode($conference), true);;
            
            $ts = $start; 

            while ($ts <= $end) {
               
                $calendar[date('Y', $ts)][date('m', $ts)][date('j', $ts)][$this->find($conference['id'])->getFrontendStatus()][] = $array;
                $ts = strtotime('+1 day', $ts);
            }
        }
        return $calendar;
    }
    
    /*End Api */
    public function getTherapeuticArea(){
        $em = $this->getEntityManager();
        $query =  $em->createQuery("SELECT t "
                                . "FROM OSSystemEMTBundle:TherapeuticArea t "
                                . "ORDER BY t.id ASC "
                        );
        $res = $query->getResult();
        
        return $res;
    }
    
    public function getAllStatus(){
        $em = $this->getEntityManager();
        $query =  $em->createQuery("SELECT s "
                                . "FROM OSSystemEMTBundle:Status s "
                                . "ORDER BY s.id ASC "
                        );
        $res = $query->getResult();
        
        return $res;
    }
    
    public function doLockFields($conference){
        $em = $this->getEntityManager();
        if (($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWED)) ||
            ($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWEDFORPARTIALSUBMISSION)) ||
            ($conference->getConferenceStatus() == $em->find("OSSystemEMTBundle:Status", Conference::CONFERENCE_STATUS_TOBEREVIEWEDFORPRECLEARANCE))
            )
        {
            return true;
        }else{
            return false;
        }
    }
    
    public function generateSnapshot($conference){
        return array(
            'title' => $conference->getTitle(),
            'acronym' => $conference->getAcronym(), 
            'startConferenceDate' => $conference->getStartConferenceDate(), 
            'endConferenceDate' => $conference->getEndConferenceDate(), 
            'organizingCompaniesNames' => $conference->getOrganizingCompaniesNames(), 
            'contactPersonsNames' => $conference->getContactPersonsNames(),
            'organizingEmails' => $conference->getOrganizingEmails(), 
            'website' => $conference->getWebsite(), 
            'email' => $conference->getEmail(), 
            'autopublishing' => $conference->getAutopublishing(), 
            'delegatesMoreThan1Country' => $conference->getDelegatesMoreThan1Country(),
            'delegatesAnticipate' => $conference->getDelegatesAnticipate(),
            'assesmentNameVenue' => $conference->getAssesmentNameVenue(),
            'assesmentCategoryVenue' => $conference->getAssesmentCategoryVenue(),
            'assesmentCity' => $conference->getAssesmentCity(),
            'assesmentLocalNA' => $conference->getAssesmentLocalNA(),
            'assesmentProposedAccomodation' => $conference->getAssesmentProposedAccomodation(),
            'eventProgramme' => $conference->getEventProgramme(),
            'comments' => $conference->getComments(),
            'assesmentCountry' => ($conference->getAssesmentCountry() ? $conference->getAssesmentCountry()->getId() : null),
            'is_new' => $conference->getisNew(),
            'startConferenceTime' => $conference->getStartConferenceTime(),
            'endConferenceTime' => $conference->getEndConferenceTime(),
            'therapeuticArea' => ($conference->getTherapeuticArea() ? $conference->getTherapeuticArea()->getId() : null ),
            'therapeuticAreaOther' => $conference->getTherapeuticAreaOther(),
            'conferenceStatus' => ($conference->getConferenceStatus() ? $conference->getConferenceStatus()->getId() : null ),
            'isPreClearance' => $conference->getIsPreClearance(),
            'archive' => $conference->getArchive(),
            'submission_date' => $conference->getSubmissionDate(),
            'assessed_on_date' => $conference->getAssessedOnDate(),
            'comments_text' => $conference->getConferenceStatusText(),
            'is_co_recommendation' => $conference->getIsCORecommendation(),
            'conference_status_date' => $conference->getConferenceStatusDate(),
            'isPublished' => $conference->getIsPublished(),
        );
    }
    
    public function traceUpdatedFields($conference){
        $snapshot = $this->generateSnapshot($conference);
        
        if (! count($conference->getSnapshot())){
            $conference->setSnapshot($snapshot);
        }
        
        $changedFields = array();
        $oldSnapshot = $conference->getSnapshot();
        if ($oldSnapshot){
            $oldSnapshotKeys = array_keys($oldSnapshot);
            
            foreach ($snapshot as $key => $value){
                if (!in_array($key, $oldSnapshotKeys)){
                    $changedFields[] = $key;
                    continue;
                }

                if ($oldSnapshot[$key] != $value){
                    $changedFields[] = $key;
                }
            }
        }else{
            $oldSnapshotKeys = array();
        }
        
        $conference->setChangedFields($changedFields);
        
        return $changedFields;
        
    }
    
    public function getAllConferencesForDoubleEntry($loc, $arch){
        $em = $this->getEntityManager();
        $QB = $this->createQueryBuilder('conf');
        $QB->leftjoin("conf.assesmentCountry", 'c');
        $QB->leftjoin("conf.conferenceStatus", 's');
        $QB->select("conf.id, "
                    ."conf.title"
                   );
        
        $QB->andWhere('conf.archive = :arch')->setParameter('arch',$arch);
        if($loc !== 'EU'){
            $QB->andWhere('c.location = :loc')->setParameter('loc', 'ME');
        }else{
//            $QB->andWhere('c.location = :loc')->setParameter('loc',$loc);
         }
        //$QB->orderBy("s.title");
        $QB->orderBy("conf.id", "DESC");
        $QB->addOrderBy("conf.createdAt", "DESC");
        //OrderBy
        $query = $QB->getQuery();
         
        return $query;
    }
    
}
