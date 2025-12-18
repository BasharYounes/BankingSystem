<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Exceptions\InvalidCodeException;
use App\Http\Requests\EmailAndCodeRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepository;
use App\Services\CasheService;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\GenerateCode;



class AuthController extends Controller
{

    public function __construct(
        protected AuthService $authService,
        protected GenerateCode $codeService,
        protected UserRepository $userRepository,
        protected CasheService $casheService,
    ) {}

    /**
     * Register new user and send verification code
     *
     * @param RegisterRequest $registerRequest
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function RegisterUser(RegisterRequest $registerRequest)
    {
        $user = $this->authService->registerUser($registerRequest);

        $code = $this->codeService->generateCode($user);

        event(new UserRegistered($user, $code));

        return response()->json(['success' => 'تم إرسال كود التحقق' ]);
    }

    /**
     * Authenticate user and send verification code
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {

        $user = $this->userRepository->findByEmail($request->email);

        $this->authService->checkPassword($request->password, $user->password);

        $code = $this->codeService->generateCode($user);

        event(new UserRegistered($user, $code));


        return response()->json(['success' => 'تم إرسال كود التحقق']);
    }

    /**
     * Resend verification code to user's email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function ResendCode(EmailRequest $request)
    {

        $user = $this->userRepository->findByEmail($request->email);

        $code = $this->codeService->generateCode($user);

        event(new UserRegistered($user, $code));

        return response()->json(['success' => 'تم إرسال كود التحقق']);
    }

    /**
     * Verify user's code and activate account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function verifyCode(EmailAndCodeRequest $request)
    {

        $user = $this->userRepository->findByEmail($request->email);

        $storedCode = $this->casheService->getCodeFromCashe($user);

        if (!$storedCode || $storedCode != $request->code) {
            throw new InvalidCodeException();
        }

        $this->userRepository->update($user, ['email_verified_at' => now()]);

        $this->casheService->forgetCodeFromCashe($user);

        $this->userRepository->deleteUserToken($user);

        $token = $this->userRepository->createToken($user);

        return response()->json(['success' => 'تم تفعيل الحساب بنجاح', 'token' => $token]);
    }

    /**
     * Refresh authentication token
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function refreshToken()
    {

        $user = auth()->user();

        $this->userRepository->deleteUserToken($user);

        $newToken = $this->userRepository->createToken($user);

        return response()->json(['success' => 'تم تحديث التوكن بنجاح', 'token' => $newToken]);
    }

    public function logout()
    {
        $user = auth()->user();
        // dd($user);
        $this->userRepository->deleteUserToken($user);
        return response()->json(['success' => 'تم تسجيل الخروج بنجاح']);
    }



    public function storeFCM_Token(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $this->authService->storeFCM(auth()->user(),$request->input('fcm_token'));

        return response()->json(['success' => 'تم حفظ التوكن بنجاح']);
    }
}
