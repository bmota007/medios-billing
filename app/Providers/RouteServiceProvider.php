use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('login', function (Request $request) {
        $email = (string) $request->input('email');

        return Limit::perMinute(5)->by(
            strtolower($email).'|'.$request->ip()
        );
    });
}
