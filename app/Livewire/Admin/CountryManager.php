<?php

namespace App\Livewire\Admin;

use App\Services\CountryService;
use Livewire\Component;
use Livewire\WithPagination;

class CountryManager extends Component
{
    use WithPagination;

    public $countryId, $code, $name;
    public $searchTerm = '';
    public $isEditMode = false;
    public $confirmingDeletion = false;
    public $countryToDelete = null;

    // Protected property to store the injected service
    protected CountryService $countryService;

    protected function rules()
    {
        return [
            'code' => "required|string|max:2|unique:countries,code,$this->countryId",
            'name' => "required|string|max:255",
        ];
    }

    public function boot(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function getCountriesProperty()
    {
        return $this->countryService->getCountriesBySearch($this->searchTerm, 10);
    }

    public function edit($id)
    {
        $country = $this->countryService->find($id);

        if (!$country) {
            session()->flash('error', 'Country not found.');
            return;
        }

        $this->countryId = $country->id;
        $this->code = $country->code;
        $this->name = $country->name;
        $this->isEditMode = true;
        $this->dispatch('switch-to-form-tab');
    }

    public function store()
    {
        $validated = $this->validate();

        try {
            $this->countryService->createCountry($validated);
            session()->flash('success', 'Country created successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create country: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $validated = $this->validate();

        try {
            $this->countryService->updateCountry($this->countryId, $validated);
            session()->flash('success', 'Country updated successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update country: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->countryToDelete = $id; // Store just the ID
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        try {
            $country = $this->countryService->find($this->countryToDelete);
            if ($country) {
                $this->countryService->deleteCountry($country);
                session()->flash('success', 'Country deleted successfully.');
            } else {
                session()->flash('error', 'Country not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete country: ' . $e->getMessage());
        } finally {
            $this->confirmingDeletion = false;
            $this->countryToDelete = null;
        }
    }

    public function resetForm()
    {
        $this->reset(['countryId', 'code', 'name', 'isEditMode']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.country-manager');
    }
}
