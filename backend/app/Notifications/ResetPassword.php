use Illuminate\Notifications\Messages\MailMessage;

public function toMail($notifiable)
{
    $url = URL::temporarySignedRoute(
        'password.reset', now()->addMinutes(60), ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]
    );

    return (new MailMessage)
        ->subject('Paroles atiestatīšanas paziņojums')
        ->line('Jūs saņemat šo e-pastu, jo mēs saņēmām pieprasījumu atiestatīt Jūsu konta paroli.')
        ->line('Šī saite ir derīga tikai vienreizējai lietošanai. Lūdzu, atiestatiet savu paroli pēc iespējas ātrāk.')
        ->action('Atiestatīt paroli', $url)
        ->line('Ja Jūs nepieprasījāt paroles atiestatīšanu, nekādas turpmākas darbības nav nepieciešamas.');
} 