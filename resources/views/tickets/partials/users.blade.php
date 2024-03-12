<div class="card" style="height: 35.15vh; overflow-y: scroll">
   
    @if(count($messages->whereNotIn('user_id', [$ticket->user_id])->orderBy('created_at', 'asc')->get()->unique('user_id'))>0)
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <strong>Users Commented in this ticket</strong>
            </div>
        </div>
    </div>
    @foreach ($messages->whereNotIn('user_id', [$ticket->user_id])->orderBy('created_at', 'asc')->get()->unique('user_id') as $message)
        <div class="card mt-2">
            <div class="card-body">
                
                <div class="row justify-content-between align-items-center">
                    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-2">
                        <span style='font-size:50px;'>&#128100;</span>

                    </div>
                    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-10">
                        <div class="row">
                            <div class="col-12">
                                {{ $message->user->name }}
                            </div>
                            <div class="col-12 ">
                                                <span
                                                    class="text-muted">{{ $message->created_at->format(config('laravel-tickets.datetime-format')) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @else
    <div class="card mt-2">
        <div class="card-body">
            <div class="row justify-content-between align-items-center">
                <div class="col-12">
                    <strong>No User Found</strong>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
