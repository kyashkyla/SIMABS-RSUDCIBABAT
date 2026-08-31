<?php

namespace App\Http\Requests\Auth;

use App\Models\Employee;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Form login pegawai (Auth/Login.vue) mengirim field `nip`,
     * sedangkan form login admin (Auth/Admin/AdminLogin.vue) tetap
     * mengirim field `username`. Keduanya sama-sama POST ke rute
     * `login` yang sama, jadi salah satu dari dua field itu wajib ada.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nip' => ['required_without:username', 'string'],
            'username' => ['required_without:nip', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Login pegawai: pakai NIP.
        //
        // NIP disimpan di tabel `employees`, bukan di tabel `users`,
        // jadi tidak bisa langsung dipakai di Auth::attempt() seperti
        // username. Makanya di sini kita cari dulu Employee-nya
        // berdasarkan NIP, lalu cek password terhadap akun User yang
        // terhubung ke Employee tersebut.
        if ($this->filled('nip')) {
            $employee = Employee::where('nip', $this->string('nip'))->first();

            if (! $employee
                || ! $employee->user
                || ! Hash::check((string) $this->password, $employee->user->password)
            ) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'nip' => 'NIP atau password salah.',
                ]);
            }

            Auth::login($employee->user, $this->boolean('remember'));

            RateLimiter::clear($this->throttleKey());

            return;
        }

        // Login admin: tetap pakai username seperti sebelumnya.
        if (! Auth::attempt(
            [
                'username' => $this->username,
                'password' => $this->password,
            ],
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $field = $this->filled('nip') ? 'nip' : 'username';

        throw ValidationException::withMessages([
            $field => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * Dibedakan berdasarkan NIP (pegawai) atau username (admin), supaya
     * batas percobaan login-nya tetap per-akun seperti semula.
     */
    public function throttleKey(): string
    {
        $identifier = $this->filled('nip')
            ? $this->string('nip')
            : $this->string('username');

        return Str::transliterate(Str::lower($identifier).'|'.$this->ip());
    }
}