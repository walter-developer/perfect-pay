<?php

namespace App\Enumerations;


enum EnumChargeStatus: int
{

    case GENERATED = 0;
    case SENT_AND_RECEIVED_ASAAS  = 1;
    case PENDING = 2;
    case RECEIVED = 3;
    case CONFIRMED = 4;
    case OVERDUE = 5;
    case REFUNDED = 6;
    case RECEIVED_IN_CASH = 7;
    case REFUND_REQUESTED = 8;
    case REFUND_IN_PROGRESS = 9;
    case CHARGEBACK_REQUESTED = 10;
    case CHARGEBACK_DISPUTE = 11;
    case AWAITING_CHARGEBACK_REVERSAL = 12;
    case DUNNING_REQUESTED = 13;
    case DUNNING_RECEIVED = 14;
    case AWAITING_RISK_ANALYSIS = 15;


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
            static::GENERATED => 'GENERATED',
            static::SENT_AND_RECEIVED_ASAAS => 'SENT_AND_RECEIVED_ASAAS',
            static::PENDING => 'PENDING',
            static::RECEIVED => 'RECEIVED',
            static::CONFIRMED => 'CONFIRMED',
            static::OVERDUE => 'OVERDUE',
            static::REFUNDED => 'REFUNDED',
            static::RECEIVED_IN_CASH => 'RECEIVED_IN_CASH',
            static::REFUND_REQUESTED => 'REFUND_REQUESTED',
            static::REFUND_IN_PROGRESS => 'REFUND_IN_PROGRESS',
            static::CHARGEBACK_REQUESTED => 'CHARGEBACK_REQUESTED',
            static::CHARGEBACK_DISPUTE => 'CHARGEBACK_DISPUTE',
            static::AWAITING_CHARGEBACK_REVERSAL => 'AWAITING_CHARGEBACK_REVERSAL',
            static::DUNNING_REQUESTED => 'DUNNING_REQUESTED',
            static::DUNNING_RECEIVED => 'DUNNING_RECEIVED',
            static::AWAITING_RISK_ANALYSIS => 'AWAITING_RISK_ANALYSIS',
            default => 'UNDEFINED'
        };
    }

    public function description(): string
    {
        return match ($this) {
            static::GENERATED => 'Registro gerado, porém não enviado a Asaas',
            static::SENT_AND_RECEIVED_ASAAS => 'Registro gerado enviado e retornado o relacionamento com registor da Asaas',
            static::PENDING => 'Aguardando pagamento',
            static::RECEIVED => 'Recebida (saldo já creditado na conta)',
            static::CONFIRMED => 'Pagamento confirmado (saldo ainda não creditado)',
            static::OVERDUE => 'Vencida',
            static::REFUNDED => 'Estornada',
            static::RECEIVED_IN_CASH => 'Recebida em dinheiro (não gera saldo na conta)',
            static::REFUND_REQUESTED => 'Estorno Solicitado',
            static::REFUND_IN_PROGRESS => 'Estorno em processamento (liquidação já está agendada, cobrança será estornada após executar a liquidação)',
            static::CHARGEBACK_REQUESTED => 'Recebido chargeback',
            static::CHARGEBACK_DISPUTE => 'Em disputa de chargeback (caso sejam apresentados documentos para contestação)',
            static::AWAITING_CHARGEBACK_REVERSAL => 'Disputa vencida, aguardando repasse da adquirente',
            static::DUNNING_REQUESTED => 'Em processo de negativação',
            static::DUNNING_RECEIVED => 'Recuperada',
            static::AWAITING_RISK_ANALYSIS => 'Pagamento em análise',
            default => 'UNDEFINED'
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
