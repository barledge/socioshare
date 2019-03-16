<template>

<form v-on:submit.prevent="onSubmit">


    <p>{{ date }}</p>
  <div class="row">

      <div class="col-sm-7">
  <h4>Editing post <b>#{{post.id}}</b> <span class="badge">{{post.type}}</span></h4><br />
</div>

<div class="col-sm-5" v-if="global_status != 'SENT'">
  <p style="    margin-top: 9px; " class="pull-right">Fine-tune for each platform

  <toggle-button
              :value="fine_tune"
              :color="{checked: '#3FB618', unchecked: '#ddd'}"
              :sync="true"
              :labels="{checked: 'YES', unchecked: 'NO'}"
              @change="updateFineTune"/>
            </p>

</div>

</div>

    <div v-if="!fine_tune" >
      <div class="row">

          <div class="col-sm-8">


      <div class="panel panel-default mb-0" data-mh>

          <div class="panel-body" :class="{ 'disabled-area': global_status == 'SENT' }">


        <div class="row">

            <div class="col-sm-12">
                <div class="form-group">
                  <!--{{message}}
                    <textarea class="form-control" v-model="message" rows="8" placeholder="The message you want to share..."></textarea>-->
                    <TextWidget :my-message.sync="message" :files="files"></TextWidget>
                </div>

            </div>

        </div>
        <div class="row">
          <div class="col-sm-12">
            <!--{{files}}-->
          <upload :my-files.sync="files" :scheduled.sync="scheduled_at" wide="col-sm-12"></upload>
        </div>

        <div class="col-sm-12" v-if="selected_groups && selected_groups.length > 0">
            <div class="pretty">
                <input type="checkbox" v-model="requires_approval" />
                <label><i class="mdi mdi-check"></i> <small>Must be approved by client</small></label>
            </div>
            <a href="" v-if="requires_approval" @click.prevent="commentsOpen=true"><i class="fa fa-comments" aria-hidden="true" style="color: #8eb4cb"></i></a>
        </div>

        </div>


        </div>

        </div>
            <div class="panel panel-default " data-mh>

              <div class="panel-body">
    <div class="row">

        <div class="col-sm-12" v-if="is_draft">
            <button type="button" v-on:click.prevent="saveDraft()" class="btn btn-default">Save draft</button>

            <!-- Split button -->
            <div class="btn-group">
              <button type="button" v-on:click.prevent="addToQueue()" class="btn btn-primary">Add to {{ (scheduled_at)?'schedule':'queue'  }}</button>
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="#" v-on:click.prevent="postNow()">Post now</a></li>
              </ul>
            </div>

        </div>

        <div class="col-sm-12  " v-else-if="!is_draft && global_status != 'SENT'">
            <button type="button" v-on:click.prevent="saveDraft()" class="btn btn-default">Remove from {{ (scheduled_at)?'schedule':'queue'  }}</button>
            <button type="button" v-on:click.prevent="saveChanges()" class="btn btn-primary">Save changes</button>
        </div>

        <div class="col-sm-12" v-else>
            <button type="button" disabled class="btn btn-primary">Already posted</button>
        </div>


    </div>

    </div>
    </div>


    <div class="row" v-if="selection_count > 0">

        <div class="col-sm-12">
          <br />
          <h6>Selection:</h6>
          <span class="badge" v-for="social_account in social_accounts" v-if="social_account.active"><i class="mdi" :class="['mdi-' + social_account.platform]"></i> {{social_account.name}}</span>

        </div>

    </div>

        </div>




          <div class="col-sm-4">
            <account-selector v-on:update="updateSelection" :my-groups=post.groups :my-accounts=social_accounts :default-accounts=socialAccounts :default-groups=groups></account-selector>


                  </div>
                </div>


</div>

    <div v-if="fine_tune">

                    <!-- Nav tabs -->
                    <div class="row" v-if="current_account != null">
                        <div class="col-sm-8">
                      <ul class="nav nav-tabs" role="tablist" style="margin-bottom: -1px;border-bottom: none;" v-if="current_account.social_account">
                        <li role="presentation" class="active"><a  style="background: #fff; border-bottom: none; " href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="mdi" :class="['mdi-' + current_account.social_account.platform]"></i> {{current_account.social_account.name}} <strong class="text-danger" v-if="current_account.status == 'SENT'">{{current_account.status}}</strong></a></li>
                      </ul>
                    </div>
                    </div>
        <div class="row">
            <div class="col-sm-8" >
            <div v-if="current_account != null">

              <div v-if="addingContent" class="text-center" style="padding: 15px; background: #fff; border : 1px solid #ddd">
                  <br />
                  <br />
                  <p><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></p>
                  <p>Please wait...</p>
                  <br />
                  <br />
              </div>
              <div v-else style="padding: 15px; background: #fff; border : 1px solid #ddd" :class="{ 'disabled-area': current_account.status == 'SENT' }">
                  <div class="row">

                      <div class="col-sm-12">
                          <div class="pretty">
                              <input type="checkbox" v-model="current_account.active" v-on:change="updateAccount(current_account.social_account.id, current_account.active)" />
                              <label><i class="mdi mdi-check"></i> Post to {{current_account.social_account.label}} ({{current_account.social_account.platform}})</label>
                          </div>

                          <div class="form-group">
                              <!--<textarea class="form-control" v-model="current_account.message" rows="8" placeholder="The message you want to share..."></textarea>-->
                              <TextWidget :my-message.sync="current_account.message" :files="current_account.files"></TextWidget>
                              <!--{{current_account.message}}-->
                          </div>
                      </div>

                      <div class="col-sm-12">
                        <upload :my-files.sync="current_account.files" :scheduled.sync="current_account.scheduled_at_formatted" wide="col-sm-12"></upload>
                      </div>

                      <div class="col-sm-12" v-if="current_account.social_account.platform == 'instagram'">
                          <div class="pretty">
                              <input type="checkbox"  v-model="current_account.meta.instagram_story" v-on:change="updateAccountMeta(current_account, current_account.meta)" />
                              <label><i class="mdi mdi-check"></i> <small>Post as instagram story</small></label>
                          </div>
                      </div>

                      <div class="col-sm-12" v-if="selected_groups && selected_groups.length > 0">
                          <div class="pretty">
                              <input type="checkbox" v-model="requires_approval" />
                              <label><i class="mdi mdi-check"></i> <small>Must be approved by client</small></label>
                          </div>
                          <a href="" v-if="requires_approval" @click.prevent="commentsOpen=true"><i class="fa fa-comments" aria-hidden="true" style="color: #CFD8DC"></i></a>
                      </div>


                  </div>
              </div>

                  <div class="panel panel-default " data-mh v-if="!addingContent">

              <div class="panel-body">
    <div class="row">

        <div class="col-sm-12" v-if="is_draft">

            <button type="button" v-on:click.prevent="saveDraft()" class="btn btn-default">Save draft</button>

            <!-- Split button -->
            <div class="btn-group">
              <button type="button" v-on:click.prevent="addToQueue()" class="btn btn-primary">Add to {{ (current_account.scheduled_at_formatted)?'schedule':'queue'  }}</button>
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="#" v-on:click.prevent="postNow()">Post now</a></li>
              </ul>
            </div>

            <!--<button type="button" v-on:click.prevent="addToQueue()" class="btn btn-primary">Add to queue</button>-->
        </div>

        <div class="col-sm-12" v-if="!is_draft">
            <button type="button" v-on:click.prevent="saveDraft()" class="btn btn-default">Remove from queue</button>
            <button type="button" v-on:click.prevent="saveChanges()" class="btn btn-primary">Save changes</button>
        </div>


    </div>

    </div>
    </div>



            </div>
              <div v-else>

                <div class="row" >
                    <div class="col-sm-8">
                  <ul class="nav nav-tabs" role="tablist" style="margin-bottom: -1px;border-bottom: none;" >
                    <li role="presentation" class="active"><a  style="background: #fff; border-bottom: none; " href="#home" aria-controls="home" role="tab" data-toggle="tab">Error</a></li>
                  </ul>
                </div>
                </div>
                      <div class="panel panel-default " data-mh>
                        <div class="panel-body text-center" style="height: 60vh;">
                          <div class="col-sm-8 col-sm-offset-2 mt-s">
                            <p><i class="fa fa-exclamation fa-3x" aria-hidden="true"></i></p>
                              <p>No accounts selected. Please select at least one account.</p>
                            </div>
                        </div>
                    </div>
              </div>
            </div>

            <div class="col-sm-4" style="">

              <a href="" @click.prevent="showAccountSelector" v-if="!account_selector_visible" class="btn btn-info  btn-xs">Add/remove accounts <i class="fa fa-angle-double-down" aria-hidden="true"></i></a>
              <a href="" @click.prevent="hideAccountSelector" v-if="account_selector_visible" class="btn btn-info btn-xs btn-top-rounded">Hide accounts selector <i class="fa fa-angle-double-up" aria-hidden="true"></i></a>

              <account-selector v-on-clickaway="hideAccountSelector" class="floating-account-selector" v-if="account_selector_visible" v-on:update="updateSelection" :my-groups=post.groups :my-accounts=social_accounts :default-accounts=socialAccounts :default-groups=groups></account-selector>

              <p v-if="selection_count == 0">
                <br />
                <small><strong>Notice!</strong> No accounts selected. Please select at least one by clicking on the button above.</small>
              </p>
              <div class="scroll arrow-menu mt-xs" style="background: transparent;height: 300px;  margin-left: -31px; padding-right: 31px">


                <a href="#" v-for="platform in social_accounts" @click.prevent="selectPlatform(platform)" class="platform-btn truncate" :class="{ active: current_account != null && current_account.social_account_id == platform.id, hidden: (!platform.active) }" style="padding-right: 31px">
                  <span :style="{textDecoration: (~selected_scheduled_accounts.indexOf(platform.id))?'':'line-through'}"><i class="mdi" :class="['mdi-' + platform.platform]"></i> {{platform.label}}</span>
                </a>


              </div>




            </div>


        </div>
    </div>
    </div>


    <edit-post-comments :post="post" :comment-open.sync="commentsOpen"></edit-post-comments>

</form>

</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';

var FileUpload = require('vue-upload-component');

export default {
    mixins: [ clickaway ],
    props: ['post', 'socialAccounts', 'groups'],
    data: () => ({
        commentsOpen: false,
        time: null,
        redirect: '/content/',
        selected_accounts: [],
        addingContent: false,
        date: null,
        showAll: true,
        current_account: {},
        selected_scheduled_accounts: [],
        selected_groups: [],
        scheduled_posts: [],
        social_accounts: [],
        scheduled_at: null,
        message: "",
        global_status: "UNSENT",
        fineTuneWarningShown: false,
        fine_tune: false,
        is_draft: false,
        requires_approval: false,
        account_selector_visible: false,
        selection_count: 0,
        selection: [],
        files: []
    }),
    components: {
        FileUpload
    },
    computed: {
      // a computed getter
      selectedAccounts: function () {
        // `this` points to the vm instance
        return 5
      }
    },
    events: {

    },
    watch: {
        social_accounts: function(val, oldVal) {
console.log('social_accounts',
val.filter(account => account.active).reduce( (ids, account) => ids.concat([account.id]), []),
oldVal.filter(account => account.active).reduce( (ids, account) => ids.concat([account.id]), [])
);

        },
        post: function(val, oldVal) {

        }
    },
    beforeCreate() {
      //console.log('beforeCreate', this.post);
    },
    created() {
      //console.log('created', this.post);
    },
    beforeMount() {
      //console.log('beforeMount', this.post);
      //console.log('selection', 'beforeMount');
      this.message = this.post.message;

      this.files = this.post.files;
    },
    updated() {
      //console.log('updated', this.post);
    },
    mounted() {
      //console.log('mounted', this.files);


        if(this.socialAccounts !== undefined){

            this.selected_accounts = this.socialAccounts;
            this.social_accounts = this.socialAccounts;
            //console.log('this.social_accounts', this.social_accounts);
            let social_account_ids = this.post.scheduled_posts.reduce(function(ids, post) {
                if(post.active)
                  ids.push(post.social_account_id);
                return ids;
            }, []);
            //console.log('social_account_ids', social_account_ids);
            this.selected_accounts.map(platform => {
                platform.active = social_account_ids.includes(platform.id);
            });

        }
        if (this.post !== undefined) {
          //console.log('post', this.post.files);

            this.message = this.post.message;
            this.fine_tune = this.post.fine_tune;
            this.is_draft = this.post.is_draft;
            this.requires_approval = this.post.requires_approval;
            this.twitter = this.post.twitter;
            this.facebook = this.post.facebook;
            this.instagram = this.post.instagram;
            //this.files = this.post.files;
            this.scheduled_at = this.post.scheduled_at_formatted;
            this.scheduled_posts = this.post.scheduled_posts;
            this.selected_groups = this.post.groups;

            let sent_posts = this.post.scheduled_posts.filter(function(post) {
                return post.status == "SENT";
            });

            if( sent_posts.length > 0 ) {
              this.global_status = "SENT";
            }
        }
        this.selected_scheduled_accounts = this.selectedScheduledAccounts();

        if(this.post.fine_tune) {
          let first = this.social_accounts.find(function(post){
            return post.active;
          });
          console.log('first', this.social_accounts);
          this.selectPlatform(first);



          let social_account_ids = this.post.scheduled_posts.reduce(function(ids, post) {
              ids.push(post.social_account_id);
              return  ids;
          }, []);


          this.social_accounts.map((platform, index) => {
              let scheduled_post = this.scheduled_posts.find(function(post){
                return post.social_account_id == platform.id;
              });
              if (this.post !== undefined) {
                this.social_accounts[index].active = Boolean(~this.post.social_account_list.indexOf(platform.id));
              } else {
                this.social_accounts[index].active = scheduled_post.active;
              }
              this.$set(this.social_accounts, platform, this.social_accounts[index]);
          });

        }

        this.selection_count = this.social_accounts.reduce((acc, row) => {
          return acc + (typeof row.active != 'undefined' && row.active);
        }, 0);

        let selected_social_accounts = this.selectedSocialAccounts();
        if(selected_social_accounts.length == 0) {
          this.current_account = null;
        } else {
          if(this.current_account != null && selected_social_accounts.indexOf(this.current_account.social_account_id) == -1) {
            this.selectPlatformBySocialAccountId(selected_social_accounts[0]);
          }
        }
        this.selected_scheduled_accounts = this.selectedScheduledAccounts();

        //console.log(this.post);
        //console.log('Component mounted.');
    },
    created() {
        //console.log('Component created.');


    },
    methods: {
        dismissCallback (msg) {
      this.msg = `Modal dismiss with msg '${msg}'.`
  },
      showAccountSelector: function() {
        this.account_selector_visible = true;
      },
      hideAccountSelector: function() {
        this.account_selector_visible = false;
      },
      updateSelection: function(selection) {

        if(typeof selection == 'undefined')
          return;

        //console.log('selection', selection, this.social_accounts, typeof selection, this.current_account);

        //find the newly inserted account
        let new_accounts = selection[0].filter(account => account.active).reduce( (ids, account) => ids.concat([account.id]), []);
        let old_accounts = this.social_accounts.filter(account => account.active).reduce( (ids, account) => ids.concat([account.id]), []);
        console.log('new_accounts', new_accounts, old_accounts, new_accounts.filter(x => old_accounts.indexOf(x) < 0 ));

        //sync
        this.social_accounts = selection[0];
        this.selected_groups = selection[1];

        this.selection_count = this.social_accounts.reduce((acc, row) => {
          return acc + (typeof row.active != 'undefined' && row.active);
        }, 0);
        /*
        this.social_accounts.map((social_account, myIndex) => {

          let myPlatform = this.social_accounts.findIndex(function(platformNew, index) {
              return platformNew.id == social_account.id;
          });

        })
        */
        let selected_social_accounts = this.selectedSocialAccounts();
        if(selected_social_accounts.length == 0) {
          this.current_account = null;
        } else {
          if(this.current_account != null && selected_social_accounts.indexOf(this.current_account.social_account_id) == -1) {
            this.selectPlatformBySocialAccountId(selected_social_accounts[0]);
          }
        }

        //cycle the
        this.selected_scheduled_accounts = this.selectedScheduledAccounts();

      },
      selectedScheduledAccounts: function() {

        return this.scheduled_posts.reduce((accumulator, scheduled_post) => {
          //console.log(scheduled_post.social_account_id, scheduled_post.active);
          if(scheduled_post.active) {
            accumulator.push(scheduled_post.social_account_id);
          }
          return accumulator;
        }, []);

      },
      selectPlatformBySocialAccountId: function (social_account_id) {
        let postIndex = this.scheduled_posts.findIndex(function(scheduled_post, j) {
            return social_account_id == scheduled_post.social_account_id;
        });
        this.current_account = this.scheduled_posts[postIndex];
      },
      selectedSocialAccounts: function () {
        return this.social_accounts.reduce((accumulator, social_account) => {
          if(social_account.active) {
            accumulator.push(social_account.id);
          }
          return accumulator;
        }, []);
      },
      selectFirstScheduledPost: function () {

      },
      updateFiles: function (files, param) {
          this.files = files;
      },
      syncScheduledPosts: function () {
        this.social_accounts.map((social_account, i) => {
            let postIndex = this.scheduled_posts.findIndex(function(scheduled_post, j) {
                return social_account.id == scheduled_post.social_account_id;
            });
            if(postIndex > -1) {
              this.scheduled_posts[postIndex].active = social_account.active;
              this.$set(this.scheduled_posts, postIndex, this.scheduled_posts[postIndex])

              //console.log('post.label', post.label);
              //post.active = social_account.active;
            }
        });
      },
      updateAccountMeta: function (current_account, meta) {
          console.log('meta', meta);
          this.current_account.meta.instagram_story = meta.instagram_story;

          let postIndex = this.scheduled_posts.findIndex(function(scheduled_post, j) {
              return current_account.id == scheduled_post.id;
          });
          this.$set(this.scheduled_posts, postIndex, this.current_account);

          //this.current_account.meta = meta.slice(0);
          //if('instagram_story' in meta) {
                //this.$set(this.current_account.meta, 'instagram_story', !this.current_account.meta.instagram_story);
                //this.$set(this.scheduled_posts[postIndex].meta, 'instagram_story', !this.current_account.meta.instagram_story);
          //}
          //console.log('meta', this.scheduled_posts[postIndex].meta);
          //this.$set(this.scheduled_posts[postIndex], 'meta', meta);
          //this.current_account = this.scheduled_posts[postIndex];
          //this.$forceUpdate(); //bad but works
          //console.log('this.scheduled_posts', this.scheduled_posts[postIndex]);
      },
      updateAccount: function (platformId, status) {
        /*let platform = this.social_accounts.findIndex(function(platform, index) {
            return platform.id == platformId;
        });
        this.social_accounts[platform].canPost = status;
        this.$set(this.social_accounts, platform, this.social_accounts[platform]);*/

        let postIndex = this.scheduled_posts.findIndex(function(scheduled_post, index) {
            return scheduled_post.social_account_id == platformId;
        });

        this.scheduled_posts[postIndex].active = status;
        this.$set(this.scheduled_posts, postIndex, this.scheduled_posts[postIndex])

        this.selected_scheduled_accounts = this.selectedScheduledAccounts();
        //console.log('selected_scheduled_accounts', this.selected_scheduled_accounts);

      },
      selectPlatform: function (platform) {

console.log(platform);
        //platform
        let current_account = this.scheduled_posts.findIndex(function(post){
          return post.social_account_id == platform.id;
        });

        //console.log('current_account', current_account, this.scheduled_posts[current_account]);

        if(current_account > -1) {
          this.current_account = this.scheduled_posts[current_account];
          console.log('this.current_account', this.current_account);
          //this.updateAccount(this.current_account.social_account_id, true);
          //this.syncScheduledPosts();
          //this.current_account =  Object.assign({}, this.scheduled_posts[current_account]);

        } else {
          //alert("Error: You cannot activate this account because it wasn't present at the time of creation.");
          this.addingContent = true;
          axios.post( site_url('/content/add'), {
                  social_account_id: platform.id,
                  post_id: this.post.id
              })
              .then((response) => {
                  this.fine_tune = response.data.post.fine_tune;
                  this.scheduled_posts = response.data.post.scheduled_posts;
                  this.addingContent = false;

                  let current_account = this.scheduled_posts.findIndex(function(post){
                    return post.social_account_id == platform.id;
                  });
                  this.current_account = this.scheduled_posts[current_account];
              })
              .catch((error) => {
                  console.log(error);
                  alert("Error: You cannot activate this account because it wasn't present at the time of creation.");
              });

        }

        console.log('current_account', current_account);
        return;

      },
      togglePlatform: function (platform) {
        platform.active = !platform.active;
        let myPlatform = this.social_accounts.findIndex(function(platformNew, index) {
            return platformNew.id == platform.id;
        });
        this.$set(this.social_accounts, myPlatform, this.social_accounts[myPlatform]);
      },
            updateGeneralDate: function (date) {
              this.scheduled_at = date;
            },
            updateDate: function (index, date) {
              this.scheduled_posts[index].scheduled_at = date;
            },
            inputFile(newFile, oldFile) {
                this.$refs.upload.active = true
            },
            removeFile(index) {
                this.files.splice(index, 1);
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
            updateFineTune(i) {


              this.fine_tune = i.value;
              if(this.fine_tune) {
                //console.log('myPlatforms', this.myPlatforms);t.find(()=>true)
                console.log('this.social_accounts', this.social_accounts);
                let social_account = this.social_accounts.find((social_account)=>social_account.active);
                if(!social_account) {
                  social_account = this.social_accounts.find((social_account)=>true);
                }
                //console.log('social_accountsocial_accountsocial_account', social_account);
                this.selectPlatform(social_account);
              } else {
                if(!this.fineTuneWarningShown) {
                  this.fineTuneWarningShown = true;
                  alertify.alert("Note: You will loose all entered content for individual social media accounts when you click 'save'. Re-check 'fine-tune' to keep the entered content for individual accounts.");
                }
              }

            },
            fineTune() {
                this.is_draft = true;
                this.fine_tune = true;
                this.onSubmit();
            },
            saveDraft() {
                this.redirect = '/drafts';
                this.is_draft = true;
                this.onSubmit();
            },
            postNow() {

                this.redirect = '/queue/create?tab=custom_schedule';
                this.is_draft = false;
                if(!this.fine_tune) {
                    this.scheduled_at = 'NOW';
                } else {
                    this.scheduled_posts.map((scheduled_post, i) => {
                        console.log(i);
                        if(!scheduled_post.scheduled_at)
                            scheduled_post.scheduled_at = 'NOW';

                        this.$set(this.scheduled_posts, scheduled_post, this.scheduled_posts[i]);
                        console.log(scheduled_post);
                    });
                    //this.$forceUpdate();
                    console.log('this.scheduled_posts', this.scheduled_posts);
                }
                this.onSubmit();
            },
            saveChanges() {
                this.redirect = null;
                this.is_draft = false;
                this.onSubmit();
            },
            addToQueue() {
                this.redirect = '/queue/create';
                this.is_draft = false;
                this.onSubmit();
            },
            onSubmit() {

              if(this.selected_accounts.length == 0) {
                alertify.error("Please select at least one account.");
                return false;
              }
                //this.$Progress.start();
                console.log(this.$Progress);
                axios.put(site_url('/content/' + this.post.id), {
                        fine_tune: this.fine_tune,
                        is_draft: this.is_draft,
                        groups: this.selected_groups,
                        message: this.message,
                        scheduled_at: this.scheduled_at,
                        requires_approval: this.requires_approval,
                        scheduled_posts: this.scheduled_posts,
                        social_accounts: this.social_accounts,
                        files: this.files
                    })
                    .then((response) => {
                        this.fine_tune = response.data.post.fine_tune;
                        this.scheduled_posts = response.data.post.scheduled_posts;
                        alertify.log("Successfully saved!");

                        if(this.redirect) {
                            window.location.href = this.redirect;
                        }

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
