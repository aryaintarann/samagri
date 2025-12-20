@extends('emails.layout')

@section('content')
    <span class="sub-heading">New Assignment</span>
    <h1>You're Assigned to a Project</h1>

    <p>You have been assigned to a new project. Here are the key details:</p>

    <div class="box">
        <table class="info-table">
            <tr>
                <td class="label-col">Project Name</td>
                <td class="value-col">{{ $project->name }}</td>
            </tr>
            <tr>
                <td class="label-col">Client</td>
                <td class="value-col">{{ $project->client->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-col">Deadline</td>
                <td class="value-col">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'No Deadline' }}</td>
            </tr>
        </table>

        @if($project->description)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e5e7eb;">
                <div style="font-size: 12px; font-weight: 700; color: #6b7280; margin-bottom: 5px; text-transform: uppercase;">
                    Description</div>
                <div style="font-size: 14px; color: #4b5563; line-height: 1.5;">
                    {{ $project->description }}
                </div>
            </div>
        @endif
    </div>

    <div class="btn-container">
        <a href="{{ route('projects.show', $project->id) }}" class="btn">View Project Details</a>
    </div>
@endsection