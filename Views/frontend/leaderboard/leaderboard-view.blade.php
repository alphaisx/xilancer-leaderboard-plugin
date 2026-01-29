@extends('frontend.new_design.layout.new_master')
@section('site_title', __('Leaderboard'))
@section('script')
    <script type="module" src="https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-wc@0.8.14/dist/dotlottie-wc.min.js">
    </script>
@endsection
@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb-02 :innerTitle="__('Leaderboard')" />
        <section class="leaderboard-area pt-12 pb-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 space-y-12">

                @if ($candidates->isEmpty())
                    {{-- Coming Soon --}}
                    <div class="flex justify-center mt-8 mb-8">
                        <div class="w-full md:w-2/3 lg:w-1/2 text-center">
                            <img src="{{ asset('assets/leaderboard/coming_soon.png') }}" alt="Coming Soon"
                                class="mx-auto mb-4 w-1/2">
                            <h3 class="mb-3 text-xl font-semibold">{{ __('Leaderboard Coming Soon!') }}</h3>
                            <p class="text-gray-500">
                                {{ __('We are working hard to bring you an exciting leaderboard experience. Stay tuned!') }}
                            </p>
                        </div>
                    </div>
                @else
                    @php
                        $topThree = $candidates->take(3);
                        $others = $candidates->slice(3);
                        $last_score =
                            $topThree
                                ->filter(function ($data) {
                                    return $data->score_snapshot > 0;
                                })
                                ->last()->score_snapshot ?? 100;
                    @endphp

                    <!-- Header with Lottie -->
                    <div class="flex justify-center mb-6">
                        <div class="w-full lg:w-3/4 text-center">
                            <h2 class="text-4xl font-semibold mt-3">{{ __('Top Talent Leaderboard') }}</h2>
                            <p class="text-gray-500">
                                {{ __('Celebrating the elite freelancers of Gigafro based on performance and excellence.') }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-20">
                        <!-- Podium Section (Top 3) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-5 gap-y-12 my-12 items-end justify-center">
                            @foreach ([1, 0, 2] as $rankIndex)
                                {{-- Reordered for visual layout: 2nd, 1st, 3rd --}}
                                @if (isset($topThree[$rankIndex]))
                                    <a href="{{ route('freelancer.profile.details', $topThree[$rankIndex]->user->username) }}"
                                        {{-- target="_blank" class="w-full block" --}}
                                        class="{{ $rankIndex == 0 ? 'order-1 md:order-2' : ($rankIndex == 1 ? 'order-2 md:order-1' : 'order-3') }} w-full">
                                        <div
                                            class="relative flex items-center flex-col hover:shadow-lg transition-all rounded-2xl  {{ $rankIndex == 0 ? 'border-2 shadow-md border-[var(--main-color-one)] pt-14 pb-10' : 'shadow-md py-10' }}">
                                            <div
                                                class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-10 h-10 text-white font-bold z-10 flex items-center justify-center rounded-full {{ $rankIndex == 0 ? 'bg-[var(--main-color-one)]' : 'bg-yellow-800' }}">
                                                {{ $rankIndex + 1 }}
                                            </div>
                                            @if ($rankIndex == 0)
                                                <div class="absolute pointer-events-none -top-20 left-0 right-0 ">
                                                    <dotlottie-wc
                                                        src="{{ asset('assets/leaderboard/celebration-animation.lottie') }}"
                                                        autoplay loop></dotlottie-wc>
                                                </div>
                                            @endif
                                            <img src="{{ fetch_image($topThree[$rankIndex]->user) }}"
                                                class="w-20 h-20 mb-3 rounded-full border-4 border-white shadow-md object-cover"
                                                alt="">
                                            <h5 class="mb-1">
                                                {{ ucwords(strtolower($topThree[$rankIndex]->user->fullname)) }}
                                                @if ($topThree[$rankIndex]->user->user_verified_status == 1)
                                                    <i class="fas fa-circle-check text-success"></i>
                                                @endif
                                            </h5>
                                            <small class="text-gray-500 block mb-2">
                                                {{ optional($topThree[$rankIndex]->user->user_introduction)->title ?? 'Top Freelancer' }}
                                            </small>
                                            @php
                                                $score = $topThree[$rankIndex]->score_snapshot ?? 0;
                                                // Use previously computed $last_score as a baseline (fallback to 1000)
                                                $baseline = isset($last_score) ? (int) $last_score : 1000;
                                                $baseline = max(100, $baseline);

                                                // Ensure progression: 1st (rankIndex==0) > 2nd (1) > 3rd (2)
                                                if ($rankIndex == 0) {
                                                    $score = $baseline + 300;
                                                } elseif ($rankIndex == 1) {
                                                    $score = $baseline + 150;
                                                } else {
                                                    $score = $baseline + 50;
                                                }
                                            @endphp
                                            <p class="text-primary font-extrabold">
                                                {{ round($score * 10) }} {{ __('Points') }}
                                            </p>
                                            @if ($rankIndex == 0)
                                                <span
                                                    class="inline-block px-3 py-1 text-sm rounded mt-2 bg-transparent text-[var(--main-color-one)] font-semibold border-2 border-[var(--main-color-one)] border-dashed">
                                                    {{ __('Top Performer') }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <!-- Grid Section (Rank 4-20+) -->
                        <div
                            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 lg:gap-4 mt-5 mb-5">
                            @foreach ($others as $index => $candidate)
                                <a class="block"
                                    href="{{ route('freelancer.profile.details', $candidate->user->username) }}"
                                    target="_blank">
                                    <div
                                        class="relative border-none rounded-xl overflow-hidden transition-all bg-white p-4 text-center shadow-md h-full hover:shadow-lg  hover:transform hover:-translate-y-1 duration-700 flex flex-col items-center justify-center">
                                        <span
                                            class="absolute top-2 right-4 font-extrabold text-gray-400 text-base tracking-wider">#{{ $index + 4 }}</span>
                                        <img src="{{ fetch_image($candidate->user) }}"
                                            class="w-16 h-16 object-cover rounded-full mb-2 shadow-sm border-2 border-white"
                                            alt="">
                                        <h6 class="mb-1 truncate">
                                            {{ ucwords(strtolower($candidate->user->fullname)) }}
                                        </h6>
                                        <small class="text-gray-500 block mb-2">
                                            {{ optional($candidate->user->user_introduction)->title ?? '' }}
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

@endsection
