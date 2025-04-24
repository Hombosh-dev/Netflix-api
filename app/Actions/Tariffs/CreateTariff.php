<?php

namespace App\Actions\Tariffs;

use App\DTOs\Tariffs\TariffStoreDTO;
use App\Models\Tariff;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTariff
{
    use AsAction;

    /**
     * Create a new tariff.
     *
     * @param  TariffStoreDTO  $dto
     * @return Tariff
     */
    public function handle(TariffStoreDTO $dto): Tariff
    {
        // Create new tariff
        $tariff = new Tariff();
        $tariff->name = $dto->name;
        $tariff->description = $dto->description;
        $tariff->price = $dto->price;
        $tariff->currency = $dto->currency;
        $tariff->duration_days = $dto->durationDays;
        $tariff->features = $dto->features;
        $tariff->is_active = $dto->isActive;
        $tariff->slug = $dto->slug;
        $tariff->meta_title = $dto->metaTitle ?? $dto->name . ' | ' . config('app.name');
        $tariff->meta_description = $dto->metaDescription ?? $dto->description;
        $tariff->meta_image = $dto->metaImage;
        $tariff->save();
        
        return $tariff;
    }
}
