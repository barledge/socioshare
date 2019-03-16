<template>
<div>
    <div v-if="view=='default'">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
            <div class="row">
                <div class="col-sm-12">
                    <a v-for="(scheduled_post, index) in post.scheduled_posts" href="" class="text-center scheduled_post_icon" :title="scheduled_post.social_account.name"  @click.prevent="setScheduledMessage(scheduled_post)" :class="[scheduled_post.id == current_scheduled_post.id ? 'active' : '']" v-if="scheduled_post.active">
                      <i class="mdi " :class="['mdi-' + scheduled_post.social_account.platform]" aria-hidden="true"></i>
                    </a>
                </div>
                <span class="label label-default" style="background: #e8e8e8; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05); color: rgba(0,0,0,.6);  position: absolute; top: -8px; right: -5px; border: 1px solid #E0E0E0">{{post.approval_status_text}}</span>

            </div>
            </div>


            <div class="panel-body pt-xxxs" style="height: 362px; position: relative; display: block; overflow-x: hidden; overflow-y: auto;">

                <div class="row">
                    <div class="col-sm-3">
                        <small class="text-muted"  style="color: #BDBDBD">#{{post.id}} <a href="" @click.prevent="history()"><i class="mdi mdi-comment-multiple-outline text-muted " style="color: #ddd;font-size: x-small"></i></a></small>
                    </div>
                    <div class="col-sm-9 text-right">
                        <small class="text-muted" style="color: #BDBDBD; font-size: 10px;">
                            <span style="font-size: 10px">Scheduled for</span> {{current_scheduled_post.scheduled_at?current_scheduled_post.scheduled_at_human:current_scheduled_post.queued_for_human}}</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">

                        <agile :options="mySettings" v-if="myPost.files && myPost.files.length > 0">
                            <div v-for="(file, index) in myPost.files"  class="slide  text-center" style="margin-top: 5px; height: 150px;">
                                <img v-if="file.response.media_type == 'image'" :src="'/uploads/' + file.response.thumb | site_url" class="img-thumbnail" style="height: 100%;"/>

                                <video v-if="file.response.media_type == 'video'"  style=" height:150px" controls>
                                  <source :src="'/uploads/' + file.response.path | site_url" type="video/mp4">
                                Your browser does not support the video tag.
                                </video>


                            </div>
                        </agile>

                    </div>
                </div>


            <small style="font-size: 10px">{{current_scheduled_post.social_account.name}}</small><br />
            <span v-html="hashify(current_scheduled_post.message)"></span>
            <br />
            <!--<a href="" style="position: absolute;bottom: 0;" @click.prevent="history()"><i class="fa fa-history text-muted " style="color: #ddd;font-size: x-small" aria-hidden="true"></i> <small style="font-size: x-small">History</small></a>-->



            </div>
            <hr class="m-0 p-0"/>
            <div class="panel-body p-xxs">
                <div class="row" v-if="post.approval_status == 'APPROVED'">
                    <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-success btn-outline btn-block" disabled><i class="fa fa-check" aria-hidden="true"></i> Approved</button>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-success btn-outline btn-block" @click.prevent="setApprove">Approve</button>

                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-danger btn-outline btn-block"  @click.prevent="requestChanges">Request changes</button>

                    </div>
                </div>
            </div>


        </div>
    </div>

    <div v-else-if="view == 'history'">
        <div class="panel panel-default">
                <div class="panel-body"  style="height: 403px; position: relative; display: block; overflow-x: hidden; overflow-y: auto;">
                    <a href="" @click.prevent="back()"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Back</a><br /><br />
                    <div v-if="loading_reviews">
                        <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                    </div>

                    <div v-else>
                        <h5 style="text-decoration: underline; font-weight:bold;">Comments</h5>

                        <div v-for="review in reviews">
                            <small class="text-muted" style="color: #BDBDBD">{{ review.user.email }}, {{ review.created_at_human }}</small><br />
                            {{ review.status == 'approve'?'APPROVED ':'' }}{{ review.message }}  <small class="text-muted"></small>
                            <hr class="m-xxs p-0"/>
                        </div>



                    </div>

                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-success btn-block" @click.prevent="comment">Add comment</button>
                </div>

        </div>
    </div>

    <div v-else>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
        <div class="panel-body">
            <form v-on:submit.prevent="onSubmit">
            <a href="" @click.prevent="back()"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Back</a><br /><br />
            <textarea class="form-control" rows="3" style="overflow-x: hidden" v-model="message" placeholder="Leave a comment..."></textarea>
            <br />
            <div class="radio">
            <label>
            <input type="radio" name="optionsRadios" value="request_changes" v-model="selection">
            <strong>Request changes</strong><br /><small>Submit feedback that must be addressed before posting.</small>
            </label>
            </div>
            <div class="radio">
            <label>
            <input type="radio" name="optionsRadios" value="comment" v-model="selection">
            <strong>Comment</strong><br /><small>Submit general feedback without explicit approval..</small>
            </label>
            </div>
            <div class="radio" v-if="post.approval_status != 'APPROVED'">
            <label>
            <input type="radio" name="optionsRadios" value="approve" v-model="selection">
            <strong>Approve</strong><br /><small>Submit feedback and approve post..</small>

            </label>
            </div>
            <div class=" text-right mt-s">
                <button type="submit" class="btn btn-success" v-if="!submitting">Submit</button>
                <button type="button" class="btn btn-success" v-else>Submitting...</button>
            </div>
        </form>
        </div>
        </div>
        </div>

    </div>

</div>
</template>

<script>
function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" style="white-space: nowrap;max-width: 200px;display: block;overflow: hidden;-o-text-overflow: ellipsis;text-overflow:ellipsis;" target="_blank">$1</a>');
console.log(replacePattern1);

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    console.log(replacePattern2);
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" style="white-space: nowrap;max-width: 200px;overflow: hidden;-o-text-overflow: ellipsis;text-overflow:ellipsis;display: block;" target="_blank">$2</a>');
    //.replace(/^(.{8}).+/, "$1&hellip;");

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}

import Vue from 'vue'
import VueAgile from 'vue-agile'

Vue.use(VueAgile)

export default {
    props: ['myPost'],
    data: () => ({
        post: {},
        default_groups: [],
        current_scheduled_post: null,
        reviews: [],
        review: false,
        submitting: false,
        loading_reviews: false,
        message: '',
        view: 'default',
        selection: 'comment',
        mySettings: {
                    infinite: false,
                    dots: false,
                    prevArrow: '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><path d="M10.25,12.75c0.191,0,0.384-0.072,0.529-0.219c0.294-0.295,0.294-0.77,0-1.062L7.311,8l3.469-3.469 c0.294-0.293,0.294-0.768,0-1.062c-0.293-0.293-0.768-0.293-1.061,0l-4,4c-0.293,0.293-0.293,0.768,0,1.062l4,4 C9.866,12.678,10.059,12.75,10.25,12.75z"/></svg>',
                    nextArrow: '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><path d="M6.249,12.75c-0.191,0-0.384-0.072-0.529-0.219c-0.294-0.295-0.294-0.77,0-1.062L9.188,8L5.72,4.531 c-0.294-0.293-0.294-0.768,0-1.062c0.293-0.293,0.768-0.293,1.061,0l4,4c0.293,0.293,0.293,0.768,0,1.062l-4,4 C6.633,12.678,6.44,12.75,6.249,12.75z"/></svg>'

                }
    }),

    beforeMount() {
        this.post = this.myPost;
        this.current_scheduled_post = this.post.scheduled_posts.find(()=>true);
    },

    mounted() {
        console.log('beforeMount', this.post.scheduled_posts);
    },
    watch: {
        // whenever question changes, this function will run
        view: function () {
          $('.panel-item').matchHeight();
        }
    },
    methods: {
        back() {
            this.view = 'default';
        },
        hashify(newMessage) {
            let replacedText = new String(newMessage);
            replacedText = replacedText.replace(/(#\S+)/g, '<span class="text-primary">$1</span>');
            replacedText = replacedText.replace(/(@\S+)/g, '<span class="text-primary">$1</span>');
            replacedText = linkify(replacedText);
            replacedText = replacedText.replace(/(?:\r\n|\r|\n)/g, '<br />');
            return replacedText;
        },
        onSubmit() {

            if(this.selection != 'approve' && this.message.length < 10) {
                alertify.alert("Please enter a comment with at least 10 characters before continuing.");
            } else {
                this.submitting = true;

                axios.put( site_url('/client/review/' + this.post.id), {
                        selection: this.selection,
                        message: this.message
                    })
                    .then((response) => {
                        console.log(response);
                        this.post = response.data.post;
                        this.message = '';
                        this.history();
                        this.submitting = false;
                    })
                    .catch((error) => {
                        console.log(error);
                        this.submitting = false;
                        alert(error);
                    });

            }


        },
        history() {
            this.view = 'history';
            this.loading_reviews = true;
            axios.get( site_url('/client/review/' + this.post.id))
                .then((response) => {
                    this.reviews = response.data.reviews;
                    this.loading_reviews = false;
                })
                .catch((error) => {
                    console.log(error);
                    this.loading_reviews = false;
                });
        },
        setScheduledMessage(scheduled_post) {
            this.current_scheduled_post = scheduled_post;
        },
        comment() {
            this.view = 'reviews';
            this.selection = 'comment';
        },
        requestChanges() {
            this.view = 'reviews';
            this.selection = 'request_changes';
        },
        setApprove() {
            this.view = 'reviews';
            this.selection = 'approve';
        },

    }
}

</script>
