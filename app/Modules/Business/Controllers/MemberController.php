<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexMemberRequest;
use App\Modules\Business\Requests\InviteMemberRequest;
use App\Modules\Business\Resources\MemberResource;
use Domain\Business\Actions\InviteMemberAction;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * MemberController handles HTTP requests for business membership management.
 */
final class MemberController extends BaseController
{
    /**
     * List members of a business
     */
    public function index(IndexMemberRequest $request, MemberRepositoryInterface $memberRepository): BaseAnonymousCollection
    {
        $memberPaginated = $memberRepository->indexPaginated($request->toDto());
        return MemberResource::collection($memberPaginated);
    }

    /**
     * Invite a user as a member of a business
     */
    public function store(InviteMemberRequest $request, InviteMemberAction $inviteMemberAction): MemberResource
    {
        $memberInvited = $inviteMemberAction->handle($request->toDto());
        return new MemberResource($memberInvited)->status(Response::HTTP_CREATED);
    }
}
