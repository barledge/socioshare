<template>

    <div>
        <modal v-model="open" title="Comments & Notifications" :footer="false" @hide="dismissCallback" ref="modal" size="sm">
<div class="row">

            <div class="col-sm-12">


    <form v-on:submit.prevent="onSubmit"  v-if="reviews && reviews.length > 0">


        <div class="row">

        <div class="col-sm-12">
            <textarea class="form-control" rows="3" style="overflow-x: hidden" v-model="message" placeholder="Leave a comment..."></textarea>
        </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="optionsRadios" value="request_review" v-model="selection">
                        <strong>Request review</strong></small>
                    </label>
                </div>
            </div>
            <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-success  mt-xxxs" v-if="!submitting">Submit</button>
                <button type="button" class="btn btn-success mt-xxxs" v-else>Submitting...</button>
            </div>
            <div class="col-sm-12" v-if="error != ''">
                <div class="alert alert-danger" role="alert">{{error}}</div>
            </div>
        </div>
<hr />
</form>



                </div>

                <div v-if="loading_reviews">
                    <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                </div>
                <div class="col-sm-12" v-if="reviews.length == 0 && !loading_reviews">
                    <div class="alert alert-danger" role="alert">No comments yet</div>
                </div>
                <div class="col-sm-12">
                    <div v-for="review in reviews">
                        <small class="text-muted" style="color: #BDBDBD">{{ review.user.email }}, {{ review.created_at_human }}</small><br />
                        {{ review.status == 'approve'?'APPROVED ':'' }}{{ review.message }}  <small class="text-muted"></small>
                        <hr class="m-xxs p-0"/>
                    </div>
                </div>
                </div>

        </modal>
    </div>
</template>

<script>
export default {
    props: ['post', 'comment-open'],
    data: () => ({
        open: false,
        submitting: false,
        message: "",
        error: "",
        selection: false,
        reviews: [],
        loading_reviews: false,
    }),
    watch: {
        commentOpen: function(val, oldVal) {
            this.open = val;
        },
        post: function(val, oldVal) {

        }
    },
    mounted() {
        this.history()
    },
    methods: {
        dismissCallback (msg) {
            //this.msg = `Modal dismiss with msg '${msg}'.`;
            this.$emit('update:commentOpen', false)
        },
        onSubmit() {

            if(!this.selection && this.message.length < 2) {
                this.error = "Please enter a comment before continuing.";
            } else {
                this.submitting = true;

                axios.put( site_url('/review/' + this.post.id), {
                        selection: this.selection,
                        message: this.message
                    })
                    .then((response) => {
                        console.log(response);
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
            this.loading_reviews = true;
            axios.get( site_url('/review/' + this.post.id))
                .then((response) => {
                    this.reviews = response.data.reviews;
                    this.loading_reviews = false;
                })
                .catch((error) => {
                    console.log(error);
                    this.loading_reviews = false;
                });
        },
    }
}

</script>
