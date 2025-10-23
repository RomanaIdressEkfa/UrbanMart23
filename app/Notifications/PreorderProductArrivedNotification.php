<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class PreorderProductArrivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $preorder;

    /**
     * Create a new notification instance.
     * Mohammad Hassan
     */
    public function __construct(Order $preorder)
    {
        $this->preorder = $preorder;
    }

    /**
     * Get the notification's delivery channels.
     * Mohammad Hassan
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     * Mohammad Hassan
     */
    public function toMail($notifiable)
    {
        $productNames = $this->preorder->orderDetails->pluck('product.name')->join(', ');
        $remainingAmount = $this->preorder->grand_total - $this->preorder->paid_amount;

        return (new MailMessage)
                    ->subject(translate('Your Pre-ordered Products Have Arrived!'))
                    ->greeting(translate('Hello') . ' ' . $notifiable->name . '!')
                    ->line(translate('Great news! Your pre-ordered products have arrived and are ready for final payment and delivery.'))
                    ->line(translate('Order Code: ') . $this->preorder->code)
                    ->line(translate('Products: ') . $productNames)
                    ->line(translate('Remaining Amount: ') . single_price($remainingAmount))
                    ->line(translate('Please complete your payment to proceed with delivery.'))
                    ->action(translate('Complete Payment'), route('orders.show', $this->preorder->id))
                    ->line(translate('Thank you for shopping with us!'));
    }

    /**
     * Get the array representation of the notification.
     * Mohammad Hassan
     */
    public function toArray($notifiable)
    {
        $productNames = $this->preorder->orderDetails->pluck('product.name')->join(', ');
        $remainingAmount = $this->preorder->grand_total - $this->preorder->paid_amount;

        return [
            'type' => 'preorder_product_arrived',
            'order_id' => $this->preorder->id,
            'order_code' => $this->preorder->code,
            'products' => $productNames,
            'remaining_amount' => $remainingAmount,
            'message' => translate('Your pre-ordered products have arrived! Complete payment to proceed with delivery.'),
            'action_url' => route('orders.show', $this->preorder->id)
        ];
    }
}