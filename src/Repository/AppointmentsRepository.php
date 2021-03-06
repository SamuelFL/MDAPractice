<?php

namespace App\Repository;

use App\Entity\Appointments;
use App\Entity\Doctors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Appointments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointments[]    findAll()
 * @method Appointments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Appointments::class);
    }
    /**
     * @return Appointments[]
     */
	public function findByPatientId($id): array
    {
		return $this->createQueryBuilder('a')
				->join("a.doctor", "u")
				->addSelect("u")
				->where("a.patient = :id")
				->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }
    /**
     * @return Appointments[]
     */
	public function findByDoctorId($id): array
    {
		return $this->createQueryBuilder('a')
				->join("a.patient", "u")
				->addSelect("u")
				->where("a.doctor = :id")
				->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }
	public function remove($id)
	{
		return $this->createQueryBuilder('a')
				->delete()
				->where("a.id = :id")
				->setParameter('id', $id)
                ->getQuery()
				->execute();
	}
	public function findByDate($date)
	{
		
		return $this->createQueryBuilder('a')
				->addSelect("a")
				->where("a.date = :date")
				->setParameter('date', $date)
                ->getQuery()
                ->getArrayResult();
	}
    public function findByDateAndDoctor($date,?Doctors $doctor)
    {

        return $this->createQueryBuilder('a')
            ->addSelect("a")
            ->where("a.doctor = :doctor")
            ->andWhere("a.date = :date")
            ->setParameter('date', $date)
            ->setParameter('doctor', $doctor->getDni())
            ->getQuery()
            ->getArrayResult();
    }

    public function addAppointment(?Appointments $appointment)
    {
        $em = $this->getEntityManager();
        try {
            $em->persist($appointment);
            $em->flush();
        } catch (OptimisticLockException $e) {
            return $e;

        } catch(UniqueConstraintViolationException $e){
            return 'Duplicated Appointment';
        } catch (ORMException $e) {
            return $e;
        } catch( \PDOException $e )
        {
        return $e;
        }
        return null;
    }

    public function findById($value): ?Appointments
    {
        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.id = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function update(Appointments $appointment)
    {
        $em = $this->getEntityManager();
        try {
            $em->merge($appointment);
            $em->flush();
        } catch (ORMException $e) {
            return $e;
        }
        return null;
    }

}