{{-- @props(['active' => null]) --}}

{{-- <?php--}}

{{--use App\ViewModel\Navigation\NavigationItemNameEnum;--}}
{{--use App\ViewModel\Navigation\NavigationItemsViewModel;--}}
{{--use Dnw\Foundation\User\UserViewModel;--}}

{{--/** @var UserViewModel $user */--}}
{{--/** @var NavigationItemsViewModel $navigation */--}}

{{--/** @var NavigationItemNameEnum|null $currentlyActiveItem */--}}
{{--$currentlyActiveItem = $active ?? null;--}}

{{--?> ?> ?> ?> ?> ?> ?> --}}


{{-- <div class="shadow"> --}}
{{--    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100"> --}}
{{--        <!-- Primary Navigation Menu --> --}}
{{--        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> --}}
{{--            <div class="flex justify-between h-16"> --}}
{{--                <div class="flex"> --}}
{{--                    <!-- Logo --> --}}
{{--                    <div class="flex-shrink-0 flex items-center"> --}}
{{--                        <a href="#" class="flex flex-row items-end"> --}}
{{--                            <div class="text-4xl font-black">DNW</div> --}}
{{--                            <div class=""> --}}
{{--                            <span --}}
{{--                                class="inline-flex items-center px-2.5 py-0.5 mb-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800"> --}}
{{--                                alpha --}}
{{--                            </span> --}}
{{--                            </div> --}}
{{--                        </a> --}}
{{--                    </div> --}}

{{--                    <!-- Navigation Links --> --}}
{{--                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"> --}}
{{--                        @foreach ($navigation as $nav) --}}
{{--                            <x-nav.link href="{{ $nav->route }}" --}}
{{--                                        :active="request()->routeIs($nav->route) || $currentlyActiveItem == $nav->name "> --}}
{{--                                {{ $nav->label }} --}}
{{--                            </x-nav.link> --}}
{{--                        @endforeach --}}

{{--                    </div> --}}
{{--                </div> --}}

{{--                <div class="hidden sm:flex sm:items-center sm:ml-6"> --}}

{{--                    <!-- Settings Dropdown --> --}}
{{--                    <div class="ml-3 relative"> --}}
{{--                        <x-nav.dropdown align="right" width="48"> --}}
{{--                            <x-slot name="trigger"> --}}
{{--                                    <span class="inline-flex rounded-md"> --}}
{{--                                    <button type="button" --}}
{{--                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"> --}}
{{--                                        {{ $user->name }} --}}

{{--                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" --}}
{{--                                             viewBox="0 0 20 20" fill="currentColor"> --}}
{{--                                            <path fill-rule="evenodd" --}}
{{--                                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" --}}
{{--                                                  clip-rule="evenodd"/> --}}
{{--                                        </svg> --}}
{{--                                    </button> --}}
{{--                                </span> --}}
{{--                            </x-slot> --}}

{{--                            <x-slot name="content"> --}}
{{--                                <!-- Account Management --> --}}
{{--                                <div class="block px-4 py-2 text-xs text-gray-400"> --}}
{{--                                    {{ __('Manage Account') }} --}}
{{--                                </div> --}}

{{--                                <x-nav.dropdown-link href="#"> --}}
{{--                                    {{ __('Profile') }} --}}
{{--                                </x-nav.dropdown-link> --}}

{{--                                <div class="border-t border-gray-100"></div> --}}

{{--                                <!-- Authentication --> --}}
{{--                                <form method="POST" action=""> --}}
{{--                                    @csrf --}}
{{--                                    <x-nav.dropdown-link href="" onclick="event.preventDefault(); --}}
{{--                                                this.closest('form').submit();"> --}}
{{--                                        {{ __('Log Out') }} --}}
{{--                                    </x-nav.dropdown-link> --}}
{{--                                </form> --}}
{{--                            </x-slot> --}}
{{--                        </x-nav.dropdown> --}}
{{--                    </div> --}}
{{--                </div> --}}

{{--                <!-- Hamburger --> --}}
{{--                <div class="-mr-2 flex items-center sm:hidden"> --}}
{{--                    <button @click="open = ! open" --}}
{{--                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"> --}}
{{--                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"> --}}
{{--                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" --}}
{{--                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" --}}
{{--                                  d="M4 6h16M4 12h16M4 18h16"/> --}}
{{--                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" --}}
{{--                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" --}}
{{--                                  d="M6 18L18 6M6 6l12 12"/> --}}
{{--                        </svg> --}}
{{--                    </button> --}}
{{--                </div> --}}
{{--            </div> --}}
{{--        </div> --}}

{{--        <!-- Responsive Navigation Menu --> --}}
{{--        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden"> --}}
{{--            <div class="pt-2 pb-3 space-y-1"> --}}
{{--                @foreach ($navigation as $nav) --}}
{{--                    <x-nav.responsive-link href="{{ $nav->route }}" --}}
{{--                                           :active="request()->routeIs($nav->route) || $currentlyActiveItem == $nav->name "> --}}
{{--                        {{ $nav->label }} --}}
{{--                    </x-nav.responsive-link> --}}
{{--                @endforeach --}}
{{--            </div> --}}

{{--            <!-- Responsive Settings Options --> --}}
{{--            <div class="pt-4 pb-1 border-t border-gray-200"> --}}
{{--                <div class="flex items-center px-4"> --}}
{{--                    <div> --}}
{{--                        <div class="font-medium text-base text-gray-800">{{ $user->name }}</div> --}}
{{--                        --}}{{--                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div> --}}
{{--                    </div> --}}
{{--                </div> --}}

{{--                <div class="mt-3 space-y-1"> --}}
{{--                    <!-- Account Management --> --}}
{{--                    <x-nav.responsive-link href="" --}}
{{--                                           :active="request()->routeIs('profile.show')"> --}}
{{--                        {{ __('Profile') }} --}}
{{--                    </x-nav.responsive-link> --}}


{{--                    <!-- Authentication --> --}}
{{--                    <form method="POST" action=""> --}}
{{--                        @csrf --}}

{{--                        <x-nav.responsive-link href="" onclick="event.preventDefault(); --}}
{{--                                    this.closest('form').submit();"> --}}
{{--                            {{ __('Log Out') }} --}}
{{--                        </x-nav.responsive-link> --}}
{{--                    </form> --}}

{{--                </div> --}}
{{--            </div> --}}
{{--        </div> --}}
{{--    </nav> --}}

{{-- </div> --}}
