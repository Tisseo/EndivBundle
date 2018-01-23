<?php

namespace Tisseo\EndivBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Tisseo\EndivBundle\Entity\Log;

class LogSubscriber implements EventSubscriber
{
    private $container;

    private $requestStack;

    private $accessor;

    private $logs = array();

    const LOG_ACTION_INSERT = 'INSERT';
    const LOG_ACTION_UPDATE = 'UPDATE';
    const LOG_ACTION_DELETE = 'DELETE';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
            'preUpdate',
            'postPersist',
            'postFlush'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->logs)) {
            $entityManager = $event->getEntityManager();
            foreach ($this->logs as $log) {
                $entityManager->persist($log);
            }

            $this->logs = [];
            $entityManager->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->log($args, self::LOG_ACTION_DELETE);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->log($args, self::LOG_ACTION_UPDATE);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if (!$args->getEntity() instanceof Log) {
            $this->log($args, self::LOG_ACTION_INSERT);
        }
    }

    /**
     * Log
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     *
     * Writing log for each detected action in database.
     */
    private function log(LifeCycleEventArgs $args, $action)
    {
        $user = $this->container->get('security.context')->getToken();

        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $entityTableName = $entityManager->getClassMetadata(get_class($entity))->getTableName();
        preg_match("/^.+\\\(\w+)Bundle/", $this->requestStack->getCurrentRequest()->attributes->get('_controller'), $bundleName);

        $log = new Log();
        $log->setDatetime(new \DateTime());
        $log->setUser($user->getUsername().'@'.$bundleName[1]);
        $log->setAction($action);

        switch ($action) {
            case self::LOG_ACTION_INSERT:
                $log->setTable($entityTableName);

                try {
                    $log->setInsertedData($this->entityToString($entity));
                } catch (\Exception $e) {
                    $log->setInsertedData($e->getMessage());
                }

                break;
            case self::LOG_ACTION_UPDATE:
                if (method_exists($entity, 'getId')) {
                    $table = $entityTableName.' ('.$entity->getId().')';
                    if (strlen($table) > 30) {
                        $table = substr($table, 0, 30);
                    }
                    $log->setTable($table);
                } else {
                    $log->setTable($entityTableName);
                }

                $changeSet = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);

                $oldData = '';
                $newData = '';
                try {
                    foreach ($changeSet as $key => $val) {
                        $this->entityPropertyToString($oldData, $key, $val[0]);
                        $this->entityPropertyToString($newData, $key, $val[1]);
                    }
                    $log->setInsertedData(trim($newData));
                    $log->setPreviousData(trim($oldData));
                } catch (\Exception $e) {
                    $log->setPreviousData($e->getMessage);
                }
                break;
            case self::LOG_ACTION_DELETE:
                $log->setTable($entityTableName);

                try {
                    $log->setPreviousData($this->entityToString($entity));
                } catch (\Exception $e) {
                    $log->setPreviousData($e->getMessage());
                }
                break;
            default:
        }

        $this->logs[] = $log;
    }

    /**
     * Entity to string
     *
     * @param object $entity
     *
     * @return string $string
     *
     * Transforming any entity into a string.
     */
    private function entityToString($entity)
    {
        $reflect = new \ReflectionClass($entity);
        $props = $reflect->getProperties();
        $string = '';

        foreach ($props as $prop) {
            $propertyValue = $this->accessor->getValue($entity, $prop->getName());
            $this->entityPropertyToString($string, $prop->getName(), $propertyValue);
        }

        return trim($string);
    }

    /**
     * EntityPropertyToString
     *
     * @param string $string
     * @param string $propertyName
     * @param void   $propertyValue
     *
     * Transforming a property into a string.
     */
    private function entityPropertyToString(&$string, $propertyName, $propertyValue)
    {
        if (!is_object($propertyValue)) {
            $string .= $propertyName.':'.($propertyValue !== null ? (strlen($propertyValue) > 0 ? strval($propertyValue) : '0') : 'null');
        } else {
            $propertyReflect = new \ReflectionClass($propertyValue);
            if ($propertyReflect->hasMethod('getId')) {
                $string .= $propertyName.':'.$propertyValue->getId();
            } elseif ($propertyValue instanceof \Datetime) {
                $string .= $propertyName.':'.$propertyValue->format('Y-m-d');
            }
        }
        $string .= ' ';
    }
}
