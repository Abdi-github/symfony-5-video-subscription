<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    protected $requestStack;
    private $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    private function getRequest()
    {
        $request = $this->requestStack->getCurrentRequest();
        $uri = $request->getRequestUri();
        $parts = \explode('/', $uri);
        $requestedUserId = $parts[3];
        return $requestedUserId;
    }

    protected function supports(string $attribute, $user): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['PROFILE_VIEW'])
            && $user instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $user, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {

            case 'PROFILE_VIEW':


                if ($this->getRequest() == $user->getId()) {
                    return \true;
                }

                break;
        }

        return false;
    }
}
