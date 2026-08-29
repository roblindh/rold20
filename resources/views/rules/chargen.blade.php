@extends('layouts.app', ['title' => 'Character Generation'])

@section('content')
<div class="rule-content prose max-w-none">
    <?php include base_path('hb03_chargen_content.php'); ?>
</div>
@endsection
