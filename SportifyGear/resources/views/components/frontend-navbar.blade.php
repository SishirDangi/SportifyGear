<section>
    <nav class="felx bg-(--primary)">
        <div class="flex container items-center justify-between h-20">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" class="h-25 md:h-40" alt="Logo">
                </a>
            </div>

            <form class="w-[50%] mx-auto ">
                <label for="search" class="block mb-2.5 text-sm font-medium text-heading sr-only ">Search</label>
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 inset-s-0 flex items-center px-1 md:ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="search"
                        class="block w-full py-2 px-4  md:p-3 md:ps-9  border bg-(--background)  text-heading text-sm rounded-3xl focus:ring-(--secondary) focus:border-(--secondary) shadow-xs placeholder:px-1 placeholder:text-body"
                        placeholder=" Search entired store here ....." required />
                    <button type="button"
                        class="absolute inset-e-1.5 text-white bg-(--secondary) hover:bg-(--secondary) box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-3xl text-xs px-4 py-1  md:px-3 md:py-1.5 focus:outline-none">Search</button>
                </div>
            </form>

            <div class="hidden md:flex gap-5">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/watchlist.png') }}" class="h-15" alt="Logo">
                </a>
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/profile.png') }}" class="h-15" alt="Logo">
                </a>
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/cart.png') }}" class=" h-15" alt="Logo">
                </a>
            </div>
            <div class="text-center md:hidden">
                <button class="text-[20px]" type="button" data-drawer-target="drawer-example"
                    data-drawer-show="drawer-example" aria-controls="drawer-example">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

        </div>

        <div class="container pb-1 pr-18 hidden md:block">
            <ul
                class="flex justify-center gap-5 border bg-(--secondary) border-(--secondary) rounded-3xl w-[60%] mx-auto py-2 cursor-pointer">
                <li><a href="{{ route('home') }}">BOYS SPORTS</a></li>|
                <li><a href="{{ route('home') }}">GIRLS SPORTS</a></li>|
                <li><a href="{{ route('home') }}">SPORT WEAR</a></li>|
                <li><a href="{{ route('home') }}">CONTACT US</a></li>|
                <li><a href="{{ route('home') }}">OTHERS</a></li>
            </ul>
        </div>
    </nav>
</section>

<!-- drawer component -->
<div id="drawer-example"
    class="fixed top-0 left-0 z-40 h-screen overflow-y-auto transition-transform -translate-x-full bg-(--secondary) w-96"
    tabindex="-1" aria-labelledby="drawer-label">
    <div class="border-b bg-(--primary) w-full h-20 pb-4 mb-5 flex  justify-center items-center">
        <h5 id="drawer-label" class="flex justify-center text-lg text-body">
            <img src="{{ asset('images/logo.png') }}" class="h-25 mt-3 " alt="Logo">
        </h5>
        <button type="button" data-drawer-hide="drawer-example" aria-controls="drawer-example"
            class="text-body bg-transparent hover:text-heading hover:bg-neutral-tertiary rounded-base w-9 h-9 absolute top-2.5 inset-e-2.5 flex items-center justify-center">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
    </div>
    <div class="flex w-full justify-center items-center gap-6">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/watchlist.png') }}" class="h-15" alt="Logo">
        </a>
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/profile.png') }}" class="h-15" alt="Logo">
        </a>
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/cart.png') }}" class=" h-15" alt="Logo">
        </a>
    </div>
    <div class="flex flex-col w-full  py-4 gap-4 justify-center items-center list-none">
        <li class="border-b text-[16px] text-center "><a href="{{ route('home') }}">BOYS
                SPORTS</a></li>
        <li class="border-b text-[16px] text-center "><a href="{{ route('home') }}">GIRLS
                SPORTS</a></li>
        <li class="border-b text-[16px] text-center "><a href="{{ route('home') }}">SPORT
                WEAR</a></li>
        <li class="border-b text-[16px] text-center "><a href="{{ route('home') }}">CONTACT
                US</a></li>
        <li class="border-b text-[16px] text-center "><a href="{{ route('home') }}">OTHERS</a>
        </li>
    </div>
</div>
