<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use JMS\Payment\CoreBundle\Entity\Payment;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Payment repository.
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $paymentBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $paymentBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    /**
     * PaymentRepository constructor.
     *
     * @param RegistryInterface $registry registry is injected by DI
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * Find one payment by uuid.
     *
     * @param string $uuid $uuid to retrieve payment
     *
     * @return Payment|null
     */
    public function findByPaymentInstruction(PaymentInstruction $instruction): ?Payment
    {
        return $this->findOneBy(['paymentInstruction' => $instruction]);
    }
}
