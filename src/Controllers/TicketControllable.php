<?php


namespace Dassuman\LaravelTickets\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Dassuman\LaravelTickets\Models\Ticket;
use Dassuman\LaravelTickets\Models\TicketMessage;
use Dassuman\LaravelTickets\Models\TicketReference;
use Dassuman\LaravelTickets\Models\TicketUpload;
use Dassuman\LaravelTickets\Rule\TicketReferenceRule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;
/**
 * Class TicketController
 *
 * The main logic of the ticket system. All actions are performed here.
 *
 * If the accept header is json, the response will be a json response
 *
 * @package Dassuman\LaravelTickets\Controllers
 */
trait TicketControllable
{

    /**
     * @link TicketControllable constructor
     */
    public function __construct()
    {
        if (!config('laravel-tickets.permission')) {
            return;
        }

        $this->middleware(config('laravel-tickets.permissions.list-ticket'))->only('index');
        $this->middleware(config('laravel-tickets.permissions.create-ticket'))->only('store', 'create');
        $this->middleware(config('laravel-tickets.permissions.close-ticket'))->only('close');
        $this->middleware(config('laravel-tickets.permissions.show-ticket'))->only('show');
        $this->middleware(config('laravel-tickets.permissions.message-ticket'))->only('message');
        $this->middleware(config('laravel-tickets.permissions.download-ticket'))->only('download');
        $this->middleware(config('laravel-tickets.permissions.ticket-assign'))->only('assign');
        $this->middleware(config('laravel-tickets.permissions.download-selfassign'))->only('selfassign');
    }

    /**
     * Show every @return View|JsonResponse
     *
     * @link Ticket that the user has created
     *
     * If the accept header is json, the response will be a json response
     *
     */
    public function index()
    {
        // echo json_encode(\request()->user()->roles()->get());die;
        if (\request()->user()->can('all-ticket')) {
            if(\request()->user()->hasRole('Ticket Admin')){
                $tickets = Ticket::query();
            }else{
                $tickets = Ticket::query()->Where('opener_id',request()->user()->id)->orWhere('opener_id',null);
            }
           
        } else {
            $tickets = Ticket::Where('user_id',request()->user()->id);
        }
        $tickets = $tickets->with('user')->orderBy('id', 'desc')->paginate(10);

        return request()->wantsJson() ?
            response()->json(compact('tickets')) :
            view(
                'laravel-tickets::tickets.index',
                compact('tickets')
            );
    }

    /**
     * Show the create form
     *
     * @return View
     */
    public function create()
    {
        return view('laravel-tickets::tickets.create');
    }

    /**
     * Creates a @param Request $request the request
     *
     * @return View|JsonResponse|RedirectResponse
     * @link Ticket
     *
     */
    public function store(Request $request)
    {
        $rules = [
            'subject' => ['required', 'string', 'max:191'],
            // 'priority' => ['required', Rule::in(config('laravel-tickets.priorities'))],
            'message' => ['required', 'string'],
            'files' => ['max:' . config('laravel-tickets.file.max-files')],
            'files.*' => [
                'sometimes',
                'file',
                'max:' . config('laravel-tickets.file.size-limit'),
                'mimes:' . config('laravel-tickets.file.mimetype'),
            ],
        ];
        if (config('laravel-tickets.category')) {
            $rules['category_id'] = [
                'required',
                Rule::exists(config('laravel-tickets.database.ticket-categories-table'), 'id'),
            ];
        }
        if (config('laravel-tickets.references')) {
            $rules['reference'] = [
                config('laravel-tickets.references-nullable') ? 'nullable' : 'required',
                new TicketReferenceRule(),
            ];
        }
        $data = $request->validate($rules);
        if ($request->user()->tickets()->where('state', '!=', 'CLOSED')->count() >= config('laravel-tickets.maximal-open-tickets')) {
            $message = trans('You have reached the limit of open tickets');
            return \request()->wantsJson() ?
                response()->json(compact('message')) :
                back()->with(
                    'message',
                    $message
                );
        }

        // Generate a unique identifier (UUID)
        $uuid = Str::uuid()->toString();

        // Concatenate the current timestamp with the UUID
        $referenceNo = time() . $uuid;

        // Remove non-numeric characters
        $referenceNo = preg_replace('/[^0-9]/', '', $referenceNo);

        // Ensure the reference number is unique
        $referenceNo = substr($referenceNo, 0, 10); // Adjust the length as needed
        $data['reference_no']="COALRR".$referenceNo;
        $ticket = $request->user()->tickets()->create(
            $data
        );

        if (array_key_exists('reference', $data)) {
            $reference = explode(',', $data['reference']);
            $ticketReference = new TicketReference();
            $ticketReference->ticket()->associate($ticket);
            $ticketReference->referenceable()->associate(
                resolve($reference[0])->find($reference[1])
            );
            $ticketReference->save();
        }

        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($request->user());
        $ticketMessage->ticket()->associate($ticket);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        $message = trans('The ticket was successfully created');
        return $request->wantsJson() ?
            response()->json(compact('message', 'ticket', 'ticketMessage')) :
            redirect(route(
                'laravel-tickets.tickets.show',
                compact('ticket')
            ))->with(
                'message',
                $message
            );
    }

    /**
     * Show detailed informations about the @param Ticket $ticket
     *
     * @return View|JsonResponse|RedirectResponse|void
     * @link Ticket and the informations
     *
     */
    public function show(Ticket $ticket)
    {
        // if(
        //     \request()->user()->hasRole('Ticket Admin') ||
        //     $ticket->opener_id==request()->user()->id
        // ) {
            $messages = $ticket->messages()->with(['user', 'uploads'])->orderBy('created_at', 'desc');
           
        // }else{
        //     return abort(403);
        // }

        

        return \request()->wantsJson() ?
            response()->json(compact(
                'ticket',
                'messages'
            )) :
            view(
                'laravel-tickets::tickets.show',
                compact(
                    'ticket',
                    'messages'
                )
            );
    }

    /**
     * Send a message to the @param Request $request
     *
     * @param Ticket $ticket
     *
     * @return JsonResponse|RedirectResponse|void
     * @link Ticket
     *
     */
    public function message(Request $request, Ticket $ticket)
    {
        // if (
        //     !$ticket->user()->get()->contains(\request()->user()) &&
        //     !request()->user()->can(config('laravel-tickets.permissions.all-ticket'))
        // ) {
        //     return abort(403);
        // }
// dd($request->all());
        if (!config('laravel-tickets.open-ticket-with-answer') && $ticket->state === 'CLOSED') {
            $message = trans('You cannot reply to a closed ticket');
            return \request()->wantsJson() ?
                response()->json(compact('message')) :
                back()->with(
                    'message',
                    $message
                );
        }

        $data = $request->validate([
            'message' => ['required', 'string'],
            'files' => ['max:' . config('laravel-tickets.file.max-files')],
            'files.*' => [
                'sometimes',
                'file',
                'max:' . config('laravel-tickets.file.size-limit'),
                'mimes:' . config('laravel-tickets.file.mimetype'),
            ]
        ]);
        // if (array_key_exists('reference', $data)) {
        //     $ticketMessage->ticket_reference=$data['reference'];
        // }
        // dd($request->reference);
        $ticketMessage = new TicketMessage($data);
        $ticketMessage->user()->associate($request->user());
        $ticketMessage->ticket()->associate($ticket);
        if ($request->reference) {
            $ticketMessage->ticket_reference=implode(",",$request->reference);
        }
        // dd($ticketMessage);
        $ticketMessage->save();

        $this->handleFiles($data['files'] ?? [], $ticketMessage);

        $ticket->update(['state' => 'OPEN']);

        $message = trans('Your answer was sent successfully');
        return $request->wantsJson() ?
            response()->json(compact('message')) :
            back()->with(
                'message',
                $message
            );
    }

    /**
     * Declare the @param Ticket $ticket
     *
     * @return JsonResponse|RedirectResponse|void
     * @link Ticket as closed.
     *
     */
    public function close(Request $request,Ticket $ticket)
    {
        // if (
        //     !$ticket->user()->get()->contains(\request()->user()) &&
        //     !request()->user()->can(config('laravel-tickets.permissions.all-ticket'))
        // ) {
        //     return abort(403);
        // }
        if ($ticket->state === 'CLOSED') {
            $message = trans('The ticket is already closed');
            return \request()->wantsJson() ?
                response()->json(compact('message')) :
                back()->with(
                    'message',
                    $message
                );
        }
        $ticket->update(['state' => 'CLOSED','close_message'=>$request->close_message,'developer_message'=>$request->developer_message,'ticket_reference'=>implode(",",$request->reference)]);

        $message = trans('The ticket was successfully closed');
        return \request()->wantsJson() ?
            response()->json(compact('message')) :
            back()->with(
                'message',
                $message
            );
    }

    /**
     * Downloads the file from @param Ticket $ticket
     *
     * @param TicketUpload $ticketUpload
     *
     * @return BinaryFileResponse
     * @link TicketUpload
     *
     */
    public function download(Ticket $ticket, TicketUpload $ticketUpload)
    {
        // if (
        //     !$ticket->user()->get()->contains(\request()->user()) &&
        //     !request()->user()->can(config('laravel-tickets.permissions.all-ticket'))
        // ) {
        //     return abort(403);
        // }

        $storagePath = storage_path('app/' . $ticketUpload->path);
        if (config('laravel-tickets.pdf-force-preview') && pathinfo($ticketUpload->path, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file($storagePath);
        }

        return response()->download($storagePath);
    }

    /**
     * Handles the uploaded files for the @param $files array uploaded files
     *
     * @param TicketMessage $ticketMessage
     *
     * @link TicketMessage
     *
     */
    private function handleFiles($files, TicketMessage $ticketMessage)
    {
        if (!config('laravel-tickets.files') || $files == null) {
            return;
        }
        foreach ($files as $file) {
            $ticketMessage->uploads()->create([
                'path' => $file->storeAs(
                    config('laravel-tickets.file.path') . $ticketMessage->id,
                    $file->getClientOriginalName(),
                    config('laravel-tickets.file.driver')
                )
            ]);
        }
    }

    public function assign(Request $request)
    {
        // dd($request->all());
        // if (
        //     !$ticket->user()->get()->contains(\request()->user()) &&
        //     !request()->user()->can(config('laravel-tickets.permissions.all-ticket'))
        // ) {
        //     return abort(403);
        // }
        $data = $request->validate([
            'priority' => ['required', 'string'],
            'users_id' => ['required', 'string'],
        ]);
        $ticketMessage =Ticket::find($request->ticket_id);
        $ticketMessage->priority=$request->priority;
        $ticketMessage->opener_id=$request->users_id;
        $ticketMessage->save();
    
        $message = trans('The ticket was successfully Assigned');
        return \request()->wantsJson() ?
            response()->json(compact('message')) :
            back()->with(
                'message',
                $message
            );
    }
    public function selfassign(Ticket $ticket)
    {
        // dd(\Auth::user()->id);
        // if (
        //     !$ticket->user()->get()->contains(\request()->user()) &&
        //     !request()->user()->can(config('laravel-tickets.permissions.all-ticket'))
        // ) {
        //     return abort(403);
        // }
        $ticket->opener_id=\Auth::user()->id;
        $ticket->save();
        $message = trans('The ticket was successfully Assigned');
        return \request()->wantsJson() ?
            response()->json(compact('message')) :
            back()->with(
                'message',
                $message
            );
    }
}
