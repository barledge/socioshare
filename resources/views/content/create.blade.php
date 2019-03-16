@extends('layouts.inner')
@section('title', (isset($post))?'Edit post':'Schedule a new post')

@section('inner')
    <div class="row">
        <div class="col-md-12">
            <?php if(!isset($post) || !defined($post)) : ?>

            <ul class="nav nav-pills">
                <li role="presentation" class="active"><a href="<?php echo route('content.create') ?>">Create New
                        Post</a></li>
                <li role="presentation"><a href="/drafts">Drafts</a></li>
            </ul>
            <?php else: ?>

            <?php if(isset($post) && $post->is_draft): ?>
            <a href="<?php echo url('drafts') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to
                drafts</a>
            <?php else: ?>
            <a href="<?php echo route('queue.create') ?>?tab=<?php echo (!$post->scheduled_at) ? 'in_queue' : 'custom_schedule' ?>&accounts=<?php echo implode(',', $social_accounts->pluck('id')->toArray()) ?>"><i
                        class="fa fa-chevron-left" aria-hidden="true"></i> Back to schedule</a>
            <?php endif; ?>
            <?php endif; ?>
            <br/>

            <div class="row">
                <div class="col-sm-12">
                    <?php if(isset($post)) : ?>
                    <edit-post :post="{{json_encode($post, JSON_NUMERIC_CHECK)}}" :groups="{{json_encode($groups)}}"
                               :social-accounts="{{json_encode($social_accounts)}}"></edit-post>
                    <?php else: ?>
                    <post :groups="{{json_encode($groups)}}"
                          :social-accounts="{{json_encode($social_accounts)}}"></post>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
@endsection
