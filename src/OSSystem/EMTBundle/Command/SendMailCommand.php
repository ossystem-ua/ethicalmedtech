<?php

namespace OSSystem\EMTBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OSSystem\EMTBundle\Entity\Conference;
use OSSystem\EMTBundle\Entity\User;
use OSSystem\EMTBundle\Entity\Status;
use OSSystem\EMTBundle\Entity\TimeNotifications;

use OSSystem\EMTBundle\Controller\DefaultController;

class SendMailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('send:mail')
            ->setDescription('send notifications');
          
    }
    

    protected function execute(InputInterface $input, OutputInterface $output)
        {
            $output->writeln("<info>send notifications</info>\n");
            /* after 10 day */
            $em = $this->getContainer()->get('doctrine')->getManager();
            $dateNow = new \DateTime('00:00:00');
            $dateNow->format('Y-m-d H:i:s');
            $deadline = array();
            
            $timeRepository = $em->getRepository('OSSystemEMTBundle:TimeNotifications');
            $conferenceRepository = $em->getRepository('OSSystemEMTBundle:Conference');
            
            $deadline = $timeRepository->findBy(array('deadlineDate'=>$dateNow));
            
            foreach($deadline as $conf){
                
                $conference = $conferenceRepository->getConferenceById($conf->getConference())->getSingleResult();
                $params = $conference;
                
                switch ($conference->getConferenceStatus()->getId()) {
                    case '2':
                        $em->remove($conf);
                        break;
                    case '15':
                        $em->remove($conf);
                        break;
                    case '5':
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/_msgNotCompliantApplicant.html.twig';
                        $templatename_per = 'OSSystemEMTBundle:Default:Notifications/RegularSubmission/_msgNotCompliantPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Assessment final decision';
                        $to_app = $conference->getOrganizingEmails();
                        $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->getContainer()->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        $subject = 'Ethical Med Tech - Conference Vetting System - Assessment final decision';
                        $to_per = $conference->getUser()->getEmail();
                        $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to_per, $templatename_per, $params);
                        /* Save mail to Message board */
                        $this->getContainer()->get('emt.savemail')->MailToMessage($subject,$to_per, $templatename_per, $params);
                        $em->remove($conf);
                        break;
                    case '19':
                        $templatename_app = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSNotCompliantApplicant.html.twig';
                        $templatename_pco = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgPSNotCompliantPCO.html.twig';
                        $subject = 'Ethical Med Tech - Conference Vetting System - Not compliance notice to applicant(Partial Submission)';
                        $to_app = $conference->getOrganizingEmails();
                        $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to_app, $templatename_app, $params);
                        /* Save mail to Message board */
                        $this->getContainer()->get('emt.savemail')->MailToMessage($subject, $to_app, $templatename_app, $params);
                        $subject = 'Ethical Med Tech - Conference Vetting System - Not compliance notice to PCO(Partial Submission)';
                        $to_per = $conference->getUser()->getEmail();
                        $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to_per, $templatename_pco, $params);
                        /* Save mail to Message board */
                        $this->getContainer()->get('emt.savemail')->MailToMessage($subject,$to_per, $templatename_per, $params);
                        $em->remove($conf);
                        break;
                }
              
            }
                
             /* At 35 day */
                $confer_all = $conferenceRepository->findBy(array());
                
               foreach($confer_all as $conf){
                   $params = $conf;
                   if($conf->getStartConferenceDate()){
                        
                        $d35 =  strtotime("-35 days", $conf->getStartConferenceDate()->getTimestamp());
                         if($d35 < time()){
                            switch ($conf->getConferenceStatus()->getId()) {                                
                                 case '18':
                                    $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgAddInfo_35d_Compliant.html.twig';
                                    $subject = 'Ethical Med Tech - Conference Vetting System - Additional information provided, Final decision is compliant';
                                    $to = $conf->getUser()->getEmail();
                                    $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                                    /* Save mail to Message board */
                                    $this->getContainer()->get('emt.savemail')->MailToMessage($subject,$to, $templatename, $params);
                                  break;
                                case '19':
                                   $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgAddInfo_35d_NotCompliant.html.twig';
                                    $subject = 'Additional information provided, Final decision is compliant';
                                    $to = $conf->getUser()->getEmail();
                                    $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                                    /* Save mail to Message board */
                                    $this->getContainer()->get('emt.savemail')->MailToMessage($subject,$to, $templatename, $params);
                                    break;
                                case '7':
                                   $templatename = 'OSSystemEMTBundle:Default:Notifications/PartialSubmission/_msgAddInfo_35d_NotAssesed.html.twig';
                                    $subject = 'Ethical Med Tech - Conference Vetting System - Additional information provided, Final decision is compliant';
                                    $to = $conf->getUser()->getEmail();
                                    $this->getContainer()->get('emt.notification')->sendNotificationEmail($subject,$to, $templatename, $params);
                                    /* Save mail to Message board */
                                    $this->getContainer()->get('emt.savemail')->MailToMessage($subject,$to, $templatename, $params);
                                  break;
                                }
                         }
                   }
                
               }
            $em->flush();
            $output->writeln("\n<info>finish!</info>");                    
        
        }
        
     
    
}


