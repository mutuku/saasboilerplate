<?php

namespace App\Livewire\Admin;

use App\Services\CurrencyService;
use Livewire\Component;
use Livewire\WithPagination;

class CurrencyManager extends Component
{
    use WithPagination;

    public $currencyId;
    public $priority;
    public $iso_code;
    public $name;
    public $symbol;
    public $subunit;
    public $subunit_to_unit;
    public $symbol_first;
    public $html_entity;
    public $decimal_mark;
    public $thousands_separator;
    public $iso_numeric;

    public $searchTerm = '';
    public $isEditMode = false;
    public $confirmingDeletion = false;
    public $currencyToDelete = null;

    // Protected property to store the injected service
    protected CurrencyService $currencyService;

    protected function rules()
    {
        return [
            'priority' => 'nullable|integer',
            'iso_code' => "required|string|max:3|unique:currencies,iso_code,$this->currencyId",
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'subunit' => 'nullable|string|max:255',
            'subunit_to_unit' => 'nullable|integer',
            'symbol_first' => 'nullable|string|max:1',
            'html_entity' => 'nullable|string|max:255',
            'decimal_mark' => 'nullable|string|max:10',
            'thousands_separator' => 'nullable|string|max:10',
            'iso_numeric' => 'nullable|integer'
        ];
    }

    public function boot(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function getCurrenciesProperty()
    {
        return $this->currencyService->getCurrenciesBySearch($this->searchTerm, 10);
    }

    public function edit($id)
    {
        $currency = $this->currencyService->find($id);

        if (!$currency) {
            session()->flash('error', 'Currency not found.');
            return;
        }

        $this->currencyId = $currency->id;
        $this->priority = $currency->priority;
        $this->iso_code = $currency->iso_code;
        $this->name = $currency->name;
        $this->symbol = $currency->symbol;
        $this->subunit = $currency->subunit;
        $this->subunit_to_unit = $currency->subunit_to_unit;
        $this->symbol_first = $currency->symbol_first;
        $this->html_entity = $currency->html_entity;
        $this->decimal_mark = $currency->decimal_mark;
        $this->thousands_separator = $currency->thousands_separator;
        $this->iso_numeric = $currency->iso_numeric;

        $this->isEditMode = true;
        $this->dispatch('switch-to-form-tab');
    }

    public function store()
    {
        $validated = $this->validate();

        try {
            $this->currencyService->createCurrency($validated);
            session()->flash('success', 'Currency created successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create currency: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $validated = $this->validate();

        try {
            $this->currencyService->updateCurrency($this->currencyId, $validated);
            session()->flash('success', 'Currency updated successfully.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update currency: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->currencyToDelete = $id; // Store just the ID
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        try {
            $currency = $this->currencyService->find($this->currencyToDelete);
            if ($currency) {
                $this->currencyService->deleteCurrency($currency);
                session()->flash('success', 'Currency deleted successfully.');
            } else {
                session()->flash('error', 'Currency not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete currency: ' . $e->getMessage());
        } finally {
            $this->confirmingDeletion = false;
            $this->currencyToDelete = null;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'currencyId',
            'priority',
            'iso_code',
            'name',
            'symbol',
            'subunit',
            'subunit_to_unit',
            'symbol_first',
            'html_entity',
            'decimal_mark',
            'thousands_separator',
            'iso_numeric',
            'isEditMode'
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.currency-manager');
    }
}
