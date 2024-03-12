@extends(config('laravel-tickets.layouts'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .card-header {
     padding: var(--bs-card-cap-padding-y) var(--bs-card-cap-padding-x);
     margin-bottom: 0;
     color: white;
     background-color: rgb(29 82 134);
     border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);
 }
 .card-box {
    position: relative;
    color: #fff;
    padding: 20px 10px 40px;
    margin: 20px 0px;
}
.card-box:hover {
    text-decoration: none;
    color: #f1f1f1;
}
.card-box:hover .icon i {
    font-size: 100px;
    transition: 1s;
    -webkit-transition: 1s;
}
.card-box .inner {
    padding: 5px 10px 0 10px;
}
.card-box h3 {
    font-size: 27px;
    font-weight: bold;
    margin: 0 0 8px 0;
    white-space: nowrap;
    padding: 0;
    text-align: left;
}
.card-box p {
    font-size: 15px;
}
.card-box .icon {
    position: absolute;
    top: auto;
    bottom: 5px;
    right: 5px;
    z-index: 0;
    font-size: 72px;
    color: rgba(0, 0, 0, 0.15);
}
.card-box .card-box-footer {
    position: absolute;
    left: 0px;
    bottom: 0px;
    text-align: center;
    padding: 3px 0;
    color: rgba(255, 255, 255, 0.8);
    background: rgba(0, 0, 0, 0.1);
    width: 100%;
    text-decoration: none;
}
.card-box:hover .card-box-footer {
    background: rgba(0, 0, 0, 0.3);
}
.bg-blue {
    background-color: #00c0ef !important;
}
.bg-green {
    background-color: #00a65a !important;
}
.bg-orange {
    background-color: #f39c12 !important;
}
.bg-red {
    background-color: #d9534f !important;
}
#myTable > thead > tr >th{
    background: #6a7ecb;
    color: white;
}
 </style>
 
    <div class="card">
        <div class="card-header">
            @lang('Tickets')
        </div>
        <div class="card-body">
            @includeWhen(session()->has('message'), 'laravel-tickets::alert', ['type' => 'info', 'message' => session()->get('message')])
            {{-- @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer')) --}}
            {{-- <div class="container"> --}}
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card-box bg-blue">
                            <div class="inner">
                                <h3> {{count($tickets)}} </h3>
                                <p> Total Ticket Received </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-dropbox" aria-hidden="true"></i>
                            </div>
                            {{-- <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
            
                    
                    <div class="col-lg-3 col-sm-6">
                        <div class="card-box bg-orange">
                            <div class="inner">
                                <h3> {{$tickets->where('opener_id',null)->count()}} </h3>
                                <p> Ticket Not Assigned </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </div>
                            {{-- <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card-box bg-green">
                            <div class="inner">
                                <h3> {{$tickets->where('state', 'OPEN')->count()}} </h3>
                                <p> Ticket Open </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-hourglass-start" aria-hidden="true"></i>
                            </div>
                            {{-- <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card-box bg-red">
                            <div class="inner">
                                <h3> {{$tickets->where('state', 'CLOSED')->count()}} </h3>
                                <p> Ticket Closed </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-ticket"></i>
                            </div>
                            {{-- <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a> --}}
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
            {{-- @endif --}}
            <div class="table-responsive">
                <table class="table table-striped" id="myTable">
                    <thead class="th">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@lang('Ref No.')</th>
                        <th scope="col">@lang('Subject')</th>
                        <th scope="col">@lang('Priority')</th>
                        <th scope="col">@lang('State')</th>
                        <th scope="col">@lang('Created by')</th>
                        <th scope="col">@lang('Assigned To')</th>
                        <th scope="col">@lang('Last Update')</th>
                        <th scope="col">@lang('Created at')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tickets as $key=>$ticket)
                        <tr>
                            <th scope="row">{{ $key+1 }}</th>
                            <td>{{ $ticket->reference_no }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td style="font-weight:bold;color: white"><span class="badge bg-{{ $ticket->priority == 'HIGH' ? 'danger' : ($ticket->priority == 'MID' ? 'warning' : 'success') }}">@lang(ucfirst(strtolower($ticket->priority)))</span></td>
                            <td style="font-weight:bold;color: white;"><span class="badge bg-{{ $ticket->state == 'OPEN' ? 'success' : 'danger' }}">@lang(ucfirst(strtolower($ticket->state)))</span></td>
                            <td>{{ $ticket->user()->first()->name }}</td>
                            <td>@if($ticket->opener()->exists())<span class="badge bg-primary">{{ $ticket->opener()->exists()?$ticket->opener()->first()->name:"Not Assigned" }}</span>@else<span class="badge bg-danger">Not Assigned</span>@endif</td>
                            <td>{{ $ticket->updated_at ? $ticket->updated_at->format(config('laravel-tickets.datetime-format')) : trans('Not updated') }}</td>
                            <td>{{ $ticket->created_at ? $ticket->created_at->format(config('laravel-tickets.datetime-format')) : trans('Not created') }}</td>
                            <td >
                                <div style="display: flex;">
                                    @can('all-ticket')
                                    @if(!$ticket->opener()->exists())
                                    <div class="form-group" style="margin-top:10px;">
                                        <form method="post" action="{{ route('laravel-tickets.tickets.selfassign', compact('ticket')) }}">
                                            @csrf
                                            <button class="btn btn-block btn-warning">@lang('Self Assign')</button>
                                        </form>
                                    </div>
                                    @endif
                                
                                @endcan
                                @can('show-ticket')
                                @if(\request()->user()->hasRole('Ticket Admin') || $ticket->opener()->exists())
                                <div class="form-group ml-2" style="margin-top:10px;margin-left:10px;">
                                <a href="{{ route('laravel-tickets.tickets.show', compact('ticket')) }}"
                                   class="btn btn-primary">@lang('View')</a>
                                </div>
                                </div>
                                @endif
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="mt-2 d-flex justify-content-center">
                    {!! $tickets->links('pagination::bootstrap-4') !!}
                </div>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
        "columns": [
            { "width": "5%" }, 
            { "width": "5%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "10%" }, 
            { "width": "20%" },
        ]
    });
        });
    </script>
@endsection

