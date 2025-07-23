@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-center">تسجيل الدخول</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">البريد الإلكتروني</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="w-full px-3 py-2 border rounded" />
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700">كلمة المرور</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2 border rounded" />
                </div>
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2" />
                        <span class="text-sm text-gray-600">تذكرني</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">هل نسيت كلمة المرور؟</a>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">تسجيل الدخول</button>
            </form>
        </div>
    </div>
@endsection
