<div class="modal fade" id="approveFormModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Approve Leaderboard Entry') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.leaderboard.approve') }}" id="approvalModalForm" method="post">
                @csrf
                <div class="modal-body" id="approve_form">
                    <input type="hidden" name="user_id" class="user_id" id="user_id">
                    <x-notice.general-notice :description="__(
                        'Notice: Enter the candidate position in the field below, or accept the automatically generated position of the candidate.',
                    )" />
                    <div>
                        <strong>Score:</strong>
                        <span class="score"></span>
                    </div>
                    <div>
                        <strong>Current Position: </strong>
                        <span class="current_position"></span>
                    </div>
                    <div>
                        <strong>Recommended Position: </strong>
                        <span class="position"></span>
                    </div>
                    <div>
                        <strong>Metrics:</strong>
                        <ul class="metrics"></ul>
                    </div>
                    <div class="form-group">
                        <label>Set New Position (1-20)</label>
                        <input type="number" name="position" class="form-control" min="1" max="20"
                            required id="position">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <x-btn.submit :title="__('Update')" :class="'btn btn-primary mt-4 pr-4 pl-4 admin_individual_settings_for_user'" />
                </div>
            </form>
        </div>
    </div>
</div>
