<?php

namespace App\Application\Http\Controllers\Backend;

use App\Application\Events\LoginAttempted;
use App\Application\Http\Controllers\Controller;
use App\Application\Http\Requests\Backend\Auth\SignInRequest;
use App\Core\Layout\Flasher;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\StatefulGuard as StatefulGuardContract;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcherContract;

class AuthorizationController extends Controller
{

    // Route to which users are redirected after being signed in.
    private const AFTER_SIGN_IN_ROUTE = 'backend.dashboard.index';

    /**
     * @var EventsDispatcherContract
     */
    private $eventsDispatcher;

    /**
     * @var StatefulGuardContract
     */
    private $authGuard;

    /**
     * @var Flasher
     */
    private $flasher;

    /**
     * @param EventsDispatcherContract $eventsDispatcher
     * @param AuthManager $authManager
     * @param Flasher $flasher
     */
    public function __construct(
        EventsDispatcherContract $eventsDispatcher,
        AuthManager $authManager,
        Flasher $flasher
    ) {
        $this->eventsDispatcher = $eventsDispatcher;
        $this->authGuard = $authManager->guard();
        $this->flasher = $flasher;
    }

    /**
     * @return mixed
     */
    public function in()
    {
        if ($this->authGuard->check()) {
            return redirect()->route(self::AFTER_SIGN_IN_ROUTE);
        }

        return view('backend.views.auth.in');
    }

    /**
     * @param SignInRequest $request
     * @return mixed
     */
    public function doIn(SignInRequest $request)
    {
        // If user is already signed in, redirect him / her to appropriate route
        if ($this->authGuard->check()) {
            return redirect()->route(self::AFTER_SIGN_IN_ROUTE);
        }

        // Perform the actual signing-in
        $loginSucceeded = $this->authGuard->attempt([
            'login' => $request->get('login'),
            'password' => $request->get('password'),
        ]);

        // Log this attempt
        $this->eventsDispatcher->dispatch(
            new LoginAttempted([
                'login' => $request->get('login'),
                'successful' => $loginSucceeded,
            ])
        );

        // If login was successful, redirect user to appropriate route
        if ($loginSucceeded) {
            return redirect()->route(self::AFTER_SIGN_IN_ROUTE);
        } else {
            $this->flasher->flashError('No such account exists.');

            return redirect()->route('backend.auth.do-in');
        }
    }

    /**
     * @return mixed
     */
    public function out()
    {
        $this->authGuard->logout();
        $this->flasher->flashSuccess('You have been signed out.');

        return redirect('/');
    }

}