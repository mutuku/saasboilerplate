<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public function getCurrenciesBySearch(string $searchTerm = '', int $perPage = 10)
    {
        return Currency::where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                          ->orWhere('iso_code', 'like', "%$searchTerm%")
                          ->orWhere('symbol', 'like', "%$searchTerm%");
                })
                ->orderBy('priority', 'asc')
                ->latest()
                ->paginate($perPage);
    }

    public function find(int $id): ?Currency
    {
        return Currency::find($id);
    }

    public function getAllCurrencies()
    {
        return Currency::orderBy('name')->get();
    }

    public function createCurrency(array $data): Currency
    {
        return Currency::create($data);
    }

    public function updateCurrency(int $id, array $data): bool
    {
        $currency = $this->find($id);
        if (!$currency) {
            throw new \Exception('Currency not found');
        }
        return $currency->update($data);
    }

    public function deleteCurrency(Currency $currency): bool
    {
        return $currency->delete();
    }
}
