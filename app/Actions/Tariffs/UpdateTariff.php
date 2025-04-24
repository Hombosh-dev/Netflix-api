<?php

namespace App\Actions\Tariffs;

use App\DTOs\Tariffs\TariffUpdateDTO;
use App\Models\Tariff;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTariff
{
    use AsAction;

    /**
     * Update an existing tariff.
     *
     * @param  Tariff  $tariff
     * @param  TariffUpdateDTO  $dto
     * @return Tariff
     */
    public function handle(Tariff $tariff, TariffUpdateDTO $dto): Tariff
    {
        // Update the tariff
        if ($dto->name !== null) {
            $tariff->name = $dto->name;
        }
        
        if ($dto->description !== null) {
            $tariff->description = $dto->description;
        }
        
        if ($dto->price !== null) {
            $tariff->price = $dto->price;
        }
        
        if ($dto->currency !== null) {
            $tariff->currency = $dto->currency;
        }
        
        if ($dto->durationDays !== null) {
            $tariff->duration_days = $dto->durationDays;
        }
        
        if ($dto->features !== null) {
            $tariff->features = $dto->features;
        }
        
        if ($dto->isActive !== null) {
            $tariff->is_active = $dto->isActive;
        }
        
        if ($dto->slug !== null) {
            $tariff->slug = $dto->slug;
        }
        
        if ($dto->metaTitle !== null) {
            $tariff->meta_title = $dto->metaTitle;
        }
        
        if ($dto->metaDescription !== null) {
            $tariff->meta_description = $dto->metaDescription;
        }
        
        if ($dto->metaImage !== null) {
            $tariff->meta_image = $dto->metaImage;
        }
        
        $tariff->save();
        
        return $tariff->load('userSubscriptions');
    }
}
