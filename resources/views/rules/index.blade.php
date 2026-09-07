@extends('layouts.app', ['title' => 'Rules Index'])

@section('content')
<div class="rule-content prose max-w-none">
    <?php include resource_path('views/rules/content/hb15_index_content.php'); ?>
</div>
@endsection
