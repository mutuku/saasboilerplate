<?php

namespace App\Services;

use App\Models\Country;

class CountryService
{
    public function getCountriesBySearch(string $searchTerm = '', int $perPage = 10)
    {
        return Country::where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                          ->orWhere('code', 'like', "%$searchTerm%");
                })
                ->latest()
                ->paginate($perPage);
    }

    public function getAllCountries()
    {
        return Country::orderBy('name')->get();
    }

    public function find(int $id): ?Country
    {
        return Country::find($id);
    }

    public function createCountry(array $data): Country
    {
        return Country::create($data);
    }

    public function updateCountry(int $id, array $data): bool
    {
        $country = $this->find($id);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        return $country->update($data);
    }

    public function deleteCountry(Country $country): bool
    {
        return $country->delete();
    }
}
