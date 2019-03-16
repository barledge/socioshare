@extends('layouts.inner')
@section('title', 'Drafts')

@section('inner')


    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{ route('content.create') }}">Create New Post</a></li>
        <li role="presentation"><a href="/drafts">Drafts</a></li>
    </ul>
    <br/>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="/content" method="GET">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>Viewing all posts</h4>
                            </div>
                            <div class="col-sm-4">
                                {{ Form::select('social_account_id', $social_accounts, null, ['class' =>
                                'form-control']) }}
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-default" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                    <hr/>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th>Message</th>
                            <th>Scheduled for</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($posts as $post) : ?>
                        <tr class="<?php echo ($post->post_id % 2) ? 'active' : '' ?>">
                            <td>#<?php echo $post->post_id ?></td>
                            <td title="<?php echo $post->id ?>">
                                <?php if($post->social_account) : ?><i
                                        class="mdi mdi-<?php echo $post->social_account->platform . " " . $post->social_account->platform ?>"></i><?php endif; ?>
                            </td>
                            <td><a href="/content/<?php echo $post->post_id ?>/edit"><?php echo str_limit($post->message, 30) ?></a>
                            </td>
                            <td><?php echo ($post->post_date) ? $post->post_date->format('jS M H:i') : '-' ?></td>
                            <td><?php if($post->status == 'FAILED') : ?><i data-toggle="tooltip" data-placement="top"
                                                                        title="<?php echo $post->error_log ?>"
                                                                        class="fa fa-exclamation-triangle fa-fw"
                                                                        style="color: #ff0000"
                                                                        aria-hidden="true"></i><?php endif ?> <?php echo $post->status_text ?> <?php if($post->status == 'SENT') : ?>
                                <a href="/content/redirect/<?php echo $post->id ?>" target="_blank"><i
                                            class="fa fa-external-link fa-fw" aria-hidden="true"></i></a><?php endif ?>
                            </td>
                            <td><?php echo ($post->created_at) ? $post->created_at->diffForHumans() : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    {{ $posts->appends($_GET)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
