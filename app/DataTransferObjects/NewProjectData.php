<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class NewProjectData extends DataTransferObject
{
    static function fromRequest($request): self
    {
        $validated = $request->validated();

        return new self([
            //
        ]);
    }
}
