<template>


<div>

  <ul class="nav nav-tabs">
      <li :class="{active: tab == 'in_queue'}"><a href="#" @click.prevent="setTab('in_queue')" >In queue</a></li>
      <li :class="{active: tab == 'custom_schedule'}"><a href="#"  @click.prevent="setTab('custom_schedule')" >Custom schedule</a></li>
      <li class="pull-right"><a href="/schedule" class="pull-right text-muted"><i class="fa fa-calendar" aria-hidden="true"></i> Edit posting times</a></li>
  </ul>

  <div class="panel panel-default " data-mh>
    <div class="panel-body" v-if="tab == 'in_queue'">
      <p>The posts here will be posted at their scheduled times. You can still edit or delete the post provided it is before the scheduled time.</p>


      <div class="row">
        <div class="col-sm-4">
          <select v-model="selectedGroup" class="form-control" @change.prevent="filterGroupsAccounts">
            <option value="0">All groups/clients</option>
            <option v-for="group in groups" v-bind:value="group.id">
              {{ group.name }}
            </option>
          </select>
        </div>
        <div class="col-sm-4">
          <select v-model="selectedAccount" class="form-control" @change.prevent="fetchPlatformQueue">
            <option value="0">All social accounts ({{groupAccounts.length}})</option>
            <option v-for="account in groupAccounts" v-bind:value="account.id">
              {{ account.name }}
            </option>
          </select>
        </div>
        <div class="col-sm-4">
          <button class="btn btn-default" @click.prevent="fetchPlatformQueue">Go</button>
        </div>
      </div>
      <hr />


            <div class="alert alert-warning mb-0" v-if="queued_posts.length == 0 && !loading">
                <a href="/content/create" class="btn btn-xs btn-warning pull-right">add one now</a>
                <strong>Oops!</strong> You don't have any posts queued at the moment.
            </div>

      <table class="table table-striped table-clickable" v-if="queued_posts.length > 0 && listings == 'queue'">
<thead>
        <tr>
          <th class="col-sm-2"></th>
          <th class="col-sm-5">Message</th>
          <th class="col-sm-2">Platform</th>
          <th class="col-sm-2">Queued for</th>
          <th class="col-sm-1"></th>
        </tr>
      </thead>
         <tbody>
           <template v-for="(post, index) in queued_posts">
             <tr >
               <td v-on:click="editPost(post)">
                  <div v-if="post.files && post.files.length == 0">
                    <a :href="'/content/'+post.id+'/edit'" class="" v-on:click.prevent="editPost(post)"><img :src="'/img/no_image.png' | asset" style="width: 90%; border-radius: 18px;" class="p-xxs "></a>
                  </div>
                  <div v-if="post.files && post.files[0] && post.files[0].response">
                    <a :href="'/content/'+post.id+'/edit'" class="" v-on:click.prevent="editPost(post)"><img :src="post.thumb" style="width: 90%; border-radius: 18px;"  alt="" class="p-xxs"></a>
                  </div>
               </td>
               <td style="position: relative">
                 <strong class="mr-xxxs">{{post.post_id}}.</strong><span v-html="post.snippet"></span>
                 <i v-if="post.post.requires_approval && post.post.approval_status != 'APPROVED'">(Pending approval)</i>
                 <br /><br />

                 <div style="position: absolute; bottom: 6px; left: 10px;">
                 <a :href="'/content/'+post.post_id+'/edit'" class="" v-on:click.prevent="editPost(post)"><small>edit</small></a>
                 <template v-if="!post.scheduled_at">
                   |
                   <template v-if="selectedAccount && selectedAccount > 0">
                    <a href="" v-on:click.stop.prevent="moveUp(post)"><small>move up</small></a> | <a href="" v-on:click.stop.prevent="moveDown(post)"><small>move down</small></a>
                   </template>
                   <template v-else>
                    <a v-on:click.stop.prevent="moveUpPrevent(post)" class="text-muted" title="Select a specific account to move"><small>move up</small></a> | <a  v-on:click.stop.prevent="moveDownPrevent(post)" title="Select a specific account to move" class="text-muted"><small>move down</small></a>
                   </template>

                 </template>
                 </div>
               </td>
               <td><span class="badge" :class="[post.social_account.platform + '-bg']"><i class="fa" :class="['fa-' + post.social_account.platform]"></i> {{post.social_account.label}}</span></td>
               <td v-if="!post.scheduled_at">{{post.queued_for_human}}</td>
               <td v-else>{{post.scheduled_at_human}} {{post.position}}</td>

               <td>
                 <a data-toggle="tooltip"  data-placement="right" title="Remove from Queue" href="" v-on:click.prevent="deletePost(post)"><i class="text-muted mdi mdi-close" aria-hidden="true"></i></a></td>
             </tr>
           </template>

         </tbody>
       </table>

    </div>

    <div class="panel-body" v-if="tab == 'custom_schedule'">
      <p>The posts here will be posted at their scheduled times. You can still edit or delete the post provided it is before the scheduled time.</p>

      <div class="row">
        <div class="col-sm-4">
          <select v-model="selectedGroup" class="form-control" @change.prevent="filterGroupsAccounts">
            <option value="0">All groups/clients</option>
            <option v-for="group in groups" v-bind:value="group.id">
              {{ group.name }}
            </option>
          </select>
        </div>
        <div class="col-sm-4">
          <select v-model="selectedAccount" class="form-control" @change.prevent="fetchPlatformQueue">
            <option value="0">All social accounts ({{groupAccounts.length}})</option>
            <option v-for="account in groupAccounts" v-bind:value="account.id">
              {{ account.name }}
            </option>
          </select>
        </div>
        <div class="col-sm-4">
          <button class="btn btn-default" @click.prevent="fetchPlatformQueue">Go</button>
        </div>
      </div>
      <hr />

      <div class="alert alert-warning mb-0" v-if="scheduled_posts.length == 0 && !loading">
          <a href="/content/create" class="btn btn-xs btn-warning pull-right">add one now</a>
          <strong>Oops!</strong> You don't have any scheduled posts.
      </div>

      <table class="table table-striped table-clickable" v-if="scheduled_posts.length > 0">
        <thead>
        <tr>
          <th class="col-sm-2"></th>
          <th class="col-sm-5">Message</th>
          <th class="col-sm-2">Platform</th>
          <th class="col-sm-2">Scheduled&nbsp;for</th>
          <th class="col-sm-1"></th>
        </tr>
        </thead>
         <tbody>
           <template v-for="(post, index) in scheduled_posts">
             <tr >
               <td v-on:click.prevent="editPost(post)">
                  <div v-if="post.files && post.files.length == 0">
                    <a :href="'/content/'+post.post_id+'/edit'" class="" v-on:click.prevent="editPost(post)"><img :src="'/img/no_image.png' | asset" style="width: 90%; border-radius: 18px;" class="p-xxs "></a>
                  </div>
                  <div v-if="post.files && post.files[0] && post.files[0].response">
                    <a :href="'/content/'+post.post_id+'/edit'" class="" v-on:click.prevent="editPost(post)"><img :src="post.thumb" style="width: 90%; border-radius: 18px;"  alt="" class="p-xxs"></a>
                  </div>
               </td>
               <td style="position: relative">
                 <strong class="mr-xxxs">{{post.post_id}}.</strong><span v-html="post.snippet" v-on:click.prevent="editPost(post)"></span>
                 <i v-if="post.post.requires_approval && post.post.approval_status != 'APPROVED'">(Pending approval)</i>
                 <br /><br />

                 <div style="position: absolute; bottom: 6px; left: 10px;">
                 <a :href="'/content/'+post.post_id+'/edit'" class="" v-on:click.prevent="editPost(post)"><small>edit</small></a>
                 <template v-if="!post.scheduled_at">
                  | <a href="" v-on:click.prevent="moveUp(post)"><small>move up</small></a> | <a href="" v-on:click.prevent="moveDown(post)"><small>move down</small></a>
                 </template>
                 </div>

               </td>
               <td><span class="badge" :class="[post.social_account.platform + '-bg']"><i class="fa" :class="['fa-' + post.social_account.platform]"></i> {{post.social_account.label}}</span></td>
               <td>{{post.scheduled_at_human}}</td>
               <td>
                 <a data-toggle="tooltip"  data-placement="right" title="Remove from Queue" href="" v-on:click.prevent="deletePost(post)"><i class="text-muted mdi mdi-close" aria-hidden="true"></i></a></td>
             </tr>
           </template>

         </tbody>
       </table>

    </div>

    </div>



    </div>
    </div>

  </div>
</div>

</template>

<script>
import VueLocalStorage from 'vue-localstorage'
Vue.use(VueLocalStorage)

var FileUpload = require('vue-upload-component');

export default {
    props: ['next', 'groups', 'socialaccounts', 'defaulttab'],
    data: () => ({
        platforms: ['in_queue', 'custom_schedule'],
        tab: 'in_queue',
        listings: 'queue',
        loading: true,
        selectedGroup: 0,
        selectedAccount: 0,
        groupAccounts: [],
        platformPosts: [],
        schedule: [],
        queued_posts: [],
        scheduled_posts: []
    }),
    components: {
        FileUpload
    },
    beforeMount() {
      this.selectedGroup = this.$localStorage.get('group', 0);
      this.selectedAccount = this.$localStorage.get('social_account_id', 0);
      this.tab = this.$localStorage.get('tab', 'in_queue');
    },
    mounted() {
        console.log('Component mounted.');
        this.fetchPlatformQueue();
        this.groupAccounts = this.socialaccounts;
        console.log(this.defaulttab);
        this.setTab(this.defaulttab);
        //this.setTab(this.$localStorage.get('tab', 'in_queue'))
    },
    watch: {
     // whenever question changes, this function will run
     activePlatform: function (newActivePlatform) {
       this.loading = true;
       this.fetchPlatformQueue();
     }
   },
    methods: {
            setTab(item) {
                this.tab = item;
                this.$localStorage.set('tab', item);
                this.fetchPlatformQueue();
            },
            moveUpPrevent(item) {
              alertify.alert("Please select a social media account in the dropdown above before moving items up/down");
            },
            moveDownPrevent(item) {
              alertify.alert("Please select a social media account in the dropdown above before moving items up/down");
            },
            moveDown(item) {
                this.saveOrder(item, 'down');
            },
            moveUp(item) {
              this.saveOrder(item, 'up');
            },
            saveOrder(item, direction) {
              axios.post(site_url('/queue/move/' + item.id), {
                  direction: direction
              })
              .then((response) => {
                  this.fetchPlatformQueue();
                  alertify.log("Moved item.");
              })
              .catch((error) => {
                  console.log(error);
                  alertify.alert(error);
              });
            },

            deletePost(post) {
              let id = 0;
              let endpoint = 'content';
              if(post.post_id) {
                id = post.id;
                endpoint = 'queue';
              } else {
                id = post.id;
              }

              alertify.confirm('Are you sure you want to delete post #' + id, () =>{
                alertify.success('Deleting');
                axios.delete(site_url('/'+endpoint+'/' + id))
                    .then((response) => {
                        this.fetchPlatformQueue();
                        alertify.success('Deleted!');
                    })
                    .catch((error) => {
                        console.log(error);
                        alertify.alert(error);
                    });

              }, () =>{
                alertify.error('Cancel')
              });

            },
            editPost(post) {

              if(post.post_id) {
                window.location.href = site_url("/content/"+post.post_id+"/edit");
              } else {
                window.location.href = site_url("/content/"+post.id+"/edit");
              }
            },
            filterGroupsAccounts() {
              this.selectedGroup = Number(this.selectedGroup)
                console.log(parseInt(this.selectedGroup));
                if(parseInt(this.selectedGroup) == 0) {
                  this.groupAccounts = this.socialaccounts;
                  this.fetchPlatformQueue();
                  return false;
                }

                let selectedGroup = this.groups.find((row) => {
                  return row.id == this.selectedGroup;
                });

                console.log('selectedGroup', selectedGroup);

                if(selectedGroup && selectedGroup.selection) {

                    let selection = [];
                    for (var key in selectedGroup.selection) {
                        if(selectedGroup.selection[key]) {
                          selection.push(parseInt(key));
                        }
                    }

                    this.groupAccounts = this.socialaccounts.filter((row) => {
                      return ~selection.indexOf(row.id);
                    });

                }

                this.fetchPlatformQueue();

            },
            fetchPlatformQueue() {
              console.log(this.groups);

              this.$localStorage.set('group', this.selectedGroup);
              this.$localStorage.set('social_account_id', this.selectedAccount);

              $('#busy').show()
                this.listings = 'queue';
                if(this.tab.toLowerCase().includes('schedule'))
                  this.listings = 'schedule'

                axios.post(site_url('/queue/listings/' + this.listings), {group: this.selectedGroup, social_account_id: this.selectedAccount})
                    .then((response) => {
                        if(this.listings == 'queue') {
                          this.queued_posts = response.data.posts;
                        } else {
                          this.scheduled_posts = response.data.posts;
                        }
                        this.schedule = response.data.schedule;
                        this.loading = false;
                        $('[data-toggle="tooltip"]').tooltip();
                        $('#busy').hide()
                    })
                    .catch((error) => {
                        console.log(error);
                        //alertify.alert(error);
                        $('#busy').hide()
                    });
            }
    }
}

</script>
