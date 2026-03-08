<?php

declare(strict_types=1);

use App\Core\Actions\BaseAction;
use App\Core\Controllers\BaseController;
use App\Core\Repositories\BaseRepository;
use App\Core\Requests\BaseFormRequest;
use App\Core\Resources\BaseJsonResource;

pest()->group('architecture');

// --- ACTIONS ---

arch('actions are final readonly and extend BaseAction')
    ->expect('Domain\Auth\Actions')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseAction::class);

arch('business actions are final readonly and extend BaseAction')
    ->expect('Domain\Business\Actions')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseAction::class);

arch('product actions are final readonly and extend BaseAction')
    ->expect('Domain\Product\Actions')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseAction::class);

// --- REPOSITORIES ---

arch('auth repositories are final readonly and extend BaseRepository')
    ->expect('Infrastructure\Auth\Repositories')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseRepository::class);

arch('business repositories are final readonly and extend BaseRepository')
    ->expect('Infrastructure\Business\Repositories')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseRepository::class);

arch('product repositories are final readonly and extend BaseRepository')
    ->expect('Infrastructure\Product\Repositories')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseRepository::class);

arch('notification repositories are final readonly and extend BaseRepository')
    ->expect('Infrastructure\Notification\Repositories')
    ->toBeFinal()
    ->toBeReadonly()
    ->toExtend(BaseRepository::class);

// --- CONTROLLERS ---

arch('business controllers are final and extend BaseController')
    ->expect('App\Modules\Business\Controllers')
    ->toBeFinal()
    ->toExtend(BaseController::class);

arch('product controllers are final and extend BaseController')
    ->expect('App\Modules\Product\Controllers')
    ->toBeFinal()
    ->toExtend(BaseController::class);

// --- REQUESTS ---

arch('business requests are final and extend BaseFormRequest')
    ->expect('App\Modules\Business\Requests')
    ->toBeFinal()
    ->toExtend(BaseFormRequest::class);

arch('product requests are final and extend BaseFormRequest')
    ->expect('App\Modules\Product\Requests')
    ->toBeFinal()
    ->toExtend(BaseFormRequest::class);

// --- RESOURCES ---

arch('business resources extend BaseJsonResource')
    ->expect('App\Modules\Business\Resources')
    ->toExtend(BaseJsonResource::class);

arch('product resources extend BaseJsonResource')
    ->expect('App\Modules\Product\Resources')
    ->toExtend(BaseJsonResource::class);
