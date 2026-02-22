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
use Domain\Business\Models\Business;
use Symfony\Component\HttpFoundation\Response;

final class MemberController extends BaseController
{
    public function index(IndexMemberRequest $request, Business $business, MemberRepositoryInterface $memberRepository): BaseAnonymousCollection
    {
        $memberPaginated = $memberRepository->indexPaginated($request->toDto(), $business);

        return MemberResource::collection($memberPaginated);
    }

    public function store(InviteMemberRequest $request, Business $business, InviteMemberAction $inviteMemberAction): MemberResource
    {
        $memberInvited = $inviteMemberAction->handle($request->toDto(), $business);

        return new MemberResource($memberInvited)->status(Response::HTTP_CREATED);
    }
}
