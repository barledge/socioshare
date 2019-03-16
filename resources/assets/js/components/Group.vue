<template>

<div>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
              <div class="panel-heading pr-1 pl-1" v-if="!group_search">
                Groups
                <a href="" @click.prevent="group_search = true" class="btn btn-link pull-right p-0"><i class="mdi mdi-magnify text-muted" style="color: #E0E0E0" aria-hidden="true"></i></a>
              </div>
              <div class="panel-heading pr-1 pl-1 clearfix" style="padding: 10px 5px" v-else>

                <form v-on:submit.prevent="searchPlatformTypes">
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control" v-model="group_query" placeholder="Search groups...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="submit"><i class="mdi mdi-magnify" aria-hidden="true"></i></button>
                    </span>
                  </div><!-- /input-group -->
                </form>
                <a href="" class="pull-right" @click.prevent="clearGroupSearch()" style="font-size: x-small;">cancel</a>

              </div>
              <div class="panel-body" v-if="filteredGroups.length == 0">
                <br />
                <div class="alert alert-warning text-center" role="alert">
                  <strong>Notice:</strong> You have no groups set-up yet. <a href="" @click.prevent="addGroupPopup()">Add one now</a>
                </div>
                    <br />
              </div>
              <div class="scroll" style="max-height: 325px; overflow: auto; padding-right: 0">

                <table class="table table-condensed table-" style="margin-bottom: 0">


                  <tbody>

                  <tr v-for="group in filteredGroups" :class="{ active: (current_group && group.id == current_group.id) }">
                    <td class="col-sm-10" style="cursor: pointer" @click.prevent="setCurrentGroup(group.id)">{{group.name}}</td>
                    <td class="col-sm-1">
                      <small class="text-muted">
                        <a :href="'/clients/'+group.user.id+'/edit'" v-if="group.user"><i class="mdi mdi-account" aria-hidden="true"></i></a>
                        <a data-toggle="tooltip" title="This group is not associated with any client" v-else><i class="mdi mdi-account text-muted" style="color: #E0E0E0" aria-hidden="true"></i></a>
                      </small>
                    </td>
                    <td class="col-sm-1"><small class="text-muted"><a href="#" @click.prevent="removeGroup(group)"><i class="mdi mdi-close" aria-hidden="true"></i></a></small></td>
                  </tr>
                </tbody>
                </table>
              </div>
                <form  v-on:submit.prevent="addGroup"  v-if="filteredGroups.length > 0">
                <div class="input-group  input-group-sm " >
                  <input type="text" class="form-control" placeholder="group name" v-model="group_name" style="border-radius: 0">
                  <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit" style="border-radius: 0" v-if="!adding">Add</button>
                    <button class="btn btn-primary" type="button" style="border-radius: 0" v-if="adding"><i class="fa fa-spinner fa-spin fa-fw"></i></button>
                  </span>
                </div><!-- /input-group -->
                </form>
            </div>
            <div v-if="filteredGroups.length > 0">
            <small>Manage your clients by grouping your social media accounts together.</small>
            <a href="/clients/create" class="btn btn-default btn-block"><i class="fa fa-plus" aria-hidden="true"></i> Add new client</a>
            </div>

        </div>


        <div class="col-sm-8">

        <div v-if="current_group">
          <ul class="nav nav-tabs">
            <li class="active" ><a href="#" style="background: #fff">{{current_group.name}}</a></li>
          </ul>

          <div class="panel panel-default" style="border-radius: 0 0 4px 4px">

            <div class="panel-body">


              <div class="row">

                    <div class="col-xs-12">
                      <form v-on:submit.prevent="searchPlatformTypes">
                        <div class="input-group input-group-sm">
                          <input type="text" class="form-control" v-model="query" placeholder="Search for an account...">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">Go!</button>
                          </span>
                        </div><!-- /input-group -->
                      </form>
                  </div>

                </div>
                <br />

                <div v-for="(platform_data, platform_type) in platform_types_filtered">

                  <h5 style="text-transform: capitalize"><i class="mdi mdi-chevron-right" ></i> {{platform_type}}</h5>
                  <div class="row mb-s">
                      <div class="col-sm-6" v-for="platform in platform_data">

                        <a href="#"  @click.prevent="togglePlatform(platform)" class="platform-btn" :class="{ active: platform.active }" style="">
                            <span><i class="mdi" :class="['mdi-' + platform.platform, platform.platform]"></i> {{platform.label}}</span>
                            <i class="selection mdi " :class="[checked && checked.hasOwnProperty(platform.id) && checked[platform.id] ? 'mdi-check' : 'mdi-close']"></i>
                        </a>

                      </div>
                  </div>
                  </div>



                  </div>

                  <div class="panel-footer clearfix">
                  <div class="pull-right">
                    <button class="btn btn-primary " type="button" @click.prevent="saveGroupAccounts">Save group accounts</button>

                  </div>
                  </div>


              </div>

        </div>

        <div v-else>
          <ul class="nav nav-tabs">
            <li class="active" ><a href="#" style="background: #fff">Social Media Accounts</a></li>
          </ul>

          <div class="panel panel-default" style="border-radius: 0 0 4px 4px">

            <div class="panel-body text-center">
              <div class="row mt-l">
                <div class="col-sm-12">
                  <i class="fa fa-exclamation-triangle fa-3x  mt-" aria-hidden="true"></i>
                </div>
              </div>
              <br />
                  <p><strong>Oops!</strong> No groups or clients found.</p>

                  <div class="row mb-xl">
                  <div class="col-sm-6">
<a href="" @click.prevent="addGroupPopup()" class="btn btn-primary pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add group</a>
</div>
<div class="col-sm-6">
  <a href="/clients/create" class="btn btn-primary pull-left"><i class="fa fa-users" aria-hidden="true"></i> Add client</a>
                  </div>
                  </div>
                  </div>

              </div>

        </div>

            </div>
        </div>








    </div>
</div>

</template>

<script>

export default {
  props: ['post',],
  data: () => ({
      group_name: [],
      groups: [],
      current_group: null,
      social_accounts: [],
      platform_types: [],
      platform_types_filtered: [],
      checked: {},
      query: '',
      adding: false,
      group_query: '',
      group_search: false
  }),
  computed: {
    filteredGroups:function() {
        return this.groups.filter((cust) => {
          return cust.name.toLowerCase().indexOf(this.group_query.toLowerCase())>=0;
        });
    }
  },
  watch: {
    query: function(val, oldVal) {
      this.searchPlatformTypes();
    }
  },
    mounted() {
      this.getSocialAccounts();
      this.getGroups();
    },
    methods: {
        clearGroupSearch() {
          this.group_query = '';
          this.group_search = false;
        },
        togglePlatform(platform) {
          this.checked[parseInt(platform.id)] = !this.checked[platform.id];
          this.checked = Object.assign({}, this.checked);
        },
        saveGroupAccounts() {
          alertify.success('Saving...');
          axios.put(site_url('/groups/' + this.current_group.id), {
              checked: this.checked
            })
            .then( (response) => {
              this.getGroups();
              alertify.success('Saved!');
            })
            .catch( (error) => {
              self.adding = false;
            });
        },
        searchPlatformTypes() {
          if(this.query != "") {
            let results = this.social_accounts.filter((row)=>{
              let fields = (row.label + row.platform + row.username).toLowerCase();
              return fields.includes(this.query.toLowerCase());
            })

            this.platform_types_filtered = {
              'Search results' : results
            };
          } else {
            this.platform_types_filtered = Object.assign({}, this.platform_types);
          }

        },
        setCurrentGroup(id) {
          console.log(id);

          this.current_group = _.find(this.groups,  (o) => o.id == id );
          this.checked = this.current_group.selection?this.current_group.selection:{};
          console.log(this.groups);
        },

        setDefaultGroup() {
          if(this.current_group) {
              //this.setCurrentGroup(this.current_group.id);
              //does the current group still exist?
              this.current_group = _.find(this.groups,  (o) => o.id == this.current_group.id );
              if(!this.current_group) {
                this.current_group = _.find(this.groups, (o) => { return true } );
                if(this.current_group)
                  this.checked = this.current_group.selection?this.current_group.selection:{};
              }
          } else {
            this.current_group = _.find(this.groups, (o) => { return true } );
            if(this.current_group)
              this.checked = this.current_group.selection?this.current_group.selection:{};
          }

        },
        getSocialAccounts() {
          console.log('getSocialAccounts');

          axios.get(site_url('/social-accounts-list'))
            .then( (response) => {
              console.log(response);
              this.social_accounts = response.data.social_accounts;
              this.platform_types = response.data.platform_types;
              this.platform_types_filtered = response.data.platform_types;
            })
            .catch(function (error) {
              console.log(error);
            });

       },
        getGroups() {

          axios.get(site_url('/groups-list'))
            .then( (response) => {
              console.log(response);
              this.groups = response.data.groups;
              this.setDefaultGroup();
            })
            .catch(function (error) {
              console.log(error);
            });

       },
      removeGroup(group) {

        let message = 'Are you sure you want to delete group: ' + group.name;
        if(group.user) {
          message = 'You will be deleting the client "'+group.user.name+'" and all accounts associated with it. Are you sure you want to continue?';
        }

        alertify.okBtn("YES").cancelBtn("No").confirm(message, () =>{
          alertify.success('Deleting');
              axios.delete(site_url('/groups/' + group.id))
                .then( (response) => {
                  console.log(response);
                  this.groups = response.data.groups;
                  this.setDefaultGroup();
                  alertify.success('Deleted!');
                })
                .catch(function (error) {
                  console.log(error);
                });

        }, () => {

        });

      },
      addGroupPopup() {
        alertify
          .defaultValue("Enter a group name")
          .prompt("Add a new group",
            (val, ev) => {
              // The click event is in the event variable, so you can use it here.
              ev.preventDefault();
              // The value entered is availble in the val variable.
              this.group_name = val;
              this.addGroup();
            }, (ev) => {
              // The click event is in the event variable, so you can use it here.
              ev.preventDefault();
              //alertify.error("You've clicked Cancel");
            }
          );
      },
      addGroup() {
          this.adding = true;
          let self = this;
          axios.post(site_url('/groups'), {
              name: this.group_name
            })
            .then( (response) => {
              console.log('response', response);
              this.groups = response.data.groups;
              this.group_name = "";
              this.adding = false;
              this.setCurrentGroup(response.data.group.id);
              alertify.success('Added new Group!');
            })
            .catch( (error) => {
              self.adding = false;
              self.group_name = "";
              if( error.response ){
                  let msg = _.find(error.response.data, function (o) { return true });
                  alertify.alert(msg[0]);
              }
            });

       }
   }
}

</script>
