<x-frontend-layout>

    {{-- Carousel Section --}}
    <section class="py-1">
        <div id="default-carousel" class="relative w-full" data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-base md:h-150">
                <!-- Item 1 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/slider1.png') }}"
                        class="absolute block w-full md:h-150 -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                        alt="...">
                </div>
                <!-- Item 2 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/slider4.png') }}"
                        class="absolute block w-full md:h-150 -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                        alt="...">
                </div>
                <!-- Item 3 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ asset('images/slider2.jpg') }}"
                        class="absolute block w-full md:h-150 -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                        alt="...">
                </div>

            </div>
            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-base" aria-current="true" aria-label="Slide 1"
                    data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 2"
                    data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 3"
                    data-carousel-slide-to="2"></button>
            </div>
            <!-- Slider controls -->
            <button type="button"
                class="absolute top-0 inset-s-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-prev>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m15 19-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button"
                class="absolute top-0 inset-e-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-next>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m9 5 7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
        </div>
    </section>

    <Section>
        <div class="py-4">
            <div class="bg-orange-400 px-4 py-3">
                <div class="flex justify-between items-center container">
                    <h2 class="text-[18px] font-bold text-white">Best Selling Products</h2>
                    <a href="#"
                        class="text-sm font-medium text-white hover:underline focus:outline-none focus:ring-4 focus:ring-gray-300 rounded-base">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            {{-- Cards --}}
            <div class="container  py-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols- lg:grid-cols-4 gap-6">
                <x-frontend-card />
            </div>
        </div>
    </Section>

    {{-- Boy's Section --}}
    <Section>
        <div class="py-4">
            <div class="bg-orange-400 px-4 py-3">
                <div class="flex justify-between items-center container">
                    <h2 class="text-[18px] font-bold text-white">Boy's Products</h2>
                    <a href="#"
                        class="text-sm font-medium text-white hover:underline focus:outline-none focus:ring-4 focus:ring-gray-300 rounded-base">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            {{-- Cards --}}
            <div class="container  py-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols- lg:grid-cols-4 gap-6">
                <x-frontend-card />
            </div>
        </div>
    </Section>
    {{-- Gorls Product --}}
    <Section>
        <div class="py-4">
            <div class="bg-orange-400 px-4 py-3">
                <div class="flex justify-between items-center container">
                    <h2 class="text-[18px] font-bold text-white">Girls Products</h2>
                    <a href="#"
                        class="text-sm font-medium text-white hover:underline focus:outline-none focus:ring-4 focus:ring-gray-300 rounded-base">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            {{-- Cards --}}
            <div class="container  py-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols- lg:grid-cols-4 gap-6">
                <x-frontend-card />
            </div>
        </div>
    </Section>
</x-frontend-layout>
