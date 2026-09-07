@extends('layouts.app', ['title' => 'Rules of Engagement'])

@section('content')
<div class="rule-content prose max-w-none">
    <?php include resource_path('views/rules/content/hb08_encounters_content.php'); ?>
</div>
@endsection
