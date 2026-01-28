<!-- State Edit Modal -->
<div class="modal fade" id="ambassadorDetailsViewModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Ambassador Approval Form') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ambassador.approve') }}" method="POST" id="view_ambassador_details">
                @csrf
                <input type="hidden" class="ambassadorid" name="ambassador_id" hidden>
                <div class="modal-body">
                    <div class="row g-4 gy-5">
                        <div class="col-lg-6">
                            <div class="user-profile userProfileDetails">
                                <div class="userProfileDetails__header">
                                    <h5 class="userProfileDetails__title">{{ __('Personal Information') }}</h5>
                                </div>
                                <div class="userDetails__wrapper userProfile__details mt-3">
                                    <div class="userDetails__wrapper userProfile__details mt-3">
                                        <div class="userProfile__details__thumb mb-3">
                                            {{-- Profile Image --}}
                                            <img style="width:150px" class="profileimage"
                                                alt="{{ __('Profile Image') }}">
                                        </div>
                                    </div>
                                    <p class="userDetails__wrapper__item"><strong>{{ __('Full Name:') }}</strong> <span
                                            class="fullname"></span></p>
                                    <p class="userDetails__wrapper__item"><strong>{{ __('Email:') }}</strong> <span
                                            class="email"></span></p>
                                    <p class="userDetails__wrapper__item"><strong>{{ __('Username:') }}</strong> <span
                                            class="username"></span></p>
                                    <p class="userDetails__wrapper__item"><strong>{{ __('Phone:') }}</strong> <span
                                            class="phone"></span></p>
                                    <p class="userDetails__wrapper__item"><strong>{{ __('Completed Orders:') }}</strong>
                                        <span class="completedorders"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="user-identity userProfileDetails">
                                <div class="userProfileDetails__header">
                                    <h5 class="userProfileDetails__title">{{ __('School Info') }}</h5>
                                    <div class="userDetails__wrapper userProfile__details mt-3">
                                        <p class="userDetails__wrapper__item">
                                            <strong>{{ __('Schoool Name:') }}</strong> <span class="school"></span>
                                        </p>
                                        <p class="userDetails__wrapper__item">
                                            <strong>{{ __('Level:') }}</strong> <span class="level"></span>
                                        </p>
                                        <p class="userDetails__wrapper__item">
                                            <strong>{{ __('Course:') }}</strong> <span class="course"></span>
                                        </p>
                                        <p class="userDetails__wrapper__item">
                                            <strong>{{ __('Address:') }}</strong> <span class="address"></span>
                                        </p>
                                        <div class="mb-3">
                                            <strong>{{ __('Reason to be an Ambassador:') }}</strong>
                                            <div class="reason"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Admin Notes --}}
                    <div class="mb-3 mt-4">
                        <label for="admin_notes" class="form-label strong">{{ __('Enter Notes') }}</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
                            placeholder="{{ __('Enter a brief notes to send with ambassador notification notice...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    @can('approve-ambassador')
                        <x-btn.submit :title="__('Decline As Ambassador')" :class="'btn btn-danger mt-4 pr-4 pl-4'" />
                        <x-btn.submit :title="__('Approve As Ambassador')" :class="'btn btn-primary mt-4 pr-4 pl-4'" />
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>
