<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TicketValidation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class GateScanPage extends Component
{
    // Scan result data
    public $scanStatus = null; // 'success', 'error', 'already_used'
    public $scanMessage = '';
    public $transactionData = null;
    public $validationTime = '';
    
    // Camera state
    public $isScanning = true;

    /**
     * Handle QR Code scan from frontend
     * 
     * @param string $uuid
     * @return void
     */
    public function handleScan($uuid)
    {
        try {
            // Cari transaksi berdasarkan UUID
            $transaction = Transaction::with(['items.product', 'cashier'])
                ->where('uuid', $uuid)
                ->where('status', 'paid')
                ->first();
            
            // SKENARIO 1: Tiket Tidak Ditemukan
            if (!$transaction) {
                $this->scanStatus = 'error';
                $this->scanMessage = 'TIKET TIDAK DIKENALI!';
                $this->transactionData = null;
                $this->playSound('error');
                $this->stopScanning();
                return;
            }
            
            // Cek apakah tiket sudah pernah di-scan (validasi)
            $existingValidation = TicketValidation::where('transaction_id', $transaction->id)->first();
            
            // SKENARIO 2: Tiket Sudah Dipakai
            if ($existingValidation) {
                $this->scanStatus = 'already_used';
                $this->scanMessage = 'TIKET SUDAH DIGUNAKAN!';
                $this->validationTime = $existingValidation->scanned_at->format('d/m/Y H:i:s');
                $this->transactionData = [
                    'id' => $transaction->id,
                    'customer_name' => $transaction->customer_name ?: 'Guest',
                    'scanned_by' => $existingValidation->gatekeeper->name ?? 'Unknown',
                ];
                $this->playSound('error');
                $this->stopScanning();
                return;
            }
            
            // SKENARIO 3: Tiket Valid - Lakukan Check-in
            DB::beginTransaction();
            
            try {
                // Create ticket validation record
                TicketValidation::create([
                    'transaction_id' => $transaction->id,
                    'scanned_at' => now(),
                    'scanned_by' => auth()->id(),
                    'status' => 'valid',
                ]);
                
                DB::commit();
                
                // Hitung total pax (jumlah tiket)
                $totalPax = $transaction->items()
                    ->whereHas('product', function($q) {
                        $q->where('type', 'ticket');
                    })
                    ->sum('quantity');
                
                // Set success data
                $this->scanStatus = 'success';
                $this->scanMessage = '✅ SILAKAN MASUK';
                $this->transactionData = [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'customer_name' => $transaction->customer_name ?: 'Guest',
                    'total_pax' => $totalPax,
                    'cashier' => $transaction->cashier->name ?? '-',
                    'transaction_date' => $transaction->created_at->format('d/m/Y H:i'),
                    'total_amount' => $transaction->final_amount,
                ];
                
                $this->playSound('success');
                $this->stopScanning();
                
                // Auto reset after 5 seconds
                $this->dispatch('auto-reset');
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->scanStatus = 'error';
                $this->scanMessage = 'Terjadi kesalahan sistem';
                $this->playSound('error');
                $this->stopScanning();
            }
            
        } catch (\Exception $e) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Error: ' . $e->getMessage();
            $this->playSound('error');
            $this->stopScanning();
        }
    }
    
    /**
     * Reset scanner state
     */
    public function resetScanner()
    {
        $this->scanStatus = null;
        $this->scanMessage = '';
        $this->transactionData = null;
        $this->validationTime = '';
        $this->isScanning = true;
        
        $this->dispatch('restart-scanner');
    }
    
    /**
     * Stop scanning
     */
    private function stopScanning()
    {
        $this->isScanning = false;
        $this->dispatch('stop-scanner');
    }
    
    /**
     * Play sound feedback
     */
    private function playSound($type)
    {
        $this->dispatch('play-sound', type: $type);
    }
    
    /**
     * Get today's statistics
     */
    public function getTodayStatsProperty()
    {
        return [
            'total_scanned' => TicketValidation::whereDate('scanned_at', today())->count(),
            'total_transactions' => Transaction::whereDate('created_at', today())
                ->where('status', 'paid')
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.gate-scan-page')->layout('layouts.app');
    }
}
