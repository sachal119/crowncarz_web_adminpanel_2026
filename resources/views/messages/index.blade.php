@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-4">Messages for Booking #{{ $booking->ref_no }}</h3>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Type</th>
        <th>Content</th>
        <th>Status</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      @foreach($messages as $message)
      <tr>
        <td>{{ $message->type }}</td>
        <td>{{ $message->content }}</td>
        <td>{{ $message->status }}</td>
        <td>{{ $message->sent_at }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
