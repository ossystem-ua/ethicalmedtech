<?php

namespace OSSystem\EMTBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OSSystem\EMTBundle\Entity\User;
use OSSystem\EMTBundle\Entity\Conference;
use OSSystem\EMTBundle\Entity\ConferenceCriteriaState;
use OSSystem\EMTBundle\Entity\Status;
use OSSystem\EMTBundle\Entity\Message;
use OSSystem\EMTBundle\Entity\Country;

class SyncOldEMTCommand extends ContainerAwareCommand
{
    //const generealCondition = ' conference.id = 5';
    //const generealConditionLink = ' conference_id = 5';
    
    const generealCondition = ' 1=1';
    const generealConditionLink = ' 1=1';
    
    protected function configure()
    {
        $this
            ->setName('syncwith:EMT')
            ->setDescription('Synchronize with old EMT')
            ->setDefinition(array(
                            new InputArgument('action', InputArgument::REQUIRED, 'action')
            ))
        ;
    }
   
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

		switch ($action) {
			case 'users':
				$this->syncUsers();
				break;
                            
                        case 'conferences':
                            $this->syncConferences();
                            break;
                        
                        case 'criterias':
                            $this->syncConferencesCriteria();
                            break;
                        
                        case 'messages':
                            $this->syncMessages();
                            break;
                        
                        case 'comments':
                            $this->syncComments();
                            break;
                        
                        case 'documents':
                            $this->syncDocuments();
                            break;
                        
                        default: $output->writeln("Available actions: users, conferences, criterias, messages, documents");
		}
    }
    
    protected function syncUsers() {
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();


        $statement = $connection->prepare("SELECT "
                ."user.id as userId, "
                ."user.salt as userSalt, "
                ."user.password as userPassword, "
                ."user.username as userUsername, "
                ."user.email_address as userEmail, "
                ."user.first_name as userFname, "
                ."user.last_name as userLname, "
                ."user.is_active as userIsActive, "
                ."user.last_login as userLastLogin, "
                ."user.created_at as userCreatedAt, "
                ."user.updated_at as userUpdatedAt, "
                ."profile.user_id as profileUserId, "
                ."profile.name as profileName, "
                ."profile.user_type as profileType, "
                ."profile.address as profileAddress, "
                ."profile.address2 as profileAddress2, "
                ."profile.zip as profileZip, "
                ."profile.city as profileCity, "
                ."profile.country as profileCountry, "
                ."profile.title as profileTitle, "
                ."profile.job_title as profileJobTitle, "
                ."profile.telephone as profilePhone, "
                ."profile.opt_in as profileOptIn, "
                ."profile.first_name as profileFname, "
                ."profile.last_name as profileLname "

                ."FROM sf_guard_user as user LEFT JOIN sf_guard_user_profile as profile ON user.id=profile.user_id   order by user.id ");

        $statement->execute();
        $results = $statement->fetchAll();
                
        foreach($results as $oldEMTUser) {
            
            $user = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('email' => $oldEMTUser['userEmail']));
            if (!$user){
                $user = new User();
            }
            /*From User table*/
            $user->setPassword($oldEMTUser['userPassword']);
            $user->setSalt($oldEMTUser['userSalt']);
            $user->setOldId($oldEMTUser['userId']);
            $user->setUsername($oldEMTUser['userUsername']);
            $user->setUsernameCanonical(strtolower($oldEMTUser['userUsername']));
            $user->setEmail($oldEMTUser['userEmail']);
            $user->setEmailCanonical(strtolower($oldEMTUser['userEmail']));
            $user->setFName($oldEMTUser['userFname']);
            $user->setLName($oldEMTUser['userLname']);
            $user->setLocked(!$oldEMTUser['userIsActive']);
            $user->setEnabled($oldEMTUser['userIsActive']);  
            $user->setLastLogin(date_create($oldEMTUser['userLastLogin']));
            $user->setCreatedAt(date_create($oldEMTUser['userCreatedAt']));
            $user->setUpdatedAt(date_create($oldEMTUser['userUpdatedAt']));
            
            /*From profile table if exists*/
            if($oldEMTUser['profileUserId']){
                
                $user->setOrganization($oldEMTUser['profileName']);

                $userCategory = $em->getRepository('OSSystemEMTBundle:UserCategory')->findOneBy(array('category' => $oldEMTUser['profileType']));
                $userCategoryId = $userCategory->getId();

                switch($userCategoryId):
                        case 1:
                        case 3:
                        case 4:
                            $user->setProfileType(User::PROFILE_TYPE_PCO);
                            break;
                        case 2:
                            $user->setProfileType(User::PROFILE_TYPE_COMPANIES);
                            break;
                        default :
                            break;
                    endswitch;

                $user->setCategory($userCategory);
                $user->setAddress($oldEMTUser['profileAddress']);
                $user->setAddress2($oldEMTUser['profileAddress2']);
                $user->setPostalCode($oldEMTUser['profileZip']);
                $user->setCity($oldEMTUser['profileCity']);
                
                $userCountry = $em->getRepository('OSSystemEMTBundle:Country')->findOneBy(array('code' => $oldEMTUser['profileCountry']));
                $user->setCountry($userCountry);
                
                
                /*Contact details*/
                $user->setTitle($oldEMTUser['profileTitle']);
                $user->setFName($oldEMTUser['profileFname']);
                $user->setLName($oldEMTUser['profileLname']);
                $user->setJobTitle($oldEMTUser['profileJobTitle']);
                $user->setPhone($oldEMTUser['profilePhone']);
                $user->setOptIn((int)$oldEMTUser['profileOptIn']);
            
            }   
            
            $em->persist($user); 
  
        }
        echo "\n flushing... \n";
        $em->flush(); 
        echo " users were imported\n";
    }
    
    protected function syncConferences()
    {
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();
        
        $statement = $connection->prepare("SELECT * FROM conference WHERE " . self::generealCondition );
        
        $statement->execute();
        $results = $statement->fetchAll();
        
        foreach($results as $oldEMTConference) {
            $conferenceId = $oldEMTConference['id'];
            echo $conferenceId." ";
            $isConference = $em->getRepository('OSSystemEMTBundle:Conference')->find($conferenceId);
            
            $conference = $isConference ? $isConference : new Conference();
            

            $conference->setId($oldEMTConference['id']);
            $user = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('oldId'=>$oldEMTConference['user_id']));
            
            $conference->setUser($user);
            
            $conference->setIsPublished($oldEMTConference['show_conference']);

            $conference->setTitle($oldEMTConference['name']) ;
            $conference->setAcronym($oldEMTConference['acronym']);
            $conference->setStartConferenceDate(date_create($oldEMTConference['start_date']));
            $conference->setEndConferenceDate(date_create($oldEMTConference['end_date']));
            $conference->setStartConferenceTime($oldEMTConference['start_time']);
            $conference->setEndConferenceTime($oldEMTConference['end_time']);
            
            
            $conference->setOrganizingCompaniesNames($oldEMTConference['organiser']);
            $conference->setContactPersonsNames($oldEMTConference['contact_person']);
            $conference->setOrganizingEmails($oldEMTConference['email']);
            $conference->setWebsite($oldEMTConference['website']);
            $conference->setEmail($oldEMTConference['general_email']);
            
            $conference->setCreatedAt(date_create($oldEMTConference['created_at']));
            $conference->setUpdateAt(date_create($oldEMTConference['updated_at']));
            /***************************/
            switch($oldEMTConference['international_participant']){
                case 'Yes':
                default:    
                    $conference->setDelegatesMoreThan1Country(1);
                    break;
                case 'No':
                    $conference->setDelegatesMoreThan1Country(0);
                    break;
                case 'No information available':
                    $conference->setDelegatesMoreThan1Country(2);
                    break;
            }
            
            $conference->setDelegatesAnticipate($oldEMTConference['number_of_participants']);
            /*************************/
            
            $conference->setAssesmentNameVenue($oldEMTConference['venue_name']);
            $conference->setAssesmentCategoryVenue($oldEMTConference['venue_category']);
            $conference->setAssesmentCity($oldEMTConference['venue_city']);
            
            $conferenceCountry = $em->getRepository('OSSystemEMTBundle:Country')->findOneBy(array('code' => $oldEMTConference['venue_country']));
            $conference->setAssesmentCountry($conferenceCountry);
            
            $conference->setAssesmentLocalNA($oldEMTConference['venue_national_association']);
            $conference->setAssesmentProposedAccomodation($oldEMTConference['accommodation_url']);
            
            $conference->setEventProgramme($oldEMTConference['programme']);
            $conference->setComments($oldEMTConference['comments']);
            
            $conferenceTherapeuticArea = $em->getRepository('OSSystemEMTBundle:TherapeuticArea')->findOneBy(array('title' => $oldEMTConference['medical_area']));
            $conference->setTherapeuticArea($conferenceTherapeuticArea);
            
            $conference->setTherapeuticAreaOther($oldEMTConference['medical_area_other']);
            
            /*countries*/
            $conferenceCountries = unserialize($oldEMTConference['countries']);
            if ($conferenceCountries){
                foreach($conferenceCountries as $country ){
                    $delegatesCountry = $em->getRepository('OSSystemEMTBundle:Country')->findOneBy(array('code' => $country));
                    //$conference->getDelegatesCountries();
                    if(!in_array($delegatesCountry, $conference->getDelegatesCountries()->toArray())){
                        $conference->addDelegatesCountry($delegatesCountry);
                    }
                }
            }
        
            /*end countries*/
            
            /*Conference status*/
            switch($oldEMTConference['status']){
                case 'Saved':
                default:    
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(1);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'To be reviewed':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(2);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Info complete':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(3);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Compliant':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(4);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Not compliant':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(5);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Non compliant criteria':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(6);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Not assessed':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(7);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Rejected':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(5);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Double Entry':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(9);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Saved for Pre-Clearance':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(10);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'To be reviewed for Pre-Clearance':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(11);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Not compliant for Pre-Clearance':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(12);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Compliant for Pre-Clearance':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(13);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Pre-Cleared':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(13);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'To be reviewed for partial submission':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(16);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Info completed for partial submission':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(16);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
 
                case 'Non compliant criteria for partial submission':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(19);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Compliant for partial submission':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(18);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
                
                case 'Non compliant for partial submission':
                    $conferenceStatus = $em->getRepository('OSSystemEMTBundle:Status')->find(19);
                    $conference->setConferenceStatus($conferenceStatus);
                    break;
               
            }
            /*End Conference status*/
            
            $conference->setConferenceStatusText($oldEMTConference['status_comment']);
            
            $em->persist($conference);
        }
        echo "\n flushing... \n";
        
        $metadata = $em->getClassMetaData(get_class($conference));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $em->flush(); 
 
        echo "conferences were imported\n";
    }
    
    protected function syncConferencesCriteria() {
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();
        
        $statementCriteria = $connection->prepare("SELECT * FROM conference_compliant_criteria WHERE  ". self::generealConditionLink);
        
        $statementCriteria->execute();
        $resultsCriteria = $statementCriteria->fetchAll();
        
        $statementConf = $connection->prepare("SELECT id FROM conference where ".self::generealCondition);
        
        $statementConf->execute();
        $resultsConf = $statementConf->fetchAll();
        
        
    
        /*set for all conferences criteria  status to be reviewed*/
        
        foreach($resultsConf as $oldEMTConf){
            $conference = $em->getRepository('OSSystemEMTBundle:Conference')->find($oldEMTConf);
            echo $oldEMTConf['id'].' ';
            /*check if this conf exist in ConferenceCriteriaState */
            $isConferenceInCriterias = $em->getRepository('OSSystemEMTBundle:ConferenceCriteriaState')->findBy(array('conference'=>$conference));
            
            if(!empty($isConferenceInCriterias)){
                foreach($isConferenceInCriterias as $isConferenceInCriteria){
                    $em->remove($isConferenceInCriteria);
                }
                
            }
           
            for($i =1; $i<=8; $i++){
                $criteria = $em->getRepository('OSSystemEMTBundle:Criteria')->find($i);
                
                
                $conferenceCriteriaState = new ConferenceCriteriaState();
                $conferenceCriteriaState->setConference($conference);
                $conferenceCriteriaState->setCriteria($criteria);
                $conferenceCriteriaState->setStatus(1);
                        
                $em->persist($conferenceCriteriaState);
            }
            
          
        };
        echo "\n flushing... \n";
        $em->flush();
         
        echo "\n criterias \n";
         foreach($resultsCriteria as $oldEMTConferenceCriteria){
            $conferenceId = $em->getRepository('OSSystemEMTBundle:Conference')->find($oldEMTConferenceCriteria['conference_id']);
            $criteria = $em->getRepository('OSSystemEMTBundle:Criteria')->find($oldEMTConferenceCriteria['assessment_criteria_id']);
            
            $conferenceCriteriaState = $em->getRepository('OSSystemEMTBundle:ConferenceCriteriaState')->findOneBy(array('conference'=>$conferenceId, 'criteria'=>$criteria));
           
            echo $conferenceCriteriaState->getId()." ";
            switch($oldEMTConferenceCriteria['status']){
                case '2':
                    $conferenceCriteriaState->setStatus(0);
                    break;
                case null:
                default:    
                    $conferenceCriteriaState->setStatus(1);
                    break;
                case '1':
                    $conferenceCriteriaState->setStatus(2);
                    break;
                case '0':
                    $conferenceCriteriaState->setStatus(3);
                    break;
            }
            

            if($conferenceCriteriaState){
                switch($oldEMTConferenceCriteria['status']){
                    case '2':
                        $conferenceCriteriaState->setStatus(0);
                        break;
                    case null:
                    default:    
                        $conferenceCriteriaState->setStatus(1);
                        break;
                    case '1':
                        $conferenceCriteriaState->setStatus(2);
                        break;
                    case '0':
                        $conferenceCriteriaState->setStatus(3);
                        break;
                }
           

            $em->persist($conferenceCriteriaState);
             }
        }
        echo "\n flushing... \n";
        $em->flush();
       
        
        echo "\nconferences_criteria were imported\n";
    }
    
    protected function syncMessages(){
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();
        
        $statement = $connection->prepare("SELECT * FROM messages ");
        
        $statement->execute();
        $results = $statement->fetchAll();
        
        foreach($results as $oldEMTMessage){
            $message = new Message();
            $message->setContent($oldEMTMessage['message']);
            
            $message->setCreatedAt(date_create($oldEMTMessage['created_at']));
            $message->setUpdatedAt(date_create($oldEMTMessage['updated_at']));
            
            $conference = $em->getRepository('OSSystemEMTBundle:Conference')->find($oldEMTMessage['conference_id']);
            $message->setConference($conference);
            $message->setSubject($oldEMTMessage['subject']);
            if(!$conference->getAssesmentCountry()){
                $conferenceLocation = '';
            }else{
                $conferenceLocation = $conference->getAssesmentCountry()->getLocation();
            };
            switch($conferenceLocation){
                    case 'EU':
                    default:
                        $sender = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('profileType'=> User::PROFILE_TYPE_EUCOMED));
                        break;
                    case 'ME':
                        $sender = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('profileType'=> User::PROFILE_TYPE_MIDDLE_EAST));    
                };
            $message->setSender($sender);
            $recipient = $conference->getUser();
            $message->setRecipient($recipient);
            $message->setTarget(Message::MESSAGE_TARGET_MAIL_BOARD);
            if($recipient){
                $message->setMailto($recipient->getEmail());
            }
            $em->persist($message);
        }
        echo "\n flushing... \n";
        $em->flush();
    }
    
     protected function syncComments(){
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();
        
        $statement = $connection->prepare("SELECT * FROM comments ");
        
        $statement->execute();
        $results = $statement->fetchAll();
        
        foreach($results as $oldEMTComment){
            $message = new Message();
            $message->setContent($oldEMTComment['comment']);
            
            $message->setCreatedAt(date_create($oldEMTComment['created_at']));
            $message->setUpdatedAt(date_create($oldEMTComment['updated_at']));            
            $message->setSubject('Comment');
            $conference = $em->getRepository('OSSystemEMTBundle:Conference')->find($oldEMTComment['conference_id']);
            $message->setConference($conference);
            if(!$conference->getAssesmentCountry()){
                $conferenceLocation = '';
            }else{
                $conferenceLocation = $conference->getAssesmentCountry()->getLocation();
            };
            switch($conferenceLocation){
                    case 'EU':
                    default:
                        $sender = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('profileType'=> User::PROFILE_TYPE_EUCOMED));
                        break;
                    case 'ME':
                        $sender = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('profileType'=> User::PROFILE_TYPE_MIDDLE_EAST));    
                };
            $message->setSender($sender);
             /* for comments */
            $recipient = $em->getRepository('OSSystemEMTBundle:User')->findOneBy(array('oldId'=>$oldEMTComment['user_id']));
            $message->setRecipient($recipient);
            $message->setTarget(Message::MESSAGE_TARGET_MESSAGE_BOARD);
            if($recipient){
                $message->setMailto($recipient->getEmail());
            }
            $em->persist($message);
        }
        echo "\n flushing... \n";
        $em->flush();
    }
    
    protected function syncDocuments(){
        $emOldEMT = $this->getContainer()->get('doctrine')->getManager('oldemt');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $emOldEMT->getConnection();
        
        $statement = $connection->prepare("SELECT conference.id, conference.user_id, conference.accommodation_document, document.file FROM conference"
                . " LEFT JOIN document ON (document.conference_id = conference.id)"
                . " WHERE ". self::generealCondition);
        
        $statement->execute();
        $results = $statement->fetchAll();
        
        $connectionCurrent = $em->getConnection();
        
        $prevConferenceId = 0;
        
        foreach ($results as $row_in){
            
            $name = basename($row_in['file']);
            $path = $row_in['file'];
            $conference_id = $row_in['id'];
            
            echo $conference_id." ";
            
            if (($path == '')||($path == null)) {
                continue;
            }
            
            $accommodation_document = $row_in['accommodation_document'];
            
            $user_id = 1; //default, to be overwritten
            if ($row_in['user_id']){
                $q = "SELECT id FROM fos_user WHERE old_id = ".$row_in['user_id'];
                $statement = $connectionCurrent->prepare($q);
                $statement->execute();

                $user_row = $statement->fetch();
                if ($user_row){
                    $user_id = $user_row['id'];
                }
            }
            
            
            if ($prevConferenceId != $conference_id){
            
                $q = "DELETE FROM documents_conference WHERE conference_id = ".$conference_id;
                $statement = $connectionCurrent->prepare($q);
                $statement->execute();
                
                $q = "DELETE FROM assesmentdocuments WHERE conference_id = ".$conference_id;
                $statement = $connectionCurrent->prepare($q);
                $statement->execute();

                $q = "DELETE FROM document WHERE target IN (1, 2,3) AND conference_id = ".$conference_id;
                $statement = $connectionCurrent->prepare($q);
                $statement->execute();
                
                if (($accommodation_document != '')&&($accommodation_document != null)) {
                    $q = "INSERT           INTO `document`(`name`, `path`, `conference_id`, `createdAt`, `updateAt`, `user_id`, `target`) VALUES "
                        . "(:name, :path , :conference_id, '" . time() . "', '" .time(). "', :user_id , 1)";
                    $statement = $connectionCurrent->prepare($q);
                    $statement->bindValue('name', basename($accommodation_document));
                    $statement->bindValue('path', $accommodation_document);
                    $statement->bindValue('conference_id', $conference_id);
                    $statement->bindValue('user_id', $user_id);
                    $statement->execute();

                    $doc_id = $connectionCurrent->lastInsertId(); 
                    if ($doc_id){
                        $q = "INSERT INTO `assesmentdocuments`(`conference_id`, `document_id`) VALUES "
                            . "('$conference_id', '$doc_id')";
                        $statement = $connectionCurrent->prepare($q);
                        $statement->execute();
                    }else{
                        echo 'accommodation doc not inserted';
                    }
                }
                
                $prevConferenceId = $conference_id;
            }
            
            
            $q = "INSERT INTO `document`(`name`, `path`, `conference_id`, `createdAt`, `updateAt`, `user_id`, `target`) VALUES "
                    . "(:name, :path , :conference_id, '" . time() . "', '" .time(). "', :user_id , 2)";
            $statement = $connectionCurrent->prepare($q);
            $statement->bindValue('name', $name);
            $statement->bindValue('path', $path);
            $statement->bindValue('conference_id', $conference_id);
            $statement->bindValue('user_id', $user_id);
            $statement->execute();
            
            $doc_id = $connectionCurrent->lastInsertId(); 
            if ($doc_id){
                $q = "INSERT INTO `documents_conference`(`conference_id`, `document_id`) VALUES "
                    . "('$conference_id', '$doc_id')";
                $statement = $connectionCurrent->prepare($q);
                $statement->execute();
            }else{
                echo 'doc not inserted';
            }
            
        }
        echo "docs were imported\n";
    }
    
    
}