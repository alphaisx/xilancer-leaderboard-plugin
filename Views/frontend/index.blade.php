@extends('frontend.layout.master')
@section('site_title', __('Leaderboard'))
@section('script')
    <script type="module" src="https://unpkg.com/@lottiefiles/dotlottie-wc@latest/dist/dotlottie-wc.js"></script>
@endsection
@section('style')
    <style>
        /* Existing Podium Styles */
        .podium-item {
            position: relative;
            padding: 30px;
            text-align: center;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 30px;
            border: 1px solid #eee;
            transition: 0.3s;
        }

        .podium-item:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .podium-1 {
            transform: scale(1.1);
            z-index: 2;
            padding-top: 3.2rem;
            border: 2px solid var(--main-color-one);
            box-shadow: 0 12px 20px color-mix(in srgb, var(--main-color-one) 30%, transparent);
        }

        .rank-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
            z-index: 3;
        }

        .bg-gold {
            background: var(--main-color-one);
        }

        .bg-silver {
            background: linear-gradient(45deg, #c0c0c0, #8e8e8e);
        }

        .bg-bronze {
            background: linear-gradient(45deg, #cd7f32, #a0522d);
        }

        .avatar-lg {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* New Grid Item Styles */
        .rank-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .rank-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .rank-number-small {
            position: absolute;
            top: 10px;
            right: 15px;
            font-weight: 800;
            color: #bbb;
            font-size: 1.2rem;
        }

        .avatar-sm {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .lottie-header {
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
    </style>
@endsection

@section('content')
    <main>
        @if (!moduleExists('CoinPaymentGateway'))
            <x-frontend.category.category />
        @endif

        <x-breadcrumb.user-profile-breadcrumb :title="__('Leaderboard')" :innerTitle="__('Leaderboard')" />

        <section class="leaderboard-area padding-top-120 padding-bottom-120 bg-light">
            <div class="container px-4">

                @if ($candidates->isEmpty())
                    {{-- Coming Soon --}}
                    <div class="row justify-content-center mt-5 mb-5">
                        <div class="col-lg-6 text-center">
                            <img src="{{ asset('assets/leaderboard/coming_soon.png') }}" alt="Coming Soon"
                                class="img-fluid mb-4 w-50 ">
                            <h3 class="mb-3">{{ __('Leaderboard Coming Soon!') }}</h3>
                            <p class="text-muted">
                                {{ __('We are working hard to bring you an exciting leaderboard experience. Stay tuned!') }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Header with Lottie -->
                    <div class="row justify-content-center mb-5">
                        <div class="col-lg-8 text-center">
                            <div class="lottie-header">
                                <lottie-player src="https://lottie.host" background="transparent" speed="1" loop
                                    autoplay></lottie-player>
                            </div>
                            <h2 class="title mt-3">{{ __('Top Talent Leaderboard') }}</h2>
                            <p class="text-muted">
                                {{ __('Celebrating the elite freelancers of Gigafro based on performance and excellence.') }}
                            </p>
                        </div>
                    </div>
                    @php
                        $topThree = $candidates->take(3);
                        $others = $candidates->slice(3);
                        if (!function_exists('fetch_image')) {
                            # to fetch user image with cloud storage support
                            function fetch_image($user)
                            {
                                if ($user?->image) {
                                    return cloudStorageExist() &&
                                        in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])
                                        ? render_frontend_cloud_image_if_module_exists(
                                            'profile/' . $user->image,
                                            load_from: $user->load_from,
                                        )
                                        : asset('assets/uploads/profile/' . $user->image);
                                }
                                return asset('assets/static/img/author/author.jpg');
                            }
                        }
                    @endphp

                    <!-- Podium Section (Top 3) -->
                    <div class="row align-items-end gx-5 gy-2 mb-5 mt-5">
                        @foreach ([1, 0, 2] as $rankIndex)
                            {{-- Reordered for visual layout: 2nd, 1st, 3rd --}}
                            @if (isset($topThree[$rankIndex]))
                                <a href="{{ route('freelancer.profile.details', $topThree[$rankIndex]->user->username) }}"
                                    target="_blank"
                                    class="col-md-4 {{ $rankIndex == 0 ? 'order-1 order-md-2' : ($rankIndex == 1 ? 'order-2 order-md-1' : 'order-3') }}">
                                    <div class="podium-item {{ $rankIndex == 0 ? 'podium-1' : '' }}">
                                        <div
                                            class="rank-badge {{ $rankIndex == 0 ? 'bg-gold' : ($rankIndex == 1 ? 'bg-silver' : 'bg-bronze') }}">
                                            {{ $rankIndex + 1 }}
                                        </div>
                                        @if ($rankIndex == 0)
                                            <div
                                                style="position: absolute; top: -70px; left: 0; right: 0; pointer-events: none;">
                                                <dotlottie-wc
                                                    src="{{ asset('assets/leaderboard/celebration-animation.lottie') }}"
                                                    autoplay loop></dotlottie-wc>
                                            </div>
                                        @endif
                                        <img src="{{ fetch_image($topThree[$rankIndex]->user) }}" class="avatar-lg mb-3"
                                            alt="">
                                        <h5 class="mb-1">
                                            {{ ucwords(strtolower($topThree[$rankIndex]->user->fullname)) }}
                                            @if ($topThree[$rankIndex]->user->user_verified_status == 1)
                                                <i class="fas fa-circle-check text-success"></i>
                                            @endif
                                        </h5>
                                        <small class="text-muted d-block mb-2">
                                            {{ optional($topThree[$rankIndex]->user->user_introduction)->title ?? '' }}
                                        </small>
                                        @if ($topThree[$rankIndex]->score_snapshot > 10)
                                            <p class="text-primary fw-bold">
                                                {{ round($topThree[$rankIndex]->score_snapshot * 10) }}
                                                {{ __('Points') }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <!-- Grid Section (Rank 4-20+) -->
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4 mt-5 mb-5">
                        @foreach ($others as $index => $candidate)
                            <a class="col" href="{{ route('freelancer.profile.details', $candidate->user->username) }}"
                                target="_blank">
                                <div class="rank-card p-4 text-center shadow-sm h-100">
                                    <span class="rank-number-small">#{{ $index + 1 }}</span>
                                    <img src="{{ fetch_image($candidate->user) }}"
                                        class="avatar-sm shadow-sm border border-2 border-white" alt="">
                                    <h6 class="mb-1 text-truncate">
                                        {{ ucwords(strtolower($candidate->user->fullname)) }}
                                    </h6>
                                    <small class="text-muted d-block mb-2">
                                        {{ optional($candidate->user->user_introduction)->title ?? '' }}
                                    </small>
                                    @if ($candidate->score_snapshot > 10)
                                        <small class="text-primary fw-bold">{{ round($candidate->score_snapshot * 10) }}
                                            Pts</small>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>

    <script src="https://unpkg.com"></script>
@endsection
