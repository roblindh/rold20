@extends('layouts.app', ['title' => 'Rules of Combat'])

@section('content')
<div class="rule-content prose max-w-none">
    <?php include resource_path('views/rules/content/hb04_combat_content.php'); ?>
</div>
@endsection
