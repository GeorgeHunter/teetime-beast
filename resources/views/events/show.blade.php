@extends('layouts.app')

@section('content')

    <div class="bg-soft p-xs-y-7 full-height">
        <div class="container">
            <div class="row m-xs-b-5">
                <div class="col col-md-6 col-md-offset-3 m-xs-b-4 m-lg-b-0">
                    <div class="card">
                        <div class="card-section">
                            <div class="m-xs-b-5">
                                <h1 class="wt-bold text-ellipsis">{{ $event->title }}</h1>
                                <span class="wt-medium text-ellipsis">{{ $event->subtitle }}</span>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-left">
                                        @icon(calendar)
                                    </div>
                                    <div class="media-body p-xs-l-2">
                                        <span class="wt-medium">{{ $event->formatted_date }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-left">
                                        @icon(time)
                                    </div>
                                    <div class="media-body p-xs-l-2">
                                        <span class="wt-medium block">First tee time {{ $event->formatted_start_time }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-left">
                                        @icon(gbp)
                                    </div>
                                    <div class="media-body p-xs-l-2">
                                        <span class="wt-medium block">{{ $event->entry_fee_in_pounds }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark-soft m-xs-b-5">
                                <div class="media-object">
                                    <div class="media-left">
                                        @icon(home)
                                    </div>
                                    <div class="media-body p-xs-l-2">
                                        <h3 class="text-base wt-medium text-dark">{{ $event->course }}</h3>
                                        {{ $event->course_address }}<br>
                                        {{ $event->city }}, {{ $event->county }} {{ $event->postcode }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-dark-soft">
                                <div class="media-object">
                                    <div class="media-left">
                                        @icon(info-sign)
                                    </div>
                                    <div class="media-body p-xs-l-2">
                                        <h3 class="text-base wt-medium text-dark">Additional Information</h3>
                                        <p>{{ $event->additional_information}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-t">
                            <div class="card-section">
                                <ticket-checkout
                                        :event-id="{{ $event->id }}"
                                        event-title="{{ $event->title }}"
                                        :price="{{ $event->entry_fee }}"
                                ></ticket-checkout>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center text-dark-soft wt-medium">
                <p>Powered by TeeTime Beast</p>
            </div>
        </div>
    </div>

@stop

@push('beforeScripts')
<script src="https://checkout.stripe.com/checkout.js"></script>
@endpush

