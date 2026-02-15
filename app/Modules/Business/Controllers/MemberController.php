<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexMemberRequest;
use App\Modules\Business\Requests\InviteMemberRequest;
use App\Modules\Business\Resources\MemberResource;
use Domain\Business\Actions\InviteMember;
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
        return MemberResource::collection($memberRepository->getPaginated($request->toDto()));
    }

    /**
     * Invite a user as a member of a business
     */
    public function store(InviteMemberRequest $request, InviteMember $action): MemberResource
    {
        return (new MemberResource($action->handle($request->toDto())))->status(Response::HTTP_CREATED);
    }
}
