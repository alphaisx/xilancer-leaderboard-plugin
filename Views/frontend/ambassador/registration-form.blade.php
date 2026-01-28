@extends('frontend.layout.master')
@section('site_title', __('Become An Ambassador'))

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .form-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            padding: 40px;
        }

        @media (max-width: 767px) {
            .form-card {
                padding: 20px;
            }
        }

        .benefits-banner {
            background: linear-gradient(135deg, var(--main-color-one), #004dbe);
            color: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .benefits-banner h3 {
            color: #fff;
        }

        .benefits-banner ul {
            list-style: none;
            padding: 0;
        }

        .benefits-banner li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .benefits-banner li i {
            margin-right: 15px;
            color: #ffeb3b;
            font-size: 1.2rem;
            margin-top: 5px;
        }

        .btn-primary-accent {
            background-color: var(--main-color-one);
            border-color: var(--main-color-one);
            transition: all 0.3s ease;
        }

        .btn-primary-accent:hover {
            background-color: #004dbe;
            border-color: #004dbe;
            transform: translateY(-2px);
        }

        /* Custom Input Focus style for accent color */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--main-color-one);
            box-shadow: 0 0 0 0.25rem rgba(0, 102, 245, 0.25);
            /* Adjust opacity of main-color-one */
        }

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
            padding: 4px 10px;
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
        <x-breadcrumb.user-profile-breadcrumb :title="__('Become An Ambassador')" :innerTitle="__('Become An Ambassador')" />

        <section class="ambassador-area pt-5 pb-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">

                    <!-- Benefits Banner Section (Left Column) -->
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="benefits-banner h-100">
                            <h3 class="mb-4">Why Become a Gigafro Ambassador?</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Exclusive access to projects and networking events.
                                </li>
                                <li><i class="fas fa-check-circle"></i> Earn commissions and performance bonuses.</li>
                                <li><i class="fas fa-check-circle"></i> Enhance your CV with real-world leadership
                                    experience.</li>
                                <li><i class="fas fa-check-circle"></i> Earn higher bonuses on referrals.</li>
                                <li><i class="fas fa-check-circle"></i> Receive mentorship from industry experts.</li>
                                <li><i class="fas fa-check-circle"></i> Get featured on our social media platforms.</li>
                            </ul>
                            <p class="mt-4 text-white">Join an elite community shaping the future of freelancing!</p>
                        </div>
                    </div>

                    <!-- Application Form Section (Right Column) -->
                    <div class="col-lg-7">
                        <div class="form-card">
                            <h4 class="mb-4">{{ __('Ambassador Application Form') }}</h4>
                            <p class="text-muted mb-4">Complete the details below to submit your application to join our
                                team.</p>

                            <form id="ambassadorForm">
                                @csrf
                                <!-- Personal Details Grid Row -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4 col-form-group">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="fullname"
                                            value="{{ auth()->user()->fullname ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 col-form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ auth()->user()->email ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 col-form-group">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ auth()->user()->phone ?? '' }}" readonly>
                                    </div>
                                </div>

                                <!-- Academic Details Grid Row -->
                                <div class="row g-3 mb-3 position-relative">
                                    <div class="col-12">
                                        <label class="form-label">Tertiary Institution Name (Africa)</label>
                                        <select class="form-control institution-select" name="school" required>
                                            <option value="">Search for your university...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Course of Study</label>
                                        <input type="text" class="form-control" name="course" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Level</label>
                                        <select class="form-select" name="level" required>
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

                                <!-- Motivation Textarea -->
                                <div class="mb-4">
                                    <label class="form-label">Why are you a great fit for this role?</label>
                                    <textarea class="form-control" name="reason" rows="4"
                                        placeholder="Describe your leadership skills, influence, and motivation." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-md btn-primary-accent w-100" id="submitBtn">Submit
                                    Application</button>
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
                                "{{ Auth::guard('web')->user()->user_type == 1 ? route('client.dashboard') : route('freelancer.dashboard') }}";
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
