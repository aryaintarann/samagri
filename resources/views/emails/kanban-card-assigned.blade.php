@extends('emails.layout')

@section('content')
    <span class="sub-heading">New Card Assignment</span>
    <h1>You've Been Assigned to a Card</h1>

    <p>{{ $assignedBy }} has assigned you to a card on the Kanban board. Here are the details:</p>

    <div class="box">
        <table class="info-table">
            <tr>
                <td class="label-col">Card Title</td>
                <td class="value-col">{{ $card->title }}</td>
            </tr>
            @if($project)
                <tr>
                    <td class="label-col">Project</td>
                    <td class="value-col">{{ $project->name }}</td>
                </tr>
            @endif
            <tr>
                <td class="label-col">Priority</td>
                <td class="value-col">
                    @if($card->priority == 'high')
                        <span style="color: #dc2626; font-weight: 600;">High</span>
                    @elseif($card->priority == 'medium')
                        <span style="color: #d97706; font-weight: 600;">Medium</span>
                    @else
                        <span style="color: #16a34a; font-weight: 600;">Low</span>
                    @endif
                </td>
            </tr>
            @if($card->due_date)
                <tr>
                    <td class="label-col">Due Date</td>
                    <td class="value-col">{{ \Carbon\Carbon::parse($card->due_date)->format('M d, Y') }}</td>
                </tr>
            @endif
        </table>

        @if($card->description && $card->description !== '<p><br></p>')
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e5e7eb;">
                <div style="font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 5px; text-transform: uppercase;">
                    Description</div>
                <div style="font-size: 14px; color: #4b5563; line-height: 1.5;">
                    {!! strip_tags($card->description, '<p><br><b><strong><i><em><ul><ol><li>') !!}
                </div>
            </div>
        @endif
    </div>

    <div class="btn-container">
        <a href="{{ $kanbanUrl }}" class="btn">View Kanban Board</a>
    </div>
@endsection