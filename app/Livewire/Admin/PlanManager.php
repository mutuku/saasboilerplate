<?php

namespace App\Livewire\Admin;

use App\Services\PlanService;
use Livewire\Component;
use Livewire\WithPagination;

class PlanManager extends Component
{
    use WithPagination;

    public $planId, $name, $slug, $plan_id, $price, $sale_price, $description, $interval, $order, $archived_at;
    public $searchTerm = '';
    public $isEditMode = false;
    public $confirmingDeletion = false;
    public $planToDelete = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => "required|alpha_dash|unique:plans,slug,$this->planId",
            'plan_id' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'interval' => 'nullable|in:monthly,yearly',
            'order' => 'nullable|integer',
            'archived_at' => 'nullable|date',
        ];
    }

    public function boot(PlanService $planService)
    {
        $this->service = $planService;
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function getPlansProperty()
    {
        return $this->service->getPlansBySearch($this->searchTerm, 10);
    }

    public function edit($id)
    {
        $plan = $this->service->find($id);

        if (!$plan) {
            session()->flash('error', 'Plan not found.');
            return;
        }

        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->plan_id = $plan->plan_id;
        $this->price = $plan->price;
        $this->sale_price = $plan->sale_price;
        $this->description = $plan->description;
        $this->interval = $plan->interval;
        $this->order = $plan->order;
        $this->archived_at = $plan->archived_at;

        $this->isEditMode = true;
        $this->dispatch('switch-to-form-tab');
    }

    public function store()
    {
        $validated = $this->validate();

        try {
            $plan = $this->service->createPlan($validated);
            session()->flash('success', 'Plan created successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create plan: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $validated = $this->validate();

        try {
            $this->service->updatePlan($this->planId, $validated);
            session()->flash('success', 'Plan updated successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update plan: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->planToDelete = $this->service->find($id);
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        try {
            $this->service->deletePlan($this->planToDelete);
            $this->confirmingDeletion = false;
            $this->planToDelete = null;
            session()->flash('success', 'Plan deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete plan: ' . $e->getMessage());
        }
    }

    public function toggleArchive($id)
    {
        if (!auth()->user()->is_super_admin) {
            session()->flash('error', 'Only super admins can archive plans.');
            return;
        }

        $plan = $this->service->find($id);

        if (!$plan) {
            session()->flash('error', 'Plan not found.');
            return;
        }

        $archived = $plan->archived_at !== null;
        $this->service->archivePlan($plan, !$archived);

        session()->flash('success', $archived ? 'Plan unarchived.' : 'Plan archived.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'slug', 'plan_id', 'price', 'sale_price', 'description', 'interval', 'order', 'archived_at', 'planId', 'isEditMode']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.plan-manager');
    }
}
