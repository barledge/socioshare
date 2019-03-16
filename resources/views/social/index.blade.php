@extends('layouts.inner')
@section('title', 'Connect your social accounts')

@section('inner')

    @hasrole('client')
    <div class="alert alert-info clearfix" role="alert">
        <p class="form-control-static"><strong>Hello <?php echo auth()->user()->name ?>!</strong> Please sync all your social
            media accounts here.</p>
    </div>
    @endhasrole

    <?php if(!count($social_accounts) && (auth()->user()->hasrole('admin'))) : ?>
    <div class="alert alert-info clearfix" role="alert">
        <p class="form-control-static"><strong>Starter Tip:</strong> Add at least one social media account before
            continuing.</p>
    </div>
    <?php endif; ?>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                                                         data-dismiss="alert"
                                                                                         aria-label="close">&times;</a>
                </p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->
    <div class="row">

        <div class="col-sm-12">


            <br/>

            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <br/>
                            <img src="<?php echo asset('/img/twitter.png') ?>"/><br/><br/>
                            <p><strong>Twitter</strong></p><br/>

                            <a href="/twitter/login" class="btn btn-primary btn-block">connect</a>

                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <br/>
                            <img src="<?= asset('/img/facebook.png') ?>"/><br/><br/>
                            <p><strong>Facebook</strong></p><br/>

                            <a href="/facebook/preselect?select=profile" class="btn btn-primary btn-block">profile</a>
                            <a href="/facebook/preselect?select=pages" class="btn btn-primary btn-block">page</a>
                            <a href="/facebook/preselect?select=groups" class="btn btn-primary btn-block">group</a>

                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <br/>
                            <img src="<?= asset('/img/linkedin.png') ?>"/><br/><br/>
                            <p><strong>Linkedin</strong></p><br/>

                            <a href="/linkedin/profile" class="btn btn-primary btn-block">profile</a>
                            <a href="/linkedin/pages" class="btn btn-primary btn-block">companies</a>

                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <br/>
                            <img src="<?= asset('/img/pinterest.png') ?>"/><br/><br/>
                            <p><strong>Pinterest</strong></p><br/>

                            <a href="/pinterest/profile" class="btn btn-primary btn-block">connect</a>

                        </div>
                    </div>
                </div>


                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <br/>
                            <img src="<?= asset('/img/instagram.png') ?>"/><br/><br/>

                            <p><strong>Instagram</strong></p><br/>

                            <a href="/instagram" class="btn btn-primary btn-block">connect</a>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Connected accounts
            @hasrole('admin')
            <a href="/groups" class="btn btn-default btn-xs  pull-right mr-xxs"><i class="fa fa-users"
                                                                                   aria-hidden="true"></i> Manage
                groups/clients</a>
            @endhasrole
        </div>
        <div class="panel-body">
            <?php if(count($social_accounts) > 0) : ?>
            <div class="row">
                <div class="col-sm-12">

                    <table class="table">
                        <tr>
                            <th style="border-top: 0">Account</th>
                            <th style="border-top: 0">Platform</th>
                            <th style="border-top: 0">Created At</th>
                        </tr>
                        <?php foreach($social_accounts as $social_account) : ?>
                        <tr>
                            <td><?php if($social_account->needs_reauth): ?><i class="mdi mdi-exclamation"
                                                                              aria-hidden="true"></i><?php endif; ?><?php echo $social_account->name; ?>
                            </td>
                            <td><?php echo $social_account->platform ?></td>
                            <td><?php echo $social_account->created_at->diffForHumans() ?></td>
                            @hasrole('admin')
                            <td><a href="/schedule/<?php echo $social_account->id ?>/edit">Schedule</a></td>
                            @endhasrole
                            <td>
                                <a href="#" class="delete-confirm"
                                   data-text="Deleting this account means any scheduled posts for it will not be posted"
                                   data-url="/social-accounts/<?php echo $social_account->id; ?>"><i
                                            class="mdi mdi-close"
                                            aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                </div>
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col-sm-12">
                    <br/>
                    <div class="alert alert-warning" role="alert">
                        <strong>Notice:</strong> You have no social accounts set-up yet.
                    </div>
                    <br/>
                </div>
            </div>
            <?php endif; ?>


        </div>
    </div>
@endsection
