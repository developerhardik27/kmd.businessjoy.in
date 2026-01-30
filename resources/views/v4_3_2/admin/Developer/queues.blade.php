@php
    $folder = session('folder_name');
@endphp

@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Mail Queue Guide
@endsection

@section('title')
    Mail Queue
@endsection

@section('form-content')
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">
                                <i class="ri-mail-send-line"></i> Laravel Mail Queue
                            </h4>
                            <small>Complete step-by-step setup & usage guide</small>
                        </div>
                        <i class="ri-stack-line fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        @php
            $steps = [
                ['title' => 'Configure Mail (.env)', 'content' => 'Configure your SMTP mail credentials.'],
                ['title' => 'Set Queue Driver', 'content' => 'Enable database queue connection.'],
                ['title' => 'Create Queue Tables', 'content' => 'Generate required queue tables.'],
                ['title' => 'Create Mailable', 'content' => 'Create mail class using Artisan.'],
                ['title' => 'Implement ShouldQueue', 'content' => 'Enable async processing for mail.'],
                ['title' => 'Create Mail View', 'content' => 'Design email Blade template.'],
                ['title' => 'Queue the Mail', 'content' => 'Dispatch mail to queue.'],
                ['title' => 'Run Queue Worker', 'content' => 'Start queue worker locally.'],
                ['title' => 'Handle Failed Jobs', 'content' => 'View and retry failed queue jobs.'],
                ['title' => 'Production Setup (Supervisor)', 'content' => 'Run Queue worker continuously on VPS.'],
            ];
        @endphp

        <div class="row">
            @foreach ($steps as $index => $step)
                <div class="col-md-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <span class="badge bg-primary me-2">Step {{ $index + 1 }}</span>
                                {{ $step['title'] }}
                            </h6>
                        </div>

                        <div class="card-body">
                            <p class="mb-3">{{ $step['content'] }}</p>
                            @if ($index == 0)
                                <pre class="bg-dark text-white p-3 rounded code-block">
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourmail@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourmail@gmail.com
MAIL_FROM_NAME="My App"
</pre>
                            @endif
                            @if ($index == 1)
                                <pre class="bg-dark text-white p-3 rounded code-block">
QUEUE_CONNECTION=database
</pre>
                            @endif
                            @if ($index == 2)
                                <pre class="bg-dark text-white p-3 rounded code-block">
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
</pre>
                            @endif
                            @if ($index == 3)
                                <pre class="bg-dark text-white p-3 rounded code-block">
php artisan make:mail WelcomeMail
</pre>
                            @endif
                            @if ($index == 4)
                                <pre class="bg-dark text-white p-3 rounded code-block">
class ThankYouMail extends Mailable Important ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($request)
    {
        $this->user = $request;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        $subject = 'Thank You' ;

        if(isset($this->user->subscribe)){
            $subject = "Welcome to BusinessJoy" ;
        }
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
</pre>
                            @endif
                            @if ($index == 5)
                                @php
                                    $html = '<h2>Hello User</h2>
<p>Welcome to our platform 🎉</p>';
                                @endphp
                                <pre class="bg-dark text-white p-3 rounded code-block">
resources/views/emails/welcome.blade.php
<hr style="border-top: 1px solid #ffffffcf; margin-top: 0px;">
{{ $html }}
</pre>
                            @endif
                            @if ($index == 6)
                                <pre class="bg-dark text-white p-3 rounded code-block">
Mail::to($user->email)->queue(new WelcomeMail());
</pre>
                            @endif
                            @if ($index == 7)
                                <pre class="bg-dark text-white p-3 rounded code-block">
php artisan queue:work
</pre>
                            @endif
                            @if ($index == 8)
                                <pre class="bg-dark text-white p-3 rounded code-block">
php artisan queue:failed
php artisan queue:retry all
</pre>
                            @endif
                            @if ($index == 9)
                                <div class="mb-4">
                                    <h6 class="fw-semibold text-primary mb-3">
                                        <i class="ri-server-line"></i> Supervisor Setup on VPS
                                    </h6>

                                    <!-- Step 1 -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-primary rounded-pill me-2 mr-1">1</span>
                                            <span class="fw-medium">
                                                Connect to your VPS server (use root or sudo user)
                                            </span>
                                        </div>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
ssh your-user@your-server-ip
            </pre>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-primary rounded-pill me-2 mr-1">2</span>
                                            <span class="fw-medium">Install Supervisor</span>
                                        </div>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
sudo apt update
sudo apt install supervisor -y
            </pre>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-primary rounded-pill me-2 mr-1">3</span>
                                            <span class="fw-medium">
                                                Create Supervisor configuration file for Laravel queue worker
                                            </span>
                                        </div>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
            </pre>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-primary rounded-pill me-2 mr-1">4</span>
                                            <span class="fw-medium">Add the following configuration</span>
                                        </div>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
[program:laravel-worker]
command=php /var/www/your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/your-project/storage/logs/queue.log
            </pre>
                                    </div>

                                    <!-- Step 5 -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <span class="badge bg-primary rounded-pill me-2 mr-1">5</span>
                                            <span class="fw-medium">
                                                Reload Supervisor and start the queue worker
                                            </span>
                                        </div>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
            </pre>
                                    </div>

                                    <!-- Status -->
                                    <div class="mt-4 ps-2 border-start border-3 border-success">
                                        <p class="mb-2 text-success fw-medium">
                                            <i class="ri-check-line"></i> Verify queue worker status
                                        </p>
                                        <pre class="bg-dark text-white p-3 rounded code-block">
sudo supervisorctl status
            </pre>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Queue Flow -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ri-flow-chart text-success"></i> Queue Flow</h6>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded">
Controller → Job Queued → Queue Worker → Mail Sent
</pre>
            </div>
        </div>

        <!-- Notes -->
        <div class="alert alert-warning shadow-sm">
            <h6><i class="ri-alert-line"></i> Important Notes</h6>
            <ul class="mb-0">
                <li>Queue worker must always be running.</li>
                <li>Use Redis for better performance in production.</li>
                <li>Monitor failed jobs regularly.</li>
            </ul>
        </div>

    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function() {
            loaderhide();
        });
    </script>
    <style>
        .code-block {
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            overflow-x: auto;
        }

        pre {
            white-space: pre-wrap;
        }
    </style>
@endpush
