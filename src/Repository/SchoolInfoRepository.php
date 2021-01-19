<?php

namespace App\Repository;

use App\Entity\SchoolInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method SchoolInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolInfo[]    findAll()
 * @method SchoolInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolInfoRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, SchoolInfo::class);
      $this->manager = $manager;
  }

  public function getAllSchoolInfo() {
      $query = $this->createQueryBuilder('e');
      $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
      return (object) $data;
    }

  public function saveSchoolInfo($schoolInfoData)
  {
      $schoolInfo = new SchoolInfo();
      $schoolInfo->setPhone($schoolInfoData->phone);
      $schoolInfo->setName($schoolInfoData->name);
      $schoolInfo->setEmail($schoolInfoData->email);
      $schoolInfo->setAddress($schoolInfoData->address);
      $schoolInfo->setProvince($schoolInfoData->province);
      $schoolInfo->setCity($schoolInfoData->city);
      $schoolInfo->setSubdistrict($schoolInfoData->subdistrict);
      $schoolInfo->setPostalCode($schoolInfoData->postalCode);

      $this->manager->persist($schoolInfo);
      $this->manager->flush();
  }

  public function updateSchoolInfo(SchoolInfo $schoolInfo, $schoolInfoData)
  {
    empty($schoolInfoData->phone) ? true : $schoolInfo->setPhone($schoolInfoData->phone);
    empty($schoolInfoData->name) ? true : $schoolInfo->setName($schoolInfoData->name);
    empty($schoolInfoData->email) ? true : $schoolInfo->setEmail($schoolInfoData->email);
    empty($schoolInfoData->address) ? true : $schoolInfo->setAddress($schoolInfoData->address);
    empty($schoolInfoData->province) ? true : $schoolInfo->setProvince($schoolInfoData->province);
    empty($schoolInfoData->city) ? true : $schoolInfo->setCity($schoolInfoData->city);
    empty($schoolInfoData->subdistrict) ? true : $schoolInfo->setSubdistrict($schoolInfoData->subdistrict);
    empty($schoolInfoData->postalCode) ? true : $schoolInfo->setPostalCode($schoolInfoData->postalCode);

      $this->manager->flush();
  }

  public function removeSchoolInfo(SchoolInfo $schoolInfo)
  {
    $schoolInfo->setIsDeleted(true);

    $this->manager->flush();
  }
}
