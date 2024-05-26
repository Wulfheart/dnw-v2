<?php
use Dnw\Foundation\User\UserViewModel;

/** @var UserViewModel $user */ ?>

<div class="shadow">
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="http://localhost:8000/dashboard" class="flex flex-row items-end">
                            <div class="text-4xl font-black">DNW</div>
                            <div class="">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 mb-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                alpha
                            </span>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a class="inline-flex items-center px-1 pt-1 border-b-2 border-primary-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-primary-700 transition"
                           href="http://localhost:8000/games">
                            Spiele
                        </a>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                        <div class="relative" x-data="{ open: false }" @click.away="open = false"
                             @close.stop="open = false">
                            <div @click="open = ! open">
        <span class="inline-flex rounded-md">
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ $user->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </span>
                            </div>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0 "
                                 style="display: none;" @click="open = false">
                                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>

                                    <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"
                                       href="http://localhost:8000/user/profile">Profile</a>


                                    <div class="border-t border-gray-100"></div>

                                    <!-- Authentication -->
                                    <form method="POST" action="http://localhost:8000/logout">
                                        <input type="hidden" name="_token"
                                               value="wC9lrHqWv0wYs5Qsh1coD2OJefso8aVENXaVvBzP">
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition"
                                           href="http://localhost:8000/logout" onclick="event.preventDefault();
                                                this.closest('form').submit();">Log Out</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a class="block pl-3 pr-4 py-2 border-l-4 border-primary-400 text-base font-medium text-primary-700 bg-primary-50 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700 transition"
                   href="http://localhost:8000/games">
                    Spiele
                </a>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">

                    <div>
                        <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
{{--                        <div class="font-medium text-sm text-gray-500">wiebke17@example.com</div>--}}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <a class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition"
                       href="http://localhost:8000/user/profile">
                        Profile
                    </a>


                    <!-- Authentication -->
                    <form method="POST" action="http://localhost:8000/logout">
                        <input type="hidden" name="_token" value="wC9lrHqWv0wYs5Qsh1coD2OJefso8aVENXaVvBzP">
                        <a class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition"
                           href="http://localhost:8000/logout" onclick="event.preventDefault();
                                    this.closest('form').submit();">
                            Log Out
                        </a>
                    </form>

                </div>
            </div>
        </div>
    </nav>
</div>
