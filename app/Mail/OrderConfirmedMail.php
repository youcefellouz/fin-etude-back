<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order]);

        return $this->subject('Confirmation de votre commande #' . $this->order->id)
                    ->view('emails.order-confirmed')
                    ->with(['order' => $this->order])
                    ->attachData(
                        $pdf->output(),
                        'facture-' . $this->order->id . '.pdf',
                        ['mime' => 'application/pdf']
                    );
    }
}