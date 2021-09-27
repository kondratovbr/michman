<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            //
        ];
    }

    /** @return $this */
    public function withProject(): static
    {
        return $this->for(Project::factory()->withUserAndServers());
    }
}
