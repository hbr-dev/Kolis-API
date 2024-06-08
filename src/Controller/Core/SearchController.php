<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Exception;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route("/core")]
class SearchController extends AbstractController
{
    private $connection;
    private $exceptionManager;

    public function __construct(Connection $connection, ExceptionManager $exceptionManager)
    {
        $this->connection = $connection;
        $this->exceptionManager = $exceptionManager;
    }

    #[Route("/search", name: "dynamic_search", methods: ["GET"])]
    public function dynamicSearch(Request $request)
    {
        $page = $request->query->get('defaultPage', 1);
        $perPage = $request->query->get('perPage', 10);
        $table = $request->query->get('table');
        $filters = $request->query->get('filters', []);

        // Perform access control for tables here

        $query = $this->connection->createQueryBuilder();

        $query->select('*')->from($table);

        foreach ($filters as $filter) {
            $column = $filter['column'];
            $value = $filter['value'];
            $type = $filter['type'];

            switch ($type) {
                case 'number':
                    $query->andWhere("$column = :value")->setParameter('value', $value);
                    break;
                case 'string':
                    $query->andWhere("$column LIKE :value")->setParameter('value', "%$value%");
                    break;
                case 'date':
                    try {
                        $dateObject = new \DateTime($value);
                        $year = $dateObject->format('Y');
                        $month = $dateObject->format('m');
                        $day = $dateObject->format('d');
    
                        $query->andWhere("TO_CHAR($column, 'YYYY') LIKE :year AND TO_CHAR($column, 'MM') LIKE :month AND TO_CHAR($column, 'DD') LIKE :day")
                            ->setParameter('year', $year . '%')
                            ->setParameter('month', $month . '%')
                            ->setParameter('day', $day . '%');
                    } catch (\Exception $e) {
                        throw new BadRequestHttpException('Invalid date format');
                    }
                    break;
            }
        }

        $query->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $results = $query->executeQuery()->fetchAllAssociative();

        return new JsonResponse($results);
    }
}


// use Doctrine\ORM\Exception\ORMException;
// use Doctrine\Persistence\ManagerRegistry;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// #[Route("/core")]
// class SearchController extends AbstractController
// {
//     private $doctrine;

//     public function __construct(ManagerRegistry $doctrine)
//     {
//         $this->doctrine = $doctrine;
//     }


//     #[Route("/search", name: "search", methods: ["POST"])]
//     public function search(Request $request)
//     {
//         $table = $request->get('table');
//         $filters = $request->query->get('filters', []);
//         $page = $request->query->get('defaultPage', 1);
//         $size = $request->query->get('size', 10);

//         if (!$table) {
//             return [
//                 'BadRequest' => 'Missing required parameter "table"'
//             ];
//         }

//         $tableName = ucfirst($table);
//         $className = "App\Entity\\" . $tableName;

//         try {
//             $repository = $this->doctrine->getManager()->getRepository($className);
//         } catch (ORMException $e) {
//             return [
//                 'InternalServerError' => 'An error occurred while retrieving data. Please try again later.' . $e->getMessage() // Consider logging the full exception for debugging
//             ];
//         }

//         $qb = $repository->createQueryBuilder('entity');

//         $this->buildQueryFilters($qb, $filters);

//         $qb->setFirstResult(($page - 1) * $size)
//             ->setMaxResults($size);


//         $results = $qb->getQuery()->getResult();

//         $response = [];
//         foreach ($results as $result) {
//             $response[] = $result->toArray();
//         }

//         return $response;
//     }



//     private function buildQueryFilters($qb, $filters)
//     {
//         $whereClauses = [];
//         $parameters = [];

//         foreach ($filters as $field => $value) {
//             // Escape user-provided filters to prevent SQL injection
//             $escapedValue = pg_escape_string($value);

//             $filterType = is_numeric($value) ? 'numeric' : 'string';
//             switch ($filterType) {
//                 case 'numeric':
//                     $whereClauses[] = "entity.$field = :$field";
//                     break;
//                 case 'string':
//                     $whereClauses[] = "entity.$field LIKE :$field";
//                     break;
//                 default:
//                     throw new \InvalidArgumentException("Unsupported filter type for field '$field'");
//             }

//             $parameters[":$field"] = $escapedValue;
//         }

//         $qb->where(implode(' AND ', $whereClauses));

//         $qb->setParameters($parameters);
//     }



//     // private function buildQueryFilters($connection, $filters, $tableName)
//     // {
//     //     $whereClauses = [];
//     //     $parameters = [];

//     //     foreach ($filters as $field => $value) {
//     //         // Escape user-provided filters to prevent SQL injection
//     //         $escapedValue = pg_escape_string($value);
            
//     //         $filterType = is_numeric($value) ? 'numeric' : 'string';
//     //         switch ($filterType) {
//     //             case 'numeric':
//     //                 $whereClauses[] = "$field = :$field";
//     //                 break;
//     //             case 'string':
//     //                 $whereClauses[] = "$field ILIKE CONCAT('%', :$field, '%')";
//     //                 break;
//     //             default:
//     //                 throw new \InvalidArgumentException("Unsupported filter type for field '$field'");
//     //         }

//     //         $parameters[":$field"] = $escapedValue;
//     //     }
        
//     //     $whereClause = '';
//     //     if (!empty($whereClauses)) {
//     //         $whereClause = ' WHERE ' . implode(' AND ', $whereClauses);
//     //     }
        
//     //     $sql = "SELECT * FROM $tableName $whereClause";
        
//     //     $statement = $connection->prepare($sql);
//     //     $statement->execute($parameters);
        
//     //     $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

//     //     return $results;
//     // }
// }
