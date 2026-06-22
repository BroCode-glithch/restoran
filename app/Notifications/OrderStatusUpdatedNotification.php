<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $note;

    public function __construct(Order $order, $note = null)
    {
        $this->order = $order;
        $this->note = $note;
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
            'type' => 'order_status_updated',
            'title' => 'Order status updated',
            'message' => 'Order ' . $this->order->order_number . ' is now ' . orderStatusLabel($this->order->status) . '.',
            'note' => $this->note,
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
        $mail = (new MailMessage)
            ->from($sender['address'], $sender['name'])
            ->subject('Order ' . $this->order->order_number . ' updated')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('Your order status is now ' . orderStatusLabel($this->order->status) . '.')
            ->action('Track Order', route('orders.show', $this->order));

        if ($this->note) {
            $mail->line($this->note);
        }

        return $mail;
    }

    protected function whatsappUrl()
    {
        $phone = preg_replace('/[^0-9]/', '', getSetting('contact.whatsapp_number', ''));
        $message = urlencode('Hello, my order ' . $this->order->order_number . ' is now ' . orderStatusLabel($this->order->status) . '.');

        if (empty($phone)) {
            return null;
        }

        return 'https://wa.me/' . $phone . '?text=' . $message;
    }
}
