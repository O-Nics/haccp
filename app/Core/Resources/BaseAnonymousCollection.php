<?php

declare(strict_types=1);

namespace App\Core\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class BaseAnonymousCollection extends AnonymousResourceCollection
{
    protected ?int $status = Response::HTTP_OK;

    protected ?string $message = null;

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->status);
    }

    public function with($request): array
    {
        return $this->message
            ? ['message' => $this->message]
            : [];
    }


    public function status(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
