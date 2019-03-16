@extends('layouts.inner')
@section('title', 'Drafts')

@section('inner')


    <ul class="nav nav-pills">
        <li role="presentation"><a href="<?= route('content.create') ?>">Create New Post</a></li>
        <li role="presentation" class="active"><a href="/drafts">Drafts</a></li>
    </ul>
    <br/>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">


                <div class="col-sm-12">
                    <h4>Draft Posts</h4>

                    <p>Drafts are posts that have not been scheduled yet.</p>

                    <?php if(count($posts) == 0 || !isset($posts) || empty($posts) || !defined($posts)) : ?>
                    <br/>
                    <div class="alert alert-warning" role="alert">
                        <strong>Notice:</strong> You have no draft posts.
                    </div>
                    <?php else: ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Message</th>
                            <th>Creation date</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($posts as $post) : ?>
                        <tr>
                            <td>#<?php echo $post->id; ?></td>
                            <td><?php echo str_limit($post->message, 20); ?></td>
                            <td><?php echo $post->created_at->diffForHumans(); ?></td>
                            <td><a href="/content/<?php echo $post->id; ?>/edit">Edit/Publish</a></td>
                            <td><a class="delete-confirm" data-url="/content/<?php echo $post->id; ?>"
                                   data-text="Are you sure you want to post #<?php echo $post->id; ?>?" href="#"
                                   data-id="<?php echo $post->id; ?>">Delete</a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    {{ $posts->links() }}
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
@endsection
