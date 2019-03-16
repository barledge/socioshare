<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">Menu</div>
            <div class="panel-body">
                <ul class="nav nav-pills nav-stacked">
                    <li role="presentation" class="{{ Request::is('/*') ? 'active' : '' }}"><a
                                href="<?php echo route('home') ?>">Home</a></li>
                    <li role="presentation"><a class="pb-0 pt-s" style="background: transparent"><strong
                                    class="text-muted">Content</strong></a></li>
                    <li role="presentation"
                        class="{{ (Route::is('content.create') || Route::is('content.edit')) ? 'active' : '' }}"><a
                                href="<?php echo route('content.create') ?>">Add Post</a></li>
                    <li role="presentation" class="{{ Route::is('content.index') ? 'active' : '' }}"><a
                                href="<?php echo route('content.index') ?>">Draft posts</a></li>
                    <li role="presentation" class="{{ Request::is('feeds*') ? 'active' : '' }}"><a
                                href="<?php echo route('feeds.index') ?>">RSS Feeds</a></li>

                    <li role="presentation"><a class="pb-0 pt-s" style="background: transparent"><strong
                                    class="text-muted">Scheduling</strong></a></li>
                    <li role="presentation" class="{{ Request::is('queue*') ? 'active' : '' }}"><a
                                href="<?php echo route('queue.create') ?>">Queued Posts</a></li>
                    <li role="presentation" class="{{ Request::is('schedule*') ? 'active' : '' }}"><a
                                href="<?php echo route('schedule.index') ?>">Posting times</a></li>

                    <li role="presentation"><a class="pb-0 pt-s" style="background: transparent"><strong
                                    class="text-muted">Settings</strong></a></li>
                    <li role="presentation" class="{{ Request::is('social-accounts*') ? 'active' : '' }}"><a
                                href="<?php echo route('social-accounts.index') ?>">Social Accounts</a></li>

                </ul>

            </div>
        </div>

    </div>
</div>
