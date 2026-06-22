<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (getSetting('notifications.email_enabled', '1') === '1' && mailIsConfigured() && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'order_placed',
            'title' => 'New order received',
            'message' => 'Order ' . $this->order->order_number . ' has been placed and is waiting for confirmation.',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'order_status' => $this->order->status,
            'action_url' => route('orders.show', $this->order),
            'whatsapp_url' => $this->whatsappUrl(),
        ];
    }

    public function toMail($notifiable)
    {
        $sender = mailIdentity('order');

        return (new MailMessage)
            ->from($sender['address'], $sender['name'])
            ->subject('Order ' . $this->order->order_number . ' received')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('Your order has been received and is now queued for processing.')
            ->line('Order number: ' . $this->order->order_number)
            ->line('Current status: ' . orderStatusLabel($this->order->status))
            ->action('Track Order', route('orders.show', $this->order))
            ->line('We will keep you updated as the order progresses.');
    }

    protected function whatsappUrl()
    {
        $phone = preg_replace('/[^0-9]/', '', getSetting('contact.whatsapp_number', ''));
        $message = urlencode('Hello, my order ' . $this->order->order_number . ' has been placed.');

        if (empty($phone)) {
            return null;
        }

        return 'https://wa.me/' . $phone . '?text=' . $message;
    }
}
