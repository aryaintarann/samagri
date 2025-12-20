@extends('emails.layout')

@section('content')
    <span class="sub-heading">Knowledge Base</span>
    <h1>{{ $sop->title }}</h1>

    <p>A new article has been published to our Knowledge Base. Here is a quick summary:</p>

    <div class="box">
        <table class="info-table">
            <tr>
                <td class="label-col">Category</td>
                <td class="value-col">{{ $sop->category }}</td>
            </tr>
            <tr>
                <td class="label-col">Author</td>
                <td class="value-col">{{ $sop->creator->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td class="label-col">Date</td>
                <td class="value-col">{{ $sop->created_at->format('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="btn-container">
        <a href="{{ route('sops.show', $sop->id) }}" class="btn">Read Full Article</a>
    </div>
@endsection