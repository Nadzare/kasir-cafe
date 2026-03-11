<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class PosPage extends Component
{
    public $products = [];
    public $cart = [];
    public $filterType = 'all'; // Filter: all, makanan, minuman, dessert
    
    // Form fields
    public $customerName = '';
    public $notes = '';
    public $paymentMethod = 'cash';
    
    // Computed values
    public $totalAmount = 0;
    public $discountAmount = 0;
    public $finalAmount = 0;

    public function mount()
    {
        $this->loadProducts();
    }

    /**
     * Get filtered products based on current filter
     */
    public function getFilteredProductsProperty()
    {
        $query = Product::where('is_active', true)->orderBy('type')->orderBy('name');
        
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }
        
        return $query->get();
    }

    /**
     * Load products from database
     */
    public function loadProducts()
    {
        $this->products = $this->filteredProducts;
    }

    /**
     * Filter products by type
     */
    public function setFilter($type)
    {
        $this->filterType = $type;
    }

    /**
     * Add product to cart
     */
    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan!');
            return;
        }
        
        // Check if product already exists in cart
        $existingIndex = $this->findProductInCart($productId);
        
        if ($existingIndex !== null) {
            // Increase quantity if already exists
            $this->cart[$existingIndex]['quantity']++;
            $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['quantity'] * $this->cart[$existingIndex]['product']['price'];
        } else {
            // Add new item to cart
            $this->cart[] = [
                'product_id' => $product->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'price' => $product->price,
                ],
                'quantity' => 1,
                'subtotal' => $product->price,
            ];
        }
        
        $this->calculateTotal();
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity($index)
    {
        if (isset($this->cart[$index])) {
            $this->cart[$index]['quantity']++;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['product']['price'];
            $this->calculateTotal();
        }
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity($index)
    {
        if (isset($this->cart[$index])) {
            if ($this->cart[$index]['quantity'] > 1) {
                $this->cart[$index]['quantity']--;
                $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['product']['price'];
            } else {
                $this->removeFromCart($index);
            }
            $this->calculateTotal();
        }
    }

    /**
     * Update total
     */
    public function updateTotal()
    {
        foreach ($this->cart as $index => $item) {
            $this->cart[$index]['subtotal'] = $item['quantity'] * $item['product']['price'];
        }
        $this->calculateTotal();
    }

    /**
     * Find product index in cart
     */
    private function findProductInCart($productId)
    {
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $productId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Update quantity of cart item
     */
    public function updateQty($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }
        
        if (isset($this->cart[$index])) {
            $this->cart[$index]['quantity'] = $quantity;
            $this->cart[$index]['subtotal'] = $quantity * $this->cart[$index]['product']['price'];
            $this->calculateTotal();
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Re-index array
            $this->calculateTotal();
        }
    }

    /**
     * Clear all cart
     */
    public function clearCart()
    {
        $this->cart = [];
        $this->customerName = '';
        $this->notes = '';
        $this->calculateTotal();
        session()->flash('message', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Calculate subtotal, discount, and grand total
     * Note: Diskon khusus dapat diterapkan untuk pembelian dalam jumlah besar
     */
    public function calculateTotal()
    {
        $this->totalAmount = 0;
        $this->discountAmount = 0;
        
        foreach ($this->cart as $item) {
            $itemSubtotal = $item['product']['price'] * $item['quantity'];
            $this->totalAmount += $itemSubtotal;
        }
        
        // Bisa ditambahkan logic diskon jika diperlukan
        // Contoh: diskon 10% jika total >= 200.000
        // if ($this->totalAmount >= 200000) {
        //     $this->discountAmount = $this->totalAmount * 0.10;
        // }
        
        $this->finalAmount = $this->totalAmount - $this->discountAmount;
    }

    /**
     * Process checkout (Payment)
     */
    public function checkout()
    {
        // Validation
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong!');
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Generate UUID untuk transaksi
            $uuid = (string) Str::uuid();
            
            // Create transaction record
            $transaction = Transaction::create([
                'uuid' => $uuid,
                'cashier_id' => auth()->id(),
                'total_amount' => $this->totalAmount,
                'discount_amount' => $this->discountAmount,
                'payment_method' => $this->paymentMethod,
                'status' => 'paid',
                'customer_name' => $this->customerName ?: null,
                'notes' => $this->notes ?: null,
            ]);
            
            // Create transaction items
            foreach ($this->cart as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $item['product']['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
            
            DB::commit();
            
            // Clear cart after successful checkout
            $this->cart = [];
            $this->customerName = '';
            $this->notes = '';
            
            // Flash success message
            session()->flash('success', 'Transaksi berhasil! Silakan cetak struk.');
            
            // Redirect to struk page
            return redirect()->route('ticket.print', ['uuid' => $uuid]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            \Log::error('Checkout Error: ' . $e->getMessage());
        }
    }

    /**
     * Get item subtotal
     */
    public function getItemSubtotal($item)
    {
        return $item['price'] * $item['quantity'];
    }

    public function render()
    {
        return view('livewire.pos-page')->layout('layouts.app');
    }
}
