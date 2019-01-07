<?php
/*
 * This file is part of the CleverAge/EAVManager package.
 *
 * Copyright (c) 2015-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\EAVManager\AdminBundle\Action\EAV;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sidus\AdminBundle\Action\ActionInjectableInterface;
use Sidus\AdminBundle\Admin\Action;
use Sidus\EAVModelBundle\Entity\DataInterface;
use Sidus\EAVModelBundle\Model\FamilyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Security("is_granted('create', family)")
 */
class CreateFromParentAction implements ActionInjectableInterface
{
    /** @var EditAction */
    protected $editAction;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var Action */
    protected $action;

    /**
     * @param EditAction                    $editAction
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EditAction $editAction, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->editAction = $editAction;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Request         $request
     * @param FamilyInterface $family
     * @param DataInterface   $parentData
     * @param string          $parentAttributeCode
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        FamilyInterface $family,
        DataInterface $parentData,
        $parentAttributeCode
    ): Response {
        $this->editAction->setAction($this->action);
        $admin = $this->action->getAdmin();

        foreach (['edit', 'read'] as $actionCode) {
            if ($admin->hasAction($actionCode) && $this->authorizationChecker->isGranted($actionCode, $family)) {
                $this->editAction->setRedirectAction($admin->getAction($actionCode));
                break;
            }
        }

        $parentAttribute = $parentData->getFamily()->getAttribute($parentAttributeCode);
        $childAttributeCode = $parentAttribute->getOption('relations', [])['families'][$family->getCode()] ?? null;
        if (null === $childAttributeCode || !$family->hasAttribute($childAttributeCode)) {
            $m = "Relational attribute {$parentData->getFamilyCode()}.{$parentAttributeCode} has no ";
            $m .= "relation to current family {$family->getCode()}";
            throw new \UnexpectedValueException($m);
        }
        $data = $family->createData();
        $data->set($childAttributeCode, $parentData);

        return ($this->editAction)($request, $data, $family);
    }

    /**
     * @param Action $action
     */
    public function setAction(Action $action): void
    {
        $this->action = $action;
    }
}
