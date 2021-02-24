<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUser($role)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted IS NULL');
        $query->andWhere('e.roles LIKE :roles')
                ->setParameter('roles', '%"'.$role.'"%');
        $query->orWhere('e.isDeleted = false');
        $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
        return (object) $data;
    }

    public function getByUsername($username)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted = false or e.isDeleted IS NULL');
        $query->andWhere('e.isActive = true');
        $query->andWhere('e.username = :user')->setParameter('user', $username);
        $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
        return $data;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function updateUser(User $user, $data, UserPasswordEncoderInterface $encoder)
    {
        empty($data->username) ? true : $user->setUsername($data->username);
        empty($data->password) ? true : $user->setPassword($encoder->encodePassword($user, $data->password));
        empty($data->email) ? true : $user->setEmail($data->email);

        $this->_em->flush();
    }

    public function removeUser(User $user)
    {
        $user->setIsDeleted(true);

        $this->_em->flush();
    }

    public function setPassport(User $user)
    {
        $user->setIsPassportActive(true);
        $user->setPassportAccess(uniqid().'-'.uniqid().'-'.uniqid());
        $user->setPassportExpiry(date("Y-m-d H:i:s", time() + 86400));

        $this->_em->flush();
    }

    public function activateUser(User $user)
    {
        $user->setIsActive(!$user->getIsActive());

        $this->_em->flush();
    }
}
