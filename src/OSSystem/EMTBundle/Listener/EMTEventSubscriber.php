<?php

namespace OSSystem\EMTBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OSSystem\EMTBundle\Entity\Message;

class EMTEventSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface $container
     */
    private $container;



    /**
     * Construct
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }
    
    public function getSubscribedEvents() {
         return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }
    
    public function index(LifecycleEventArgs $args)
    {

    }

    

}

