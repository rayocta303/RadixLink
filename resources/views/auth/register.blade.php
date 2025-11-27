@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h1 class="text-center text-3xl font-bold text-primary-600">ISP Manager</h1>
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-white">Register your ISP Business</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white dark:bg-gray-800 px-6 py-12 shadow sm:rounded-lg sm:px-12">
            @if($errors->any())
                <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Your Name</label>
                    <div class="mt-2">
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Company / ISP Name</label>
                    <div class="mt-2">
                        <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="subdomain" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Subdomain</label>
                    <div class="mt-2 flex rounded-md shadow-sm">
                        <input id="subdomain" name="subdomain" type="text" required value="{{ old('subdomain') }}" class="block w-full min-w-0 flex-1 rounded-none rounded-l-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3" placeholder="myisp">
                        <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 dark:border-gray-600 dark:bg-gray-600 px-3 text-gray-500 dark:text-gray-300 sm:text-sm">.ispmanager.com</span>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Email address</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Phone Number</label>
                    <div class="mt-2">
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Password</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Confirm Password</label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Register</button>
                </div>
            </form>
        </div>

        <p class="mt-10 text-center text-sm text-gray-500 dark:text-gray-400">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold leading-6 text-primary-600 hover:text-primary-500">Sign in</a>
        </p>
    </div>
</div>
@endsection
