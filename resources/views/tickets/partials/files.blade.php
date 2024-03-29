<div style="height: 28.15vh; overflow-y: scroll">
    @php
    $flag_doc=false;
    @endphp
    @foreach ($messages_all as $message)
        @foreach($message->uploads()->get() as $upload)
        @php
        $flag_doc=true;
        @endphp
            <div class="card mt-2">
                <div class="card-body">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('laravel-tickets.tickets.download', ['ticket' => $ticket, 'ticketUpload' => $upload]) }}">{{ basename($upload->path) }}</a>
                                </div>
                                <div class="col-12 ">
                                                <span class="text-muted">
                                                   <strong> {{ $message->user->name }}</strong> {{ $upload->created_at->format(config('laravel-tickets.datetime-format')) }}
                                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
    @if($flag_doc==false)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row justify-content-between align-items-center">
                <div class="col-12">
                    <strong>No File Found</strong>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
