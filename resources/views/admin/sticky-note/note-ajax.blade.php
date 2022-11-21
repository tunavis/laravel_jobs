@foreach($stickyNotes as $note)
    <div class="col-md-12 sticky-note" id="stickyBox_{{$note->id}}">
        <div class="well bg-{{$note->colour}} b-none">
            <p>{!! nl2br($note->note_text)  !!}</p>
            <hr>
            <div class="row font-12">
                <div class="col-md-12">
                    @lang("modules.sticky.lastUpdated"): {{ $note->updated_at->diffForHumans() }}
                </div>
                <div class="col-md-12 mt-2">
                    <a href="javascript:;" class="text-white" onclick="showEditNoteModal({{$note->id}})"><i class="ti-pencil-alt"></i> @lang('app.edit')</a>
                    <a href="javascript:;" class="text-white ml-2" onclick="deleteSticky({{$note->id}})" ><i class="ti-close"></i> @lang('app.delete')</a>
                </div>
            </div>
        </div>
    </div>
@endforeach
