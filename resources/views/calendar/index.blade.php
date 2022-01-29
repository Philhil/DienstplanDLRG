@extends('_layouts.application')

@section('head')
    <link href="/plugins/fullcalendar/css/main.css" rel="stylesheet" />
    <script src="/plugins/fullcalendar/js/main.min.js"></script>
    <script src="/plugins/fullcalendar/locales/de.js"></script>
@endsection

@section('content')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Kalender</h2>
                @if($isAdmin || $isTrainingEditor)
                <ul class="header-dropdown m-r--5">
                    <a href="{{action('CalendarController@create')}}">
                        <button type="button" class="btn btn-success waves-effect">
                            <i class="material-icons">playlist_add</i>
                            <span>Hinzufügen</span>
                        </button>
                    </a>
                </ul>
                @endif
            </div>
            <div class="container">
                <div id='calendar'></div>
            </div>
            <br>
        </div>
    </div>

@endsection

@section('post_body')
    <script>
        $(document).ready(function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'de',
                @if(Browser::isDesktop())
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridDay,listMonth',
                },
                @else
                initialView: 'listMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'listMonth',
                },
                @endif
                weekNumbers: true,
                firstDay: 1,
                //navLinks: true, // can click day/week names to navigate views
                dayMaxEvents: true, // allow "more" link when too many events
                //editable: true,
                eventClick: function(event) {
                    if(event.event.id) {
                        switch (event.event.groupId) {
                            case "calendar":
                                window.location.href='/calendar/'+event.event.id+'/edit';
                                break;
                        }
                    }
                },
                eventSources: [
                    {
                        //calendar events
                        events: [
                            @forEach($calendars as $calendar)
                            {
                                id: {{$calendar->id}},
                                groupId: "calendar",
                                title: '{{$calendar->title}} @if(!empty($calendar->verantwortlicher)) ({{$calendar->verantwortlicher}}) @endif',
                                start: '{{\Carbon\Carbon::parse($calendar->date)->toIso8601String()}}',
                                @if(!empty($calendar->dateEnd))
                                end: '{{\Carbon\Carbon::parse($calendar->dateEnd)->toIso8601String()}}',
                                @endif
                            },
                            @endforeach
                        ],
                    },
                    {
                        //training events
                        events: [
                            @forEach($trainings as $training)
                            {
                                id : {{$training->id}},
                                groupId: "training",
                                title  : '{{$training->title}}',
                                start  : '{{\Carbon\Carbon::parse($training->date)->toIso8601String()}}',
                                @if(!empty($training->dateEnd))
                                end: '{{\Carbon\Carbon::parse($training->dateEnd)->toIso8601String()}}',
                                @endif
                            },
                            @endforeach
                            ],
                        color: '#607D8B',
                    },
                    {
                        //services of user
                        events: [
                            @forEach($services_of_user as $service)
                            {
                                id : {{$service->id}},
                                groupId: "service",
                                title  : 'Wachdienst {{$service->location}} @if(!empty($service->comment)) ({{$service->comment}}) @endif',
                                start  : '{{\Carbon\Carbon::parse($service->date)->toIso8601String()}}',
                                @if(!empty($service->dateEnd))
                                end: '{{\Carbon\Carbon::parse($service->dateEnd)->toIso8601String()}}',
                                @endif
                            },
                            @endforeach
                        ],
                        color: '#F44336',
                        textColor: 'black'
                    },
                    {
                        //all services where user is not participating
                        events: [
                                @forEach($services_without_user as $service)
                            {
                                id : {{$service->id}},
                                groupId: "service",
                                title  : 'Wachdienst {{$service->location}} @if(!empty($service->comment)) ({{$service->comment}}) @endif',
                                start  : '{{\Carbon\Carbon::parse($service->date)->toIso8601String()}}',
                                @if(!empty($service->dateEnd))
                                end: '{{\Carbon\Carbon::parse($service->dateEnd)->toIso8601String()}}',
                                @endif
                                allDay: true
                            },
                            @endforeach
                        ],
                        color: '#c0c0c0',
                        textColor: 'black'
                    },
                ]
            });
            calendar.render();
        });
    </script>
@endsection