<?php

namespace App\Enumerations;


enum EnumChargeType: int
{

    case UNDEFINED = 0;
    case BOLETO = 1;
    case CREDIT_CARD = 2;
    case PIX = 3;


    public function name(): string
    {
        return $this->name;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function names(): array
    {
        $assoc = [];
        $list = self::cases();
        array_walk($list, function ($case) use (&$assoc) {
            $assoc[$case->value] = $case->name;
        });
        return $assoc;
    }

    public function values(): array
    {
        $assoc = [];
        $list = self::cases();
        array_walk($list, function ($case) use (&$assoc) {
            $assoc[$case->name] = $case->value;
        });
        return $assoc;
    }

    public function label(): string
    {
        return match ($this) {
            static::UNDEFINED => 'UNDEFINED',
            static::BOLETO => 'BOLETO',
            static::CREDIT_CARD => 'CREDIT_CARD',
            static::PIX => 'PIX',
            default => 'UNDEFINED'
        };
    }

    public function description(): string
    {
        return match ($this) {
            static::UNDEFINED => 'Não definido',
            static::BOLETO => 'Boleto',
            static::CREDIT_CARD => 'Cartão de crédito',
            static::PIX => 'Tranferência PIX',
            default => 'Não definido.'
        };
    }

    public static function enum(int|string|null $value): static|null
    {
        $list = self::cases();
        $enums = array_filter($list, function ($case) use ($value) {
            return (is_numeric($value) && $case->value == $value) || (empty(is_numeric($value)) && $case->label() == $value);
        });
        return current($enums) ?: null;
    }

    public static function options(): array
    {
        $assoc = [];
        $list = self::cases();
        array_walk($list, function ($case) use (&$assoc) {
            $assoc[] = ['id' => $case->value, 'description' => $case->label()];
        });
        return $assoc;
    }

    public static function assoc(): array
    {
        $assoc = [];
        $list = self::cases();
        array_walk($list, function ($case) use (&$assoc) {
            $assoc[$case->value] = $case->label();
        });
        return $assoc;
    }
}
