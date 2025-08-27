<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseCompleted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\Models\Purchase
     */
    public $purchase;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        // 関連を事前ロード
        $purchase->loadMissing(['exhibition.user', 'buyer']);
        $this->purchase = $purchase;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $exhibition = $this->purchase->exhibition;
        $buyer = $this->purchase->buyer;

        return $this
            ->subject('【flema】取引が完了しました（' . $exhibition->name . '）')
            ->markdown('emails.purchase.completed', [
                'purchase' => $this->purchase,
                'exhibition' => $exhibition,
                'buyer' => $buyer,
                'seller' => $exhibition->user,
            ]);
    }
}
