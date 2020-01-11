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

namespace App\Security\Voter;

use App\Entity\Bill;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BillVoter extends Voter
{
    /*
     * Constants.
     */
    public const SHOW = 'show';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::SHOW], true)) {
            return false;
        }

        // only vote on Bill objects inside this voter
        if (!$subject instanceof Bill) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute attribute
     * @param Bill           $subject   subject is a bill, thanks to the supports method
     * @param TokenInterface $token     token interface
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView($subject, $user);
        }

        return false;
    }

    /**
     * This bill can be showed only if it's owned by user.
     *
     * @param Bill $bill bill to show
     * @param User $user user currently connected
     */
    private function canView(Bill $bill, User $user): bool
    {
        return $bill->getCustomer() === $user;
    }
}
