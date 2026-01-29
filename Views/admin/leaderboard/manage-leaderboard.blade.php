@extends('backend.layout.master')
@section('title', __('Freelancers Leaderboard'))
@section('style')
    <x-select2.select2-css />
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Leaderboard') }}</h4>

                            {{-- View Leaderboard --}}
                            <a href="{{ route('user.leaderboard.index') }}" target="_blank" class="btn btn-secondary btn-sm"><i
                                    class="fa-regular fa-eye"></i>&nbsp;{{ __('View Leaderboard') }}</a>
                        </div>
                        @php
                            $existing = $candidates->isNotEmpty();
                        @endphp

                        <div class="customMarkup__single__inner mt-4">
                            @if (!$existing)
                                <x-notice.general-notice :class="'mb-5'" :description="__(
                                    'Notice: To generate report and fill up leaderboard table, click on \'Generate Leaderboard Candidates\'.',
                                )" />
                            @endif

                            <x-validation.error />
                            <div class="error-message"></div>
                            <form method="POST" action="{{ route('admin.leaderboard.generate') }}">
                                @csrf
                                <button
                                    class="btn btn-primary mb-3">{{ $existing ? __('Refresh Candidates') : __('Generate Leaderboard Candidates') }}</button>
                            </form>
                            <div class="bulk-delete-wrapper mt-3">
                                <div class="select-box-wrap">
                                    <select name="bulk_option" id="bulk_option" class="me-1">
                                        <option value="">{{ __('Select Bulk Action') }}</option>
                                        <option value="approve_rank">{{ __('Approve New Ranks') }}</option>
                                        <option value="remove">{{ __('Remove Users from Rank List') }}</option>
                                    </select>
                                    <button class="btn btn-primary btn-md" id="bulk_apply_btn">{{ __('Apply') }}</button>
                                </div>
                            </div>

                            <div class="custom_table mt-3 style-04 search_result">
                                {{-- Table is here --}}
                                <table class="table table-striped table_activation">
                                    <thead>
                                        <tr>
                                            <th class="no-sort">
                                                <div class="mark-all-checkbox">
                                                    <input type="checkbox" class="all-checkbox">
                                                </div>
                                            </th>
                                            <th>Name</th>
                                            <th>Score</th>
                                            <th>Metrics</th>
                                            <th>Current Rank </th>
                                            <th>New Rank</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($existing)
                                            @foreach ($candidates as $idx => $c)
                                                @php
                                                    $current_position = $c->entry->position ?? null;
                                                    $new_position = $idx + 1;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <x-bulk-action.bulk-delete-checkbox :id="json_encode([
                                                            'id' => $c->user->id,
                                                            'position' => $new_position,
                                                        ])" />
                                                    </td>
                                                    <td>{{ ucwords(optional($c->user)->fullname ?? ' ') }}
                                                        #{{ number_format($c->user_id) }}
                                                    </td>
                                                    <td>{{ number_format($c->score, 2) }}</td>
                                                    <td>
                                                        @foreach ($c->metrics as $key => $m)
                                                            <div><strong>{{ $m['label'] ?? $key }}:</strong>
                                                                {{ $m['value'] ?? 0 }}</div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ $current_position ? \Illuminate\Support\Number::ordinal($current_position) : 'No Rank' }}
                                                        @if ($c->entry && $current_position == $new_position)
                                                            &nbsp;<span>Approved ✅</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $new_position ? \Illuminate\Support\Number::ordinal($new_position) : '' }}&nbsp;&nbsp;
                                                        @if (!$current_position)
                                                        @elseif ($new_position > $current_position)
                                                            {{-- decreased --}}
                                                            <span>
                                                                <i class="fa-solid fa-arrow-down text-danger"></i>
                                                                <small>lower</small>
                                                            </span>
                                                        @elseif($new_position < $current_position)
                                                            {{-- Increased --}}
                                                            <span>
                                                                <i class="fa-solid fa-arrow-up text-success"></i>
                                                                <small>higher</small>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($c->entry && $current_position == $new_position)
                                                            <form action="{{ route('admin.leaderboard.remove') }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="user_id"
                                                                    value="{{ $c->user_id }}">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger">Remove</button>
                                                            </form>
                                                        @else
                                                            <a data-bs-toggle="modal" data-bs-target="#approveFormModal"
                                                                data-user_id="{{ number_format($c->user_id) }}"
                                                                data-score="{{ number_format($c->score, 2) }}"
                                                                data-metrics="{{ json_encode($c->metrics) }}"
                                                                data-current_position="{{ !!$c->entry ? json_encode(['value' => $current_position, 'label' => \Illuminate\Support\Number::ordinal($current_position)]) : '' }}"
                                                                data-position="{{ json_encode(['value' => $new_position, 'label' => \Illuminate\Support\Number::ordinal($new_position)]) }}"
                                                                class="btn btn-sm btn-success approve_form">
                                                                {{ __('Approve New Rank') }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <x-table.no-data-found :colspan="'7'" :class="'text-danger text-center py-5'" />
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('rank::components.leaderboard.leaderboard-approve-modal')
@endsection

@section('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $(document).on('click', '.approve_form', function() {
                    let user_id = $(this).data('user_id');
                    let score = $(this).data('score');
                    let metrics = $(this).data('metrics') ?? {};
                    let current_position = $(this).data('current_position') ?? 'None'
                    let position = $(this).data('position') ?? {};
                    let metrics_html = '';
                    Object.keys(metrics).map((item, i) => {
                        const data = metrics[item];
                        metrics_html +=
                            `<li>${data?.label??''}: ${data?.value??0} (weight: ${data?.weight??1})</li>`
                    })
                    $('#approve_form .user_id').val(user_id);
                    $('#approve_form .score').text(score);
                    $('#approve_form .metrics').html(metrics_html);
                    $('#approve_form .current_position').html(current_position?.label);
                    $('#approve_form .position').html(position?.label);
                    $('#approve_form #position').val(position?.value);

                });

                $(document).on('click', '#bulk_apply_btn', function(e) {
                    e.preventDefault();
                    let bulkOption = $('#bulk_option').val();
                    let allCheckbox = $('.bulk-checkbox:checked');
                    let allIds = [];
                    allCheckbox.each(function(index, value) {
                        allIds.push(JSON.parse($(this).val() ?? {}));
                    });
                    let alertContainer = $(".error-message");
                    alertContainer.html('');
                    if (allIds != '' && bulkOption != '') {
                        if (bulkOption == 'delete') {
                            $(this).html(
                                '<i class="fas fa-spinner fa-spin mr-1"></i>{{ __('Deleting') }}');
                        } else {
                            $(this).html(
                                '<i class="fas fa-spinner fa-spin mr-1"></i>{{ __('Processing') }}'
                            );
                        }
                        $.ajax({
                            'type': "POST",
                            'url': "{{ route('admin.leaderboard.bulk_actions') }}",
                            'data': {
                                _token: "{{ csrf_token() }}",
                                payloads: allIds,
                                action: bulkOption
                            },
                            success: function(data) {
                                if (data.message) {
                                    alertContainer.html(
                                        '<div class="alert alert-success"><p>' + data
                                        .message + '</p></div>');
                                }
                                setTimeout(function() {
                                    location.reload();
                                }, 2500);
                            },
                            error: function(err) {
                                let errors = err.responseJSON;
                                alertContainer.html(
                                    '<div class="alert alert-danger"></div>');
                                if (errors?.message) {
                                    alertContainer.find('.alert.alert-danger').append(
                                        '<p>' + errors.message + '</p>');
                                } else if (errors?.errors) {
                                    $.each(errors.errors, function(index, value) {
                                        alertContainer.find('.alert.alert-danger')
                                            .append(
                                                '<p>' + value + '</p>');
                                    });

                                }
                            },
                            complete: function() {
                                $('#bulk_apply_btn').html('{{ __('Apply') }}');
                            }
                        });
                    }
                });

                $('.all-checkbox').on('change', function(e) {
                    e.preventDefault();
                    let value = $('.all-checkbox').is(':checked');
                    let allChek = $(this).parent().parent().parent().parent().parent().find(
                        '.bulk-checkbox');
                    if (value == true) {
                        allChek.prop('checked', true);
                    } else {
                        allChek.prop('checked', false);
                    }
                });
            })
        }(jQuery))
    </script>
@endsection
