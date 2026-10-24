<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Services\ContactCaptcha;
use App\Services\PortfolioContent;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendContactNotification;

class HomeController extends Controller
{
    public function __construct(private PortfolioContent $portfolioContent)
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::orderBy('id')->first();

        return view('fields.home', [
            'user' => $user,
            'portfolio' => $this->portfolioContent->all(),
        ]);
    }

    public function experience()
    {
        return view('fields.experience', ['portfolio' => $this->portfolioContent->all()]);
    }

    public function education()
    {
        return view('fields.education', ['portfolio' => $this->portfolioContent->all()]);
    }

    public function projects()
    {
        return view('fields.projects', ['portfolio' => $this->portfolioContent->all()]);
    }

    public function skills()
    {
        return view('fields.skills', ['portfolio' => $this->portfolioContent->all()]);
    }

    public function captcha(Request $request, ContactCaptcha $captcha)
    {
        return response($captcha->image($request), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, private, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function message(Request $request, ContactCaptcha $captcha)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'captcha' => ['required', 'string', 'size:5'],
        ]);

        if (!$captcha->verify($request, $data['captcha'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'captcha' => 'The verification code is incorrect or expired. Enter the new code below.',
            ]);
        }

        try {
            DB::transaction(function () use ($data) {
                $message = Message::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'description' => $data['message'],
                    'status' => 1,
                ]);
                SendContactNotification::dispatch($message->id)
                    ->onConnection(config('portfolio_notifications.contact_queue_connection'));
            });
        } catch (QueryException $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'msg' => 'Your message could not be saved. Please try again later.',
            ], 503);
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'Thank you! Your message has been received.',
        ], 201);
    }
}
