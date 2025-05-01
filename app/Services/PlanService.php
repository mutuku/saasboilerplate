<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    public function getPlansBySearch(string $searchTerm = '', int $perPage = 10)
    {
        return Plan::where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                          ->orWhere('description', 'like', "%$searchTerm%")
                          ->orWhere('slug', 'like', "%$searchTerm%");
                })
                ->latest()
                ->paginate($perPage);
    }

    public function find(int $id): ?Plan
    {
        return Plan::withTrashed()->find($id);
    }

    public function createPlan(array $data): Plan
    {
        return Plan::create($data);
    }

    public function updatePlan(int $id, array $data): bool
    {
        $plan = $this->find($id);
        return $plan->update($data);
    }

    public function deletePlan(Plan $plan): bool
    {
        return $plan->delete();
    }

    public function archivePlan(Plan $plan, bool $shouldArchive): bool
    {
        return $plan->update(['archived_at' => $shouldArchive ? now() : null]);
    }
}
