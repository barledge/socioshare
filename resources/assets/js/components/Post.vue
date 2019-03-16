

<template>
  <form v-on:submit.prevent="onSubmit">


                <div class="row">

                    <div class="col-sm-8">

<div class="panel  panel-default equalize" data-mh>

    <div class="panel-body">

            <div class="row">

                <div class="col-sm-12">

                    <div class="form-group">
                        <!--<trumbowyg v-model="message" :config="trumbowyg_config"  placeholder="The message you want to share..."></trumbowyg>-->
                        <TextWidget :my-message.sync="message" :files="files"></TextWidget>
                    </div>


                </div>

            </div>


            <div class="row">
                <div class="col-sm-12">
                    <upload :my-files.sync="files" :scheduled.sync="scheduled_at" wide="col-sm-12 "></upload>
                </div>

                <div class="col-sm-12" v-if="selected_groups && selected_groups.length > 0">
                    <div class="pretty">
                        <input type="checkbox" v-model="requires_approval" />
                        <label><i class="mdi mdi-check"></i> Must be approved by client</label>
                    </div>
                </div>

            </div>



                    <div class="row">

                        <div class="col-sm-12">
                            <div class="pretty">
                                <input type="checkbox" v-model="fine_tune" />
                                <label><i class="mdi mdi-check"></i> Fine-tune for each platform</label>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-xxs" v-if="fine_tune">
                            <button type="button" v-on:click.prevent="fineTune()" class="btn btn-primary">Continue <i class="fa fa-angle-right" aria-hidden="true"></i></button>
                        </div>
                        <div class="col-sm-12 mt-xxs" v-if="!fine_tune">
                            <button type="button" v-on:click.prevent="saveDraft()" class="btn btn-default">Save draft</button>

                            <!-- Split button -->
                            <div class="btn-group">
                              <button type="submit" class="btn btn-primary">Add to {{ (scheduled_at)?'schedule':'queue'  }}</button>
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                              </button>
                              <ul class="dropdown-menu">
                                <li><a href="#" v-on:click.prevent="postNow()">Post now</a></li>
                              </ul>
                            </div>


                        </div>

                    </div>





    </div>

</div>


                    <div class="row" v-if="selection_count > 0">

                        <div class="col-sm-12">
                          <h6>Selection:</h6>
                          <span class="badge badge-info mr-xxxs" style="" v-for="social_account in social_accounts" v-if="social_account.active"><i class="mdi" :class="['mdi-' + social_account.platform]"></i> {{social_account.name}}</span>

                        </div>

                    </div>


</div>

    <div class="col-sm-4">

        <account-selector v-on:update="updateSelection" :default-accounts="socialAccounts" :default-groups="groups"></account-selector>

      </div>
    </div>
</div>
</form>

</template>

<script>
import VueLocalStorage from 'vue-localstorage'
import datePicker from 'vue-bootstrap-datetimepicker';
Vue.use(VueLocalStorage)

export default {
    props: ['post', 'socialAccounts', 'groups'],
    data: () => ({
        myPlatforms: [],
        selection_count: 0,
        social_accounts: [],
        selected_groups: [],
        scheduled_at: null,
        message: "",
        fine_tune: false,
        requires_approval: false,
        trumbowyg_config: {
          btns: [],
          autogrow: false
        },
        is_draft: false,
        showPreview: true,
        previewLoading: false,
        previewText: "",
        preview: {},
        files: []
    }),
    watch: {

    },
    mounted() {
        if (this.platforms !== undefined) {
            this.myPlatforms = this.platforms;
            this.myPlatforms.map(platform => platform.active = true);
        }

        if (this.post !== undefined) {
            this.message = this.post.message;
            this.fine_tune = this.post.fine_tune;
            this.is_draft = this.post.is_draft;
            this.twitter = this.post.twitter;
            this.facebook = this.post.facebook;
            this.instagram = this.post.instagram;
            this.files = this.post.files;
            //this.scheduled_at = this.post.scheduled_at;
            this.scheduled_at = this.post.scheduled_at_formatted;
        }
        console.log(this.post);
        console.log('Component mounted.');
    },
    created() {
        console.log('Component created.');
    },
    methods: {
        togglePlatform: function(platform) {
            platform.active = !platform.active;
            this.myPlatforms = Object.assign({}, this.myPlatforms)
        },
        postNow() {
            this.is_draft = false;
            this.scheduled_at = 'NOW';
            this.onSubmit();
        },
        updateSelection: function(selection) {
          this.social_accounts = selection[0];
          this.selected_groups = selection[1];

          this.selection_count = this.social_accounts.reduce((acc, row) => {
            return acc + (typeof row.active != 'undefined' && row.active);
          }, 0);
        },
        updateFiles: function(files) {
            console.log('Component mounted.');

            this.files = files
        },
        removeTime(index) {
            this.times.splice(index, 1);
        },
        addTime() {
            this.times.push({
                hour: 8,
                minute: 5
            });
        },
        fineTune() {
            this.is_draft = true;
            this.fine_tune = true;
            this.onSubmit();
        },
        saveDraft() {
            this.is_draft = true;
            this.onSubmit();
        },
        onSubmit() {
            let groups = this.groups;

            if(this.message.length == 0) {
              alertify.error("Please enter a message.");
              return false;
            }

            if(this.selection_count == 0) {
              alertify.error("Please select at least one account.");
              return false;
            }

            this.$localStorage.remove('group');
            this.$localStorage.remove('social_account_id');

            axios.post(site_url('/content'), {
                    fine_tune: this.fine_tune,
                    is_draft: this.is_draft,
                    requires_approval: this.requires_approval,
                    message: this.message,
                    social_accounts: this.social_accounts,
                    groups: this.selected_groups,
                    scheduled_at: this.scheduled_at,
                    files: this.files
                })
                .then((response) => {
                    console.log(response.data.post.id);
                    if (response.data.post.is_draft && !response.data.post.fine_tune) {
                        window.location.href = site_url("/drafts/");
                    } else if (response.data.post.fine_tune) {
                        window.location.href = site_url("/content/" + response.data.post.id + '/edit');
                    } else {
                        let target = (response.data.post.scheduled_at)?'custom_schedule':'in_queue';
                        window.location.href = "/queue/create?tab=" + target;
                    }
                    alertify.log("Successfully saved!");
                })
                .catch((error) => {
                    if(error.response && error.response.data && error.response.data.error) {
                      alertify.alert(error.response.data.error);
                    } else {
                      alertify.alert(error);
                    }
                });

        }
    }
}

</script>
