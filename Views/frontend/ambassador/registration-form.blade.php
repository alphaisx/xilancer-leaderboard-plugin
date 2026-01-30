@extends('frontend.new_design.layout.new_master')
@section('site_title', __('Become An Ambassador'))

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Modernizing the Select2 container to match Bootstrap 5 */
        .select2-container {
            z-index: 1050 !important;
            /* Ensure it appears above other elements */
        }

        .select2-container--default .select2-selection--single {
            min-height: 40px !important;
            height: auto !important;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            padding: 0.625rem 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            white-space: normal !important;
            line-height: 1.15 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }

        .select2-dropdown {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb-02 :innerTitle="__('Become An Ambassador')" />
        <section class="py-12 max-w-340 mx-auto px-4 bg-white">
            <div>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                    {{-- BENEFITS --}}
                    <div class="lg:col-span-5">
                        <div
                            class="h-full rounded-3xl p-8 sm:p-10
                               bg-linear-to-br from-primary to-blue-700
                               text-white shadow-2xl">
                            <h3 class="text-2xl font-bold mb-6">
                                Why Become a Gigafro Ambassador?
                            </h3>

                            <ul class="space-y-4">
                                @foreach (['Exclusive access to projects and networking events', 'Earn commissions and performance bonuses', 'Enhance your CV with real-world leadership experience', 'Earn higher bonuses on referrals', 'Receive mentorship from industry experts', 'Get featured on our social media platforms'] as $benefit)
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-check-circle text-yellow-300 mt-1"></i>
                                        <span class="text-sm sm:text-base">{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <p class="mt-6 text-sm text-white/90">
                                Join an elite community shaping the future of freelancing.
                            </p>
                        </div>
                    </div>

                    {{-- FORM --}}
                    <div class="lg:col-span-7">
                        <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-10 h-full">
                            <h4 class="text-xl font-semibold text-gray-800 mb-2">
                                {{ __('Ambassador Application Form') }}
                            </h4>
                            <p class="text-sm text-gray-500 mb-6">
                                Complete the details below to submit your application.
                            </p>

                            <form id="ambassadorForm">
                                @csrf

                                {{-- USER INFO --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Full Name</label>
                                        <input type="text" {{ auth()?->user()?->fullname ? 'readonly' : '' }}
                                            name="fullname" value="{{ auth()->user()->fullname ?? '' }}"
                                            class="mt-1 w-full rounded-xl border border-gray-300 {{ auth()?->user()?->fullname ? 'bg-gray-100' : 'focus:ring-2 focus:ring-primary/40 focus:outline-none' }} text-gray-700 px-4 py-2.5" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Email</label>
                                        <input type="email" {{ auth()?->user()?->email ? 'readonly' : '' }} name="email"
                                            value="{{ auth()->user()->email ?? '' }}"
                                            class="mt-1 w-full rounded-xl border border-gray-300 {{ auth()?->user()?->email ? 'bg-gray-100' : 'focus:ring-2 focus:ring-primary/40 focus:outline-none' }} text-gray-700 px-4 py-2.5" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Phone</label>
                                        <input type="text" {{ auth()?->user()?->phone ? 'readonly' : '' }} name="phone"
                                            value="{{ auth()->user()->phone ?? '' }}"
                                            class="mt-1 w-full rounded-xl border border-gray-300 {{ auth()?->user()?->phone ? 'bg-gray-100' : 'focus:ring-2 focus:ring-primary/40 focus:outline-none' }} text-gray-700 px-4 py-2.5" />
                                    </div>
                                </div>

                                {{-- ACADEMIC --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-600">
                                            Tertiary Institution (Africa)
                                        </label>
                                        <select name="school" required class="institution-select px-4 py-2.5 w-full">
                                            <option value="">Search for your university...</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Course of Study</label>
                                        <input type="text" name="course" required
                                            placeholder="Enter your course of study"
                                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-primary/40 focus:outline-none" />
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Level</label>
                                        <select name="level" required
                                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5
                                               focus:ring-2 focus:ring-primary/40 focus:outline-none">
                                            <option value="">Select Level</option>
                                            <option value="100L">100 Level</option>
                                            <option value="200L">200 Level</option>
                                            <option value="300L+">300 Level & Above</option>
                                            <option value="graduate">Graduate</option>
                                            <option value="staff">Staff</option>
                                            <option value="masters">Masters</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- MOTIVATION --}}
                                <div class="mb-6">
                                    <label class="text-sm font-medium text-gray-600">
                                        Why are you a great fit?
                                    </label>
                                    <textarea name="reason" rows="4" required
                                        class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-3
                                           focus:ring-2 focus:ring-primary/40 focus:outline-none"
                                        placeholder="Describe your leadership skills, influence, and motivation"></textarea>
                                </div>

                                {{-- SUBMIT --}}
                                <button type="submit" id="submitBtn"
                                    class="w-full py-3 rounded-xl bg-primary text-white font-semibold
                                       hover:bg-primary/90 transition active:scale-95">
                                    Submit Application
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <!-- Include SweetAlert2 for modern alerts -->
    <script src="https://cdn.jsdelivr.net"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Check if jQuery is loaded (assuming it is included in your master layout)
            if (typeof $ === 'undefined') {
                console.error('jQuery not loaded! AJAX form submission will fail.');
                return;
            }
            // Initialize Select2 for Institution Search
            $('.institution-select').select2({
                ajax: {
                    // Using the Hipo API for real-time university search
                    url: "{{ route('api.university.search') }}",
                    dataType: 'json',
                    delay: 250, // Wait for user to stop typing
                    data: function(params) {
                        return {
                            name: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        // List of common African countries to prioritize/filter
                        const africanCountries = ["Nigeria", "Ghana", "South Africa", "Kenya", "Egypt",
                            "Ethiopia", "Uganda", "Rwanda", "Tanzania"
                        ];

                        // Map the API results to Select2 format
                        let results = data
                            .filter(item => africanCountries.includes(item
                                .country)) // Optional: Filter for Africa
                            .map(item => ({
                                id: item.name,
                                text: `${item.name} (${item.country})`
                            }));

                        return {
                            results: results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3,
                placeholder: "Start typing...",
                allowClear: true
            });
            // AJAX Submission
            $('#ambassadorForm').on('submit', function(e) {
                e.preventDefault();
                let submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: "{{ route('user.ambassador.store') }}", // Define this route in web.php
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Your application has been submitted successfully. We will review it shortly.',
                            confirmButtonColor: 'var(--main-color-one)'
                        }).then(() => {
                            window.location.href =
                                "{{ Auth::guard('web')?->user()?->user_type == 1 ? route('client.dashboard') : route('freelancer.dashboard') }}";
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Concatenate all validation errors into a single string
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join(
                                '<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMsg,
                            confirmButtonColor: 'var(--main-color-one)'
                        });
                        submitBtn.prop('disabled', false).text('Submit Application');
                    }
                });
            });
        });
    </script>
@endsection
