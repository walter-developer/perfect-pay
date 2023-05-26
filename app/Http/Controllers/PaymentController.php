<?php

namespace App\Http\Controllers;

use App\Models\{
    Person as ModelPerson,
    AsaasClientCharge as ModelAsaasClientCharge
};

use App\Http\Requests\Web\{
    FormPaymentTikect,
    FormPaymentPix,
    FormPaymentCard,
    FormPaymentSuccess
};
use App\Integrations\Asaas\{
    Clients as AsaasClients,
    Charges as AsaasCharges
};

use App\Enumerations\EnumChargeType;
use App\Support\Collection\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentController extends Controller
{
    private AsaasClients $asaasClients;
    private AsaasCharges $asaasCharges;
    private ModelPerson $modelPerson;
    private ModelAsaasClientCharge $modelAsaasClientCharge;

    public function __construct(ModelPerson $modelPerson, ModelAsaasClientCharge $modelAsaasClientCharge, AsaasClients $asaasClients, AsaasCharges $asaasCharges)
    {
        $this->asaasClients = $asaasClients;
        $this->asaasCharges = $asaasCharges;
        $this->modelPerson = $modelPerson;
        $this->modelAsaasClientCharge = $modelAsaasClientCharge;
    }

    public function viewPayment()
    {
        return view('payment');
    }

    public function viewPaymentSucess(FormPaymentSuccess $request)
    {
        $charge = $this->modelAsaasClientCharge?->find($request->valid()->get('payment'));
        $chargeAsaas = $this->asaasCharges->listCharge($charge->id);
        if ($charge->charge_type == EnumChargeType::PIX->value()) {
            $charge->pix =  $this->asaasCharges->listQrcodePix($charge->id)?->toArray();
        }
        $chargeAsaas = $chargeAsaas->merge($charge?->toArray());
        return view('payment-success', ['payment' => $chargeAsaas?->toArray()]);
    }

    public function paymentTikect(FormPaymentTikect $request)
    {
        $collection = $request->valid();
        $person = $this->newPerson($collection);
        $newAsaasClient = $this->newAsaasClient($person);
        $value = $collection->get('value', 0);
        $dueDate =  $collection->get('due_date', date('Y-m-d'));
        $description = $collection->get('description', 'cobrança perfectpay');
        $newAsaasCharge = $this->newAsaasCharge($newAsaasClient, $value, $dueDate, $description, EnumChargeType::BOLETO);
        return redirect(route('view.payment.success', ['payment' => $newAsaasCharge->get('id')]));
    }

    public function paymentPix(FormPaymentPix $request)
    {
        $collection = $request->valid();
        $person = $this->newPerson($collection);
        $newAsaasClient = $this->newAsaasClient($person);
        $value = $collection->get('value', 0);
        $dueDate =  $collection->get('due_date', date('Y-m-d'));
        $description = $collection->get('description', 'cobrança perfectpay');
        $newAsaasCharge = $this->newAsaasCharge($newAsaasClient, $value, $dueDate, $description, EnumChargeType::PIX);
        return redirect(route('view.payment.success', ['payment' => $newAsaasCharge->get('id')]));
    }

    public function paymentCard(FormPaymentCard $request)
    {
        $collection = $request->valid();
        $person = $this->newPerson($collection);
        $newAsaasClient = $this->newAsaasClient($person);
        $value = $collection->get('value', 0);
        $dueDate =  $collection->get('due_date', date('Y-m-d'));
        $description = $collection->get('description', 'cobrança perfectpay');

        $dateCardExpiration = $collection->get('payment.card.expiration');
        $dateCardExpirationArray =  preg_split("/\/|\-/", $dateCardExpiration);
        $dateCardExpirationMonth = array_shift($dateCardExpirationArray);
        $dateCardExpirationYear = end($dateCardExpirationArray);

        $newAsaasClient->set('card.holder_name', $collection->get('payment.card.name'));
        $newAsaasClient->set('card.number', $collection->get('payment.card.number'));
        $newAsaasClient->set('card.expiry_month', $dateCardExpirationMonth);
        $newAsaasClient->set('card.expiry_year', $dateCardExpirationYear);
        $newAsaasClient->set('card.ccv', $collection->get('payment.card.ccv'));

        $newAsaasCharge = $this->newAsaasCharge($newAsaasClient, $value, $dueDate, $description, EnumChargeType::CREDIT_CARD);
        return redirect(route('view.payment.success', ['payment' => $newAsaasCharge->get('id')]));
    }

    private function newAsaasCharge(Collection $newClientAsaas, float $value, string $dueDate, string $description, EnumChargeType $chargeType = EnumChargeType::BOLETO): Collection
    {
        $newCharge = new Collection($newClientAsaas?->toArray());
        $newCharge->set('type', $chargeType->value());
        $newCharge->set('value', $value);
        $newCharge->set('due_date', $dueDate);
        $newCharge->set('description', $description);
        $newChargeAsaas = $this->asaasCharges->createCharge($newCharge);
        if (empty($newChargeAsaas?->count())) {
            return throw new HttpResponseException(redirect()->back()->withInput()
                ->withErrors($this->asaasCharges->errors())
                ->setStatusCode(303, 'Falha ao cadastrar cobrança.'));
        }
        return $newChargeAsaas;
    }

    private function newAsaasClient(ModelPerson $person): Collection
    {
        $newClient = new Collection($person?->toArray());
        $newClient->copy('id', 'externalReference');
        $newClient->copy('document', 'cpfCnpj');
        $newClient->copy('phone', 'phone');
        $newClient->copy('cell_phone', 'mobilePhone');
        $newClient->copy('address.address.cep', 'postalCode');
        $newClient->copy('address.address.address', 'address');
        $newClient->copy('address.number', 'addressNumber');
        $newClient->copy('address.observation', 'complement');
        $newClient->copy('email', 'additionalEmails');
        $newClient->copy('document', 'observations');
        $newClient->copy('document', 'groupName');
        $newClient->copy('company.municipal_registration', 'municipalInscription', '');
        $newClient->copy('company.state_registration', 'stateInscription', '');
        $newClient->set('notificationDisabled', false);
        $newClientAsaas = $this->asaasClients->createAsaasClient($newClient);

        if (empty($newClientAsaas?->count())) {
            return throw new HttpResponseException(redirect()->back()->withInput()
                ->withErrors($this->asaasCharges->errors())
                ->setStatusCode(303, 'Falha ao cadastrar cliente ao meio de pagamento.'));
        }

        return $newClientAsaas;
    }

    private function newPerson(Collection $person): ModelPerson|null
    {
        $newPerson = $this->modelPerson->updateOrCreate(
            [
                'document' => $person->get('document'),
            ],
            [
                'name' => $person->get('name'),
                'document' => $person->get('document'),
                'birth_date' => $person->get('birth_date'),
                'phone' => $person->get('phone'),
                'cell_phone' => $person->get('cell_phone'),
                'email' => $person->get('email'),
            ]
        );

        $adresses = $newPerson
            ->adresses()
            ->newModelInstance()
            ->address()
            ->newModelInstance()
            ->where('cep', $person->get('cep'))->first();

        $filterAddress = [
            'id_people' => $newPerson?->id,
            'id_adresses' => $adresses?->id
        ];

        $newAddress = [
            'id_people' => $newPerson?->id,
            'id_adresses' => $adresses?->id,
            'number' =>  $person->get('number'),
            'observation' =>  $person->get('observation')
        ];

        $newPersonAddress = $newPerson->adresses()
            ->newModelInstance($newAddress)
            ->updateOrCreate($filterAddress, $newAddress);

        $newPerson->update(['id_people_adresses' => $newPersonAddress?->id]);

        $newPerson?->load(['physical', 'company', 'address.address', 'adresses']);
        return $newPerson;
    }
}
