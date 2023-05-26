<?php

namespace App\Models;

use App\Models\{
    Model,
    AsaasClient
};
use App\Enumerations\{
    EnumChargeType,
    EnumChargeStatus
};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AsaasClientCharge extends Model
{
    protected $table = 'asaas_clients_charges';

    protected $columns = [
        'id',
        'id_charge_asaas',
        'id_client_asaas',
        'due_date',
        'value',
        'description',
        'charge_type',
        'charge_status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_charge_asaas',
        'id_client_asaas',
        'charge_type',
        'charge_status',
        'due_date',
        'value',
        'description',
    ];

    protected $appends = [
        'charge_type_label',
        'charge_type_description',
    ];

    public function asaasClient()
    {
        return $this->belongsTo(AsaasClient::class, 'id_client_asaas', 'id');
    }

    public function setDueDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return  $this->setAttributeRaw('due_date', $date->format('Y-m-d'));
    }

    protected function chargeType(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRaw('charge_type', $type))?->value(),
            set: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_type'))?->value()
        );
    }

    protected function chargeTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRaw('charge_type', $type))?->label(),
            set: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_type'))?->value()
        );
    }

    protected function chargeTypeDescription(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRaw('charge_type', $type))?->description(),
            set: fn (EnumChargeType|int|null $type) => $type instanceof EnumChargeType ?
                $type?->value() : EnumChargeType::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_type'))?->value()
        );
    }

    protected function chargeStatus(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRaw('charge_status', $type))?->value(),
            set: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_status'))?->value()
        );
    }

    protected function chargeStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRaw('charge_status', $type))?->label(),
            set: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_status'))?->value()
        );
    }

    protected function chargeStatusDescription(): Attribute
    {
        return Attribute::make(
            get: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRaw('charge_status', $type))?->description(),
            set: fn (EnumChargeStatus|int|null $type) => $type instanceof EnumChargeStatus ?
                $type?->value() : EnumChargeStatus::enum($this->getAttributeRawCaseValueEmpty($type, 'charge_status'))?->value()
        );
    }
}
