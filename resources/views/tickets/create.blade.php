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
  height: 55px;
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
 </style>
    <div class="card" style="width: 42%;margin-left: 29%;">
        <div class="card-header">
            @lang('Create ticket')
        </div>
        <div class="card-body">
            @includeWhen(session()->has('message'), 'laravel-tickets::alert', ['type' => 'info', 'message' => session()->get('message')])
            <form method="post" action="{{ route('laravel-tickets.tickets.store') }}"
                  @if (config('laravel-tickets.files'))
                  enctype="multipart/form-data"
                @endif>
                @csrf
                <div class="row">
                    {{-- <div class="col-md-3"> --}}
                        @if (config('laravel-tickets.category'))
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('Category')</label>
                                <select class="form-control @error('category_id') is-invalid @enderror"
                                        name="category_id">
                                    @foreach (\DassumanLaravelTickets\Models\TicketCategory::all() as $ticketCategory)

                                        <option value="{{ $ticketCategory->id }}"
                                                @if (old('category_id') === $ticketCategory->id)
                                                selected
                                            @endif>@lang($ticketCategory->translation)</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                   
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('Subject'):</label>
                            <input class="form-control @error('subject') is-invalid @enderror" name="subject"
                                   placeholder="@lang('Subject')" value="{{ old('subject') }}">
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- </div> --}}
                    <div class="col-md-12 mt-2">
                        <label>@lang('Decription'):</label>
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
                    <div class="col-md-6 mt-2">
                        <div class="form-group">
                        <label>@lang('Upload File'):<small>(Only Pdf,Jpg,Png Allowed)</small></label>
                        <input type="file" id="files" name="files[]" multiple class="form-control @error('files') is-invalid @enderror {{ empty($errors->get('files.*'))?'':'is-invalid' }}">
                        @error('files')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button class="chat-send-btn btn btn-primary">Send</button>
                    </div>
                    
                </div>
                
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

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
           <script>
            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .catch( error => {
                    console.error( error );
                } );
        </script>
@endsection
