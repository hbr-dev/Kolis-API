<?php

namespace App\Manager;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Model\Traits\MyEntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Uid\Uuid;

class AbstractManager
{
    use MyEntityManager;

    private $requestStack;
    private $settings;
    private $em;

    protected $request;

    const PACKAGE_STATUS_CREATED = "created";
    const PACKAGE_STATUS_AWAITING_TRIP = "awaiting_for_trip";
    const PACKAGE_STATUS_DECLINED = "declined";
    const PACKAGE_STATUS_APPROVED = "approved";
    const PACKAEG_STATUS_IN_TRANSIT = "delivery_in_transit";
    const PACKAGE_STATUS_DELIVERED = "delivered";
    const PACKAGE_STATUS_DAMAGED = "damaged";
    const PACKAGE_STATUS_LOST = "lost";

    const TRIP_STATUS_CREATED = "created";
    const TRIP_STATUS_CONFIRMED = "confirmed";
    const TRIP_STATUS_IN_PROGRESS = "in_progress";
    const TRIP_STATUS_ACCOMPLISHED = "accomplished";
    const TRIP_STATUS_CANCELED = "cancelled";

    const ROLE_BACK = 'ROLE_BACK';
    const ROLE_CLIENT = 'ROLE_CLIENT';
    const ROLE_TRANSPORTER = 'ROLE_TRANSPORTER';


    protected const HASH_ALGO = "sha512";





    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;

        if ($requestStack instanceof RequestStack) {
            $this->request = $requestStack->getCurrentRequest();

            if (!$this->request) {
                throw new \Exception('No request found.');
            }
        }
    }



    public function insertObject($data, $class, $field = null, $options = array())
    {
        if ($field && count($options)) {
            $this->validateUnicity($class, $field, $options);
        }

        $object = new $class($data);

        $this->configureObjectDefaults($object, array_key_exists('code', $data));

        $this->em->persist($object);
        $this->em->flush();
        $this->em->refresh($object);

        return $object;
    }



    public function updateObject($class, $object, $data, $field = null, $options = array())
    {
        if ($field && count($options)) {
            $this->validateUnicity($class, $field, $options, $object);
        }

        if ($object instanceof $class) {

            $object->fromArray($data);

            $dataToReturn = [
                'messages' => 'update_success',
            ];

            if (method_exists($object, 'getCode')) {
                $dataToReturn['code'] = $object->getCode();
            } else {
                $dataToReturn['code'] = $object->getId();
            }

            $object->setUpdatedAt(new \DateTime());

            $this->em->persist($object);
            $this->em->flush();

            return ['data' => $dataToReturn];
        }

        return ['data' => [
            'messages' => 'update_fail'
        ]];
    }


    protected function setSettings($settings)
    {
        $this->settings = $settings;

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($this->settings as $property => $value) {
            try {
                $accessor->setValue($this, $property, $value);
            } catch (ExceptionInterface $e) {
                throw $e;
            }
        }
        return $this;
    }



    public function patchObject($class, $object, $field = null, $options = array())
    {
        if ($field && count($options)) {
            $this->validateUnicity($class, $field, $options, $object);
        }

        if ($object instanceof $class) {

            $dataToReturn = [
                'messages' => 'patch_success',
            ];

            if (method_exists($object, 'getCode')) {
                $dataToReturn['code'] = $object->getCode();
            } else {
                $dataToReturn['code'] = $object->getId();
            }

            $object->setUpdatedAt(new \DateTime());

            $this->em->flush();

            return ['data' => $dataToReturn];
        }

        return ['data' => [
            'messages' => 'patch_fail'
        ]];
    }




    public function deleteObject($object, $prevSession = null)
    {
        if ($prevSession) {
            $code = $object->getId();
            $message = "LoggedOut_success";
        } else {
            $code = $object->getCode();
            $message = "delete_success";
        }

        $this->em->remove($object);
        $this->em->flush();

        return ['data' => [
            'code' => $code,
            'messages' => $message,
        ]];
    }




    public function getObjectsByOwner(string $entity, $field, $user)
    {
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $repository = $this->em->getRepository('App\Entity\\' . $entity);
        $objects = $repository->findBy([$field => $user]);

        return $objects;
    }




    public function getObjectsByCriteria(string $entity, $criteria, $orderBy = null, $limit = null)
    {
        $repository = $this->em->getRepository('App\Entity\\' . $entity);
        $objects = $repository->findBy($criteria, $orderBy, $limit);

        return $objects;
    }



    private function validateUnicity($class, $field, $options, $objectCompare = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('o')
            ->from($class, 'o');

        foreach ($options as $option => $value) {
            $paramName = 'param_' . $option;
            $qb->andWhere("o.$option = :$paramName")
                ->setParameter($paramName, $value);
        }

        $query = $qb->getQuery();
        $objects = $query->getResult();

        if ($objectCompare && $this->inCollection($objectCompare, $objects)) {
            return true;
        }

        if (!empty($objects)) {
            throw new ConflictHttpException(json_encode([
                'error' => 'NOT_UNIQUE_ENTITY',
                'parameter' => json_encode(is_array($field) ? $field : [$field])
            ]));
        }

        return true;
    }



    private function configureObjectDefaults($object, $isCodeSet)
    {
        $reflection = new \ReflectionClass($object);

        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Check if property has a setter method
            $setterMethod = 'set' . ucfirst($propertyName);

            if (!method_exists($object, $setterMethod)) {
                continue;
            }

            // Check if default value should be set
            if ((!$isCodeSet && $propertyName === 'code') || $propertyName === 'updatedAt') {
                $value = $this->getDefaultValue($propertyName);
                if ($value !== null) {
                    $object->$setterMethod($value);
                }
            }
        }
    }



    private function getDefaultValue($propertyName)
    {
        switch ($propertyName) {
            case 'code':
                return Uuid::v4();
            case 'updatedAt':
                return new \DateTime();
            default:
                return null;
        }
    }
}
