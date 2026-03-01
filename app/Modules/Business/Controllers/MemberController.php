<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexMemberRequest;
use App\Modules\Business\Requests\InviteMemberRequest;
use App\Modules\Business\Resources\MemberResource;
use Domain\Business\Actions\DeleteMemberAction;
use Domain\Business\Actions\InviteMemberAction;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Symfony\Component\HttpFoundation\Response;

final class MemberController extends BaseController
{
    /**
     * Returns a paginated list of members for the given business.
     */
    public function index(IndexMemberRequest $request, Business $business, MemberRepositoryInterface $memberRepository): BaseAnonymousCollection
    {
        $memberPaginated = $memberRepository->indexPaginated($request->toDto(), $business);

        return MemberResource::collection($memberPaginated);
    }

    /**
     * Invites a new member to the given business. Returns 201 on success.
     * @throws BusinessException 409 if the user is already a member of the business
     */
    public function store(InviteMemberRequest $request, Business $business, InviteMemberAction $inviteMemberAction): MemberResource
    {
        $memberInvited = $inviteMemberAction->handle($request->toDto(), $business);

        return new MemberResource($memberInvited)->status(Response::HTTP_CREATED);
    }


    /**
     * Deletes a member from the given business. Returns 204 on success.
     * @throws NotFoundException 404 if the member does not belong to the business
     * @throws BusinessException 409 if the member is the owner of the business
     */
    public function delete(Business $business, Member $member, DeleteMemberAction $deleteMemberAction): \Illuminate\Http\Response
    {
        $deleteMemberAction->handle($member, $business);

        return response()->noContent();
    }
}
