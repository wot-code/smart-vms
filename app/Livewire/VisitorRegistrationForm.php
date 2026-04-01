<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use AfricasTalking\SDK\AfricasTalking;

class VisitorRegistrationForm extends Component
{
    public $full_name;
    public $phone;
    public $company;
    public $id_number;
    public $type = 'Adult';
    public $is_accompanied = false;
    public $purpose = '';
    public $purpose_other;
    public $host_id = '';
    public $vehicle_reg;
    public $time_in;
    public $signature;
    public $date;

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function submit()
    {
        // 1. Validation
        $this->validate([
            'full_name'     => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'id_number'     => 'required|string|max:50',
            'type'          => 'required|in:Adult,Minor',
            'purpose'       => 'required|string',
            'purpose_other' => 'required_if:purpose,Other',
            'host_id'       => 'required|exists:users,id',
            'time_in'       => 'required',
            'signature'     => 'required|string',
        ]);

        // 2. Look up host
        $host = User::findOrFail($this->host_id);

        // 3. Process Signature — store as base64 data URL directly
        //    (so visitor/show.blade.php can display it as an <img src="">)
        $signatureValue = $this->signature;

        // 4. Create Visitor Record (all columns match fillable in Visitor model)
        $visitor = Visitor::create([
            'full_name'      => $this->full_name,
            'phone'          => $this->formatPhone($this->phone),
            'company'        => $this->company,
            'type'           => $this->type,
            'id_number'      => $this->id_number,  // Fixed: was id_passport
            'is_accompanied' => $this->is_accompanied,
            'purpose'        => $this->purpose === 'Other' ? $this->purpose_other : $this->purpose,
            'host_id'        => $this->host_id,
            'host_name'      => $host->name,
            'date'           => $this->date,
            'time_in'        => $this->time_in,
            'vehicle_reg'    => strtoupper($this->vehicle_reg ?? ''),
            'signature'      => $signatureValue,  // Fixed: was signature_path
            'status'         => 'pending',
        ]);

        // 5. 🔔 NOTIFY HOST — Send SMS to host immediately
        $this->notifyHost($visitor, $host);

        // 6. Redirect to visitor pass
        return redirect()->to('/visitor/success/' . $visitor->id)
            ->with('status', 'Check-in successful!');
    }

    /**
     * Send SMS notification to the host about the new visitor.
     */
    protected function notifyHost(Visitor $visitor, User $host)
    {
        $username = env('AT_USERNAME');
        $apiKey   = env('AT_API_KEY');

        // Skip if API credentials are missing
        if (!$username || !$apiKey) {
            Log::warning("SMS: Africa's Talking credentials not configured in .env");
            return;
        }

        // Skip if host has no real phone number
        $phone = $this->formatPhone($host->phone ?? '');
        if (!$host->phone || $phone === '+254000000000') {
            Log::info("SMS: Host '{$host->name}' has no phone number configured. SMS skipped.");
            return;
        }

        $message = "VMS ALERT: {$visitor->full_name} is at the gate to see you. "
                 . "Log in to approve or reject: " . url('/login');

        try {
            $at = new AfricasTalking($username, $apiKey);
            $response = $at->sms()->send([
                'to'      => $phone,
                'message' => $message,
            ]);
            Log::info("SMS sent to host {$host->name} ({$phone}): " . json_encode($response));
        } catch (\Exception $e) {
            Log::error("SMS Error (notifyHost): " . $e->getMessage());
        }
    }

    /**
     * Normalize phone number to +254 format.
     */
    private function formatPhone($phone)
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone ?? '');
        if (empty($cleaned)) return '+254000000000';
        if (str_starts_with($cleaned, '07') || str_starts_with($cleaned, '01')) {
            return '+254' . substr($cleaned, 1);
        }
        if (str_starts_with($cleaned, '254') && strlen($cleaned) === 12) {
            return '+' . $cleaned;
        }
        if (str_starts_with($phone, '+254')) {
            return $phone;
        }
        return '+254' . $cleaned;
    }

    public function render()
    {
        $hosts = User::whereIn('role', ['host', 'Host'])
                     ->orderBy('name')
                     ->get();

        return view('livewire.visitor-registration-form', [
            'hosts' => $hosts
        ]);
    }
}