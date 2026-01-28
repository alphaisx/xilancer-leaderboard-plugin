@extends('backend.layout.master')
@section('title', __('Freelancers & Clients Ambassadors Management'))
@section('style')
    <x-select2.select2-css />
    <style>
        .ambassador-data i {
            text-decoration: underline;
            text-decoration-style: dotted;
            text-underline-offset: 2px;
        }

        .alert-warning {
            border-color: #f2f2f2;
            border-left: 3px solid #e0a800;
            background-color: #f2f2f2;
            color: #333;
            border-radius: 0;
            padding: 5px;
        }

        .alert-success {
            border-color: #f2f2f2;
            border-left: 3px solid #319a31;
            background-color: #f2f2f2;
            color: #333;
            border-radius: 0;
            padding: 5px;
        }

        .alert-danger {
            border-color: #f2f2f2;
            border-left: 3px solid #dd0000;
            background-color: #f2f2f2;
            color: #333;
            border-radius: 0;
            padding: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Manage Ambassadors') }}</h4>

                            {{-- View Leaderboard --}}
                            <a href="{{ route('user.leaderboard.index') }}" target="_blank" class="btn btn-secondary btn-sm"><i
                                    class="fa-regular fa-eye"></i></a>
                        </div>
                        <div class="customMarkup__single__inner mt-4">

                            @php
                                $existing = isset($ambassadors) && $ambassadors->count() > 0;
                            @endphp
                            <x-validation.error />
                            <div class="error-message"></div>
                            <div class="bulk-delete-wrapper mt-3">
                                <div class="select-box-wrap">
                                    <select name="bulk_option" id="bulk_option" class="me-1">
                                        <option value="">{{ __('Select Bulk Action') }}</option>
                                        <option value="approve_rank">{{ __('Approve All') }}</option>
                                        <option value="remove">{{ __('Remove All') }}</option>
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
                                            <th>Personal Information</th>
                                            <th>School Information</th>
                                            <th>Reasons</th>
                                            <th>Active</th>
                                            <th>Community Notes</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="ambassador-data">
                                        @if ($existing)
                                            @foreach ($ambassadors as $ambassador)
                                                @php
                                                    $total_completed_orders =
                                                        (optional($ambassador->user->freelancer_orders)->count() ?? 0) +
                                                        (optional($ambassador->user->user_complete_orders)->count() ??
                                                            0);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="mark-checkbox">
                                                            <input type="checkbox" class="bulk-checkbox"
                                                                value="{{ $ambassador->id }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="user-info">
                                                            <p class="name"> Name:
                                                                <i>{{ $ambassador->fullname ?? 'N/A' }}</i>
                                                            </p>
                                                            <p class="email">Email:
                                                                <i>{{ $ambassador->email ?? 'N/A' }}</i>
                                                            </p>
                                                            <p>
                                                                Username:
                                                                <i>{{ $ambassador->user->username ?? 'N/A' }}</i>
                                                            </p>
                                                            <p class="phone">Phone:
                                                                <i>{{ $ambassador->phone ?? 'N/A' }}</i>
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <p>Name: <i>{{ $ambassador->school ?? 'N/A' }}</i> </p>
                                                            <p>Info: <i>{{ strtoupper($ambassador->level ?? 'N/A') }},
                                                                    {{ $ambassador->course ?? 'N/A' }},
                                                                    {{ $ambassador->address ?? 'N/A' }}</i> </p>
                                                        </div>
                                                    </td>
                                                    <td>{{ substr($ambassador->reason ?? 'N/A', 0, 30) . '...' }}</td>
                                                    <td class="text-nowrap">
                                                        @if ($ambassador->is_ambassador)
                                                            <span class="alert alert-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="alert alert-warning">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $ambassador->community_notes ?? 'N/A' }}</td>
                                                    <td>
                                                        <x-status.table.select-action :title="__('Select Action')" />
                                                        <ul class="dropdown-menu status_dropdown__list">
                                                            <li>
                                                                <a data-bs-toggle="modal"
                                                                    data-bs-target="#ambassadorDetailsViewModal"
                                                                    data-info="{{ json_encode([
                                                                        'ambassadorid' => [
                                                                            'attr' => 'val',
                                                                            'value' => $ambassador->id,
                                                                        ],
                                                                        'profileimage' => [
                                                                            'attr' => 'src',
                                                                            'value' => fetch_image($ambassador->user),
                                                                        ],
                                                                        'fullname' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->user->fullname ?? 'N/A',
                                                                        ],
                                                                        'username' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->user->username ?? 'N/A',
                                                                        ],
                                                                        'email' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->user->email ?? 'N/A',
                                                                        ],
                                                                        'phone' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->user->phone ?? 'N/A',
                                                                        ],
                                                                        'completedorders' => [
                                                                            'attr' => 'text',
                                                                            'value' => $total_completed_orders . ' Order' . ($total_completed_orders > 1 ? 's' : ''),
                                                                        ],
                                                                        'school' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->school ?? 'N/A',
                                                                        ],
                                                                        'level' => [
                                                                            'attr' => 'text',
                                                                            'value' => strtoupper($ambassador->level ?? 'N/A'),
                                                                        ],
                                                                        'course' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->course ?? 'N/A',
                                                                        ],
                                                                        'address' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->address ?? 'N/A',
                                                                        ],
                                                                        'reason' => [
                                                                            'attr' => 'text',
                                                                            'value' => $ambassador->reason ?? 'N/A',
                                                                        ],
                                                                    ]) }}"
                                                                    class="btn dropdown-item status_dropdown__list__link view_ambassador_details">
                                                                    {{ __('View Details') }}
                                                                </a>
                                                            </li>
                                                            <li class="status_dropdown__item">
                                                                <x-status.table.status-change :title="__('Approve as Ambassador')"
                                                                    :url="route(
                                                                        'admin.ambassador.approve',
                                                                        $ambassador->id,
                                                                    )" />
                                                            </li>
                                                            <li class="status_dropdown__item">
                                                                <x-popup.delete-popup :title="__('Delete Record')" :url="route(
                                                                    'admin.ambassador.delete',
                                                                    $ambassador->id,
                                                                )" />
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <x-table.no-data-found :colspan="'7'" :class="'text-danger text-center py-5'" />
                                        @endif
                                    </tbody>
                                </table>
                                <x-pagination.laravel-paginate :allData="$ambassadors" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('rank::components.ambassador.ambassador-view-modal')
@endsection

@section('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                // View Ambassador Details
                $(document).on('click', '.view_ambassador_details', function(e) {
                    e.preventDefault();
                    let info = $(this).data('info');
                    Object.keys(info).forEach(function(key) {
                        let element = $('#view_ambassador_details .' + key);
                        let attr = info[key]['attr'];
                        let value = info[key]['value'];
                        if (attr === 'text') {
                            element.text(value);
                        } else if (attr === 'src') {
                            element.attr('src', value);
                        } else if (attr === 'val') {
                            element.val(value);
                        }
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
