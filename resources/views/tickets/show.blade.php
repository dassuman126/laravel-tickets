@extends(config('laravel-tickets.layouts'))

@section('content')
<style>
   .card-header {
    padding: var(--bs-card-cap-padding-y) var(--bs-card-cap-padding-x);
    margin-bottom: 0;
    color: white;
    background-color: rgb(29 82 134);
    border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);
}
.p-8{
    padding: 8px;
    border: 1px solid #f2e9f6;
}

.input-wrapper {
    position: relative;
  border: 1px solid rgb(29 82 134);
  border-radius: 4px;
  font-size: .9rem;
  display: flex;
  flex: 1;
  overflow: hidden;
  padding: 0 6px 0 12px;
  justify-content: space-between;
  margin: 0 8px;
  background-color: none;
}

.emoji-btn {
  border: none;
  background-color: transparent;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 4px;
  color: #ffca3e;
}

.chat-send-btn {
  height: 32px;
  color: #fff;
  background-color: rgb(60, 219, 25);
  border: none;
  border-radius: 4px;
  padding: 0 32px 0 10px;
  font-size: 12px;
  background-position: center right 8px;
  background-repeat: no-repeat;
  background-size: 14px;
  line-height: 16px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cdefs/%3E%3Cpath fill='%23fff' d='M481.508 210.336L68.414 38.926c-17.403-7.222-37.064-4.045-51.309 8.287C2.86 59.547-3.098 78.551 1.558 96.808L38.327 241h180.026c8.284 0 15.001 6.716 15.001 15.001 0 8.284-6.716 15.001-15.001 15.001H38.327L1.558 415.193c-4.656 18.258 1.301 37.262 15.547 49.595 14.274 12.357 33.937 15.495 51.31 8.287l413.094-171.409C500.317 293.862 512 276.364 512 256.001s-11.683-37.862-30.492-45.665z'/%3E%3C/svg%3E");
}

.chat-attachment-btn {
    border: none;
    padding: 7px;
    background-color: #ffffff;
    color: var(--text-light);
    display: flex;
    align-items: center;
    opacity: 1.7;
    position: absolute;
    top: 0px;
    right: 0px;
    border-radius: 3px;
    box-shadow: 2px -3px 5px 2px #be9f9f;
}
.chat-input {
  border: none;
  outline: none;
  height: 100px;
  flex: 1;
  margin-right: 4px;
  background-color: var(--chat-input);
  color: var(--text-dark);

  &:placeholder {
    color: #373737;
    font-size: 12px;
  }

  &-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--box-shadow);
    margin-top: auto;
    border-radius: 6px;
    padding: 12px;
    background-color: var(--chat-input);
  }
}
.chat-input-wrapper{
    display: flex;
    position: relative;
}
/* width */
::-webkit-scrollbar {
  width: 4px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 10px;
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: #1d5286; 
  border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #1d5286; 
}
label{
    color:#373737;
    font-weight: bold;
    margin-bottom: 1px;
}
.chosen-container-multi{
    width: 100% !important;
}
.content_show {
        height: 50px;
        overflow: hidden;
        transition: height 0.3s ease;
        border-left: 1px solid #ccc;
    }
    #toggleButton{
        border: none;
    color: black;
    display: none;
    }
</style>
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-8">
            @includeWhen(session()->has('message'), 'laravel-tickets::alert', ['type' => 'info', 'message' => session()->get('message')])
            @if (($ticket->user_id==\Auth::user()->id) || $ticket->opener_id==\Auth::user()->id)
            @if (($ticket->user_id==\Auth::user()->id) || $ticket->state !== 'CLOSED')

                <div class="card mb-3">
                    <div class="card-header">
                        @lang('Ticket answer')
                    </div>
                    <div class="card-body">
                        
                        <form method="post" action="{{ route('laravel-tickets.tickets.message', compact('ticket')) }}"
                              @if (config('laravel-tickets.files')) enctype="multipart/form-data" @endif>
                            @csrf
                            <div class="col-md-12 mt-2">
                                <label>@lang('Ticket Reply'):</label>
                                <textarea class="chat-input1 form-control @error('message') is-invalid @enderror" placeholder="Enter your message here" name="message" id="editor"></textarea>
                                {{-- <div class="chat-input-wrapper">
                                   
                                    <div class="input-wrapper">
                                      <textarea class="chat-input @error('message') is-invalid @enderror" placeholder="Enter your message here" name="message" id="editor"></textarea>
                                      @if (config('laravel-tickets.files'))
                                      <input type="file" id="files" name="files[]" multiple style="display: none;" class="@error('files') is-invalid @enderror {{ empty($errors->get('files.*'))?'':'is-invalid' }}">
                                        <button class="chat-attachment-btn" id="attachment-button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-paperclip" viewBox="0 0 24 24">
                                                <defs></defs>
                                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                    <button class="chat-send-btn btn btn-primary">Send</button>
                                  </div> --}}
                                  @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                  
                            </div>
                            @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer') )
                        <div class="col-md-6 mt-2">
                            <div class="form-group">
                                <label>@lang('Reference Ticket'):</label>
                                <select class="form-control chosen-select @error('reference') is-invalid @enderror"
                                        name="reference[]" multiple>
                                    @if (config('laravel-tickets.references-nullable'))
                                        <option value="">@lang('No reference')</option>
                                    @endif
                                    {{-- @foreach (use App/Models/Tickets::class as $modelClass) --}}
                                    @foreach (\App\Models\Ticket::all() as $model)
                                    <option value="{{ $model->id }}"
                                        @if (old('reference') == $model->id)
                                            selected
                                        @endif>{{ $model->reference_no }}</option>
                                @endforeach
                                    {{-- @endforeach --}}
                                </select>
                                @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Upload File'):</label>
                                <input type="file" id="files" name="files[]" multiple class="form-control @error('files') is-invalid @enderror {{ empty($errors->get('files.*'))?'':'is-invalid' }}">
                                @error('files')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-2 mt-2">
                                <button class="chat-send-btn btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            @endif
            @php($messages_all = $messages->get())
            @php($messagesPagination = $messages->paginate(4))
            @php($messagesPagination->pop())
            @foreach ($messagesPagination as $message)
                <div class="card @if (! $loop->first) mt-2 @endif">
                    <div class="card-header">
                        <div class="row">
                            
                            <div class="col">
                                <strong>Created By:</strong> {{ $message->user()->exists() ? $message->user->name : trans('Deleted user') }}
                            
                                ({{ $message->user()->exists() ? $message->user->email : trans('Deleted user') }})
                            </div>
                            <div class="col-auto">
                                <strong>Created At: </strong>{{ $message->created_at->format(config('laravel-tickets.datetime-format')) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            {!! $message->message !!}
                        </div>
                    </div>
                    @if (count(explode(",",$message->ticket_reference)) > 0)
                        <div class="card-body border-top p-1">
                            <div class="d-flex mt-1 mb-2 pr-2 pl-2">
                                <div class="m-1 mt-0 mb-0">
                                    <label>Reference Ticket:&nbsp;&nbsp;</label>
                                </div>
                                @foreach (explode(",",$message->ticket_reference) as $ticketUpload)
                                    <div class="m-1 mt-0 mb-0">
                                        <?php
                                        $ticket_ref1=\App\Models\Ticket::find($ticketUpload);
                                        ?>
                                        <a href="{{ route('laravel-tickets.tickets.show', $ticket_ref1) }}"
                                   class="btn btn-warning btn-sm"> <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-paperclip" viewBox="0 0 24 24">
                                    <defs></defs>
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path>
                                </svg>
                                    {{$ticket_ref1->reference_no}}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($message->uploads()->count() > 0)
                        <div class="card-body border-top p-1">
                            <div class="d-flex mt-1 mb-2 pr-2 pl-2">
                                <div class="m-1 mt-0 mb-0">
                                    <label>Reference Document:&nbsp;&nbsp;</label>
                                </div>
                                @foreach ($message->uploads()->get() as $ticketUpload)
                                    <div class="m-1 mt-0 mb-0">
                                        <a
                                            href="{{ route('laravel-tickets.tickets.download', compact('ticket', 'ticketUpload')) }}"
                                        >{{ basename($ticketUpload->path) }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach

            <div class="mt-2 d-flex justify-content-center">
                {!! $messagesPagination->links('pagination::bootstrap-4') !!}
            </div>
            @if ($ticket->state === 'CLOSED')
            <div class="card mt-2">
                <div class="card-header bg-danger">
                    <div class="row">
                        
                        <div class="col">
                            <strong>Closed By:</strong> {{ $ticket->user()->exists() ? $ticket->user->name : trans('Deleted user') }}
                        
                            ({{ $ticket->user()->exists() ? $ticket->user->email : trans('Deleted user') }})
                        </div>
                        <div class="col-auto">
                            <strong>Closed At: </strong>{{ $ticket->updated_at->format(config('laravel-tickets.datetime-format')) }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <label><strong>@lang('Ticket Closing Remarks') :</strong></label>
                    <div>
                        {!! $ticket->close_message !!}
                    </div>
                </div>
                @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer') )
                
                <div class="card-body">
                    <label><strong>@lang('Ticket Reslove Remarks') :</strong></label>
                    <div>
                        {!! $ticket->developer_message !!}
                    </div>
                </div>
                @endif
                @if (count(explode(",",$ticket->ticket_reference)) > 0)
                    <div class="card-body border-top p-1">
                        <div class="d-flex mt-1 mb-2 pr-2 pl-2">
                            <div class="m-1 mt-0 mb-0">
                                <label>Reference Ticket:&nbsp;&nbsp;</label>
                            </div>
                            @foreach (explode(",",$ticket->ticket_reference) as $ticketUpload)
                            <div class="m-1 mt-0 mb-0">
                                <?php
                                $ticket_ref=\App\Models\Ticket::find($ticketUpload);
                                ?>
                                <a href="{{ route('laravel-tickets.tickets.show', $ticket_ref) }}"
                           class="btn btn-warning btn-sm"> <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-paperclip" viewBox="0 0 24 24">
                            <defs></defs>
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path>
                        </svg>
                            {{$ticket_ref->reference_no}}</a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endif
               

            </div>
            @endif
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    @lang('Ticket overview')
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 p-8">
                        <div class="form-group">
                            <label class="text-warning">@lang('Reference No'):<strong> {{$ticket->reference_no}}</strong></label>
                        </div>
                        </div>
                        <div class="col-md-12 p-8">
                            <div class="form-group">
                                <label class="text-primary">@lang('Subject'):<strong> {{$ticket->subject}}</strong></label>
                            </div>
                        </div>
                        <div class="col-md-12 p-8">
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="text-dark">@lang('Message'):
                                </div>
                                <div class="col-md-12 content_show">
                                    <strong> {!!$ticket->messages->first()->message!!}</strong></label>
                                </div>
                                <button onclick="expandContent()" id="toggleButton">Show All</button>
                            </div>
                        </div>
                        <div class="col-md-12 p-8">
                            <div class="form-group">
                                <label class="text-success">@lang('Created By'):<strong> {{$ticket->messages->first()->user()->first()->name}}</strong><small>({{$ticket->messages->first()->user()->first()->email}})</small></label>
                            </div>
                        </div>
                        <div class="col-md-6 p-8">
                            <div class="form-group">
                                <label class="text-info">@lang('Created At'):<strong style="font-size: 12px;"> {{$ticket->messages->first()->created_at->format(config('laravel-tickets.datetime-format'))}}</strong></label>
                            </div>
                        </div>
                        @if (config('laravel-tickets.category') && $ticket->category()->exists())
                        <div class="col-md-4 p-8">
                           
                            <div class="form-group">
                                <label>@lang('Category'):<strong>{{ $ticket->category()->first()->translation }}</strong></label>
                                
                            </div>
                        
                        </div>
                        @endif
                        {{-- <div class="col-md-6">
                            @if (config('laravel-tickets.references') && $ticket->reference()->exists())
                            <div class="form-group">
                                <label>@lang('Reference'):</label>
                                @php($referenceable = $ticket->reference->referenceable)
                                <input class="form-control" type="text"
                                       value="{{ $referenceable->toReference() }}" disabled>
                            </div>
                            @endif 
                        </div> --}}
                        <div class="col-md-6 p-8">
                            <div class="form-group">
                                <label>@lang('State'): <strong style="color: {{ $ticket->state == 'OPEN' ? 'green' : 'red' }}">@lang(ucfirst(strtolower($ticket->state)))</strong></label>
                            </div>
                            </div>
                            @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer'))
                        <div class="col-md-6 p-8">
                        <div class="form-group">
                          
                        <label>@lang('Priority'): <strong style="color: {{ $ticket->priority == 'HIGH' ? 'red' : ($ticket->priority == 'MID' ? 'orange' : 'green') }}">@lang(ucfirst(strtolower($ticket->priority)))</strong></label>
                        </div>
                        
                        </div>
                        <div class="col-md-6 p-8">
                            <div class="form-group">
                              
                            <label>@lang('Assigned To'): <strong >{{ $ticket->opener()->exists()?$ticket->opener()->first()->name:"Not Assigned" }}</strong></label>
                            </div>
                            
                            </div>
                        @endif
                        @can('ticket-assign')
                        <form method="post" action="{{ route('laravel-tickets.tickets.assign', compact('ticket')) }}" @if (config('laravel-tickets.files')) enctype="multipart/form-data" @endif>
                            @csrf
                            <input type="hidden" id="ticket_id" name="ticket_id" value="{{$ticket->id}}">
                        
                            <div class="card mt-2">
                                <div class="card-header">
                                    <div class="row">
                                        <strong>Assign Users</strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>@lang('Priority')</label>
                                                <select class="form-control @error('priority') is-invalid @enderror" name="priority" required>
                                                    @foreach (config('laravel-tickets.priorities') as $priority)
                    
                                                        <option value="{{ $priority }}" @if (old('priority') === $priority)
                                                        selected
                                                            @endif>@lang(ucfirst(strtolower($priority)))</option>
                                                    @endforeach
                                                </select>
                                                @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
                                            <div class="form-group">
                                                <label><strong>@lang('User') :</strong></label>
                                                <select class="form-control @error('category_id') is-invalid @enderror"
                                                        name="users_id" required>
                                                        <option value="">Select User</option>
                                                    @foreach (App\Models\User::all() as $ticketCategory)
                                                        <option value="{{ $ticketCategory->id }}" @if($ticket->opener_id==$ticketCategory->id) selected @endif>@lang($ticketCategory->name)</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                            <button class="btn btn-block btn-info mt-10" style="margin-top: 20px;">@lang('Assign')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endcan
                    </div>
                   
                    
                    
                    
                    
                    @if ($ticket->state !== 'CLOSED')
                    <div class="form-group" style="margin-top:10px;">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                            @lang('Close ticket')
                          </button>
                          <!-- Modal -->
                            <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Close ticket</h5>
                                   
                                    </div>
                                    <form method="post" action="{{ route('laravel-tickets.tickets.close', compact('ticket')) }}">
                                    <div class="modal-body">
                                            @csrf
                                      <div class="row">
                                      <div class="col-md-12">
                                        <label><strong>@lang('Closing Remarks') :</strong></label>
                                        <textarea class="chat-input1 form-control @error('message') is-invalid @enderror" placeholder="Enter your message here" name="close_message" id="editor1"></textarea>
                                      </div>
                                      @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer') )
                                      <div class="col-6">
                                          <div class="form-group">
                                              <label>@lang('Reference')</label>
                                              <select class="form-control chosen-select1 @error('reference') is-invalid @enderror"
                                                      name="reference[]" multiple>
                                                  @if (config('laravel-tickets.references-nullable'))
                                                      <option value="">@lang('No reference')</option>
                                                  @endif
                                                  {{-- @foreach (use App/Models/Tickets::class as $modelClass) --}}
                                                  @foreach (\App\Models\Ticket::all() as $model)
                                                  <option value="{{ $model->id }}"
                                                      @if (old('reference') == $model->id)
                                                          selected
                                                      @endif>{{ $model->reference_no }}</option>
                                              @endforeach
                                                  {{-- @endforeach --}}
                                              </select>
                                              @error('reference')
                                              <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                      @endif
                                      @if(\request()->user()->hasRole('Ticket Admin') || \request()->user()->hasRole('Developer') )
                                      <div class="col-md-12">
                                        <label><strong>@lang('Ticket Reslove Remarks')<small style="font-size: 12px;">[Developer Use Only]</small> :</strong></label>
                                        <textarea class="chat-input1 form-control @error('message') is-invalid @enderror" placeholder="Enter your message here" name="developer_message" id="editor2"></textarea>
                                    </div> 
                                    @endif 
                                      </div>     
                                        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </form>
                                    </div>
                                </div>
                                </div>
                            </div>
                        
                    </div>
                    @endif
                </div>
            </div>

            <ul class="nav nav-pills mb mt-2" id="pills-tab">
                @if (config('laravel-tickets.list.files'))
                <li class="nav-item">
                    <a class="nav-link active" id="pills-files-tab" data-toggle="pill"
                       href="#pills-files">&#128193; @lang('Files')</a>
                </li>
            @endif
                @if (config('laravel-tickets.list.users'))
                    <li class="nav-item">
                        <a class="nav-link " id="pills-users-tab" data-toggle="pill"
                           href="#pills-users">&#128101; @lang('Users')</a>
                    </li>
                @endif
               
            </ul>
            <div class="tab-content" id="pills-tabContent">
               
                <div class="tab-pane fade show active" id="pills-files">
                    @include('laravel-tickets::tickets.partials.files', compact('ticket', 'messages_all'))
                </div>
                <div class="tab-pane fade" id="pills-users">
                    @include('laravel-tickets::tickets.partials.users', compact('ticket', 'messages'))
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
    <script src="https://harvesthq.github.io/chosen/chosen.jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="https://harvesthq.github.io/chosen/chosen.css">
    <script>
         var contentDiv1 = document.getElementsByClassName('content_show')[0];
        var toggleButton1 = document.getElementById('toggleButton');
// console.log(contentDiv1.scrollHeight);
        if (contentDiv1.scrollHeight > contentDiv1.clientHeight) {
            toggleButton1.style.display = 'block';
        } else {
            toggleButton1.style.display = 'none';
        }
        function expandContent() {
            var contentDivs = document.getElementsByClassName('content_show');

for (var i = 0; i < contentDivs.length; i++) {
    var contentDiv = contentDivs[i];

    if (contentDiv.style.height === '50px' || document.getElementById('toggleButton').innerHTML  == 'Show All') {
        contentDiv.style.height = 'auto';
        document.getElementById('toggleButton').innerHTML  = 'Hide';
    } else {
        contentDiv.style.height = '50px';
        document.getElementById('toggleButton').innerHTML  = 'Show All';
    }
}
        }
    </script>
    <script>
        $(".chosen-select").chosen();
        $(".chosen-select1").chosen();
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .catch( error => {
                console.error( error );
            } );
            ClassicEditor
            .create( document.querySelector( '#editor1' ) )
            .catch( error => {
                console.error( error );
            } );
            ClassicEditor
            .create( document.querySelector( '#editor2' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    <script>
 document.getElementById('attachment-button').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission
    document.getElementById('files').click(); // Trigger file input click
});
document.getElementById('files').addEventListener('change', function() {
    var files = this.files;
    var selectedFilesDiv = document.getElementById('selected-files');
    selectedFilesDiv.innerHTML = ''; // Clear previous selections

    if (files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            var fileName = files[i].name;
            var fileItem = document.createElement('div');
            fileItem.innerHTML = '&nbsp;&nbsp;&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-paperclip" viewBox="0 0 24 24"><defs></defs><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path></svg>' + fileName; // Using Font Awesome icon
            selectedFilesDiv.appendChild(fileItem);
        }
    } else {
        selectedFilesDiv.textContent = 'No files selected';
    }
});
    </script>
@endsection
