<template>

<div>
    <div class="panel with-nav-tabs  panel-default " data-mh style="margin-bottom: 0">
        <div class="panel-heading" style="background: #f5f8fa">
            <ul class="nav nav-tabs">
                <li :class="{active: tab == 'groups'}" v-if="this.default_groups.length > 0"><a href="#" @click.prevent="setTab('groups')" >Clients</a></li>
                <li :class="{active: tab == 'accounts'}"><a href="#"  @click.prevent="setTab('accounts')" >Accounts</a></li>
            </ul>
        </div>

        <div class="panel-body" v-if="tab == 'groups'">
            <div class="input-group  input-group-sm">
                <input type="text" class="form-control" v-model="group_query" placeholder="Search clients...">
                <span class="input-group-btn">
                <button class="btn btn-default" type="button">Go!</button>
              </span>
            </div>
            <!-- /input-group -->
            <br />
            <div class="scroll" style="height: 225px; ">

                <a href="#" v-for="(group, index) in groups" @click.prevent="toggleGroup(index)" class="platform-btn" :class="{ active: group.active }" style="">
                    <span><i class="mdi" :class="['mdi-' + group.platform]"></i> {{group.name}}</span>
                    <i class="selection mdi " :class="[group.active ? 'mdi-check' : 'mdi-close']"></i>
                </a>

            </div>
            <br />
            <div class="text-center">
              <small class="text-center">Select the client(s) you want to post for.</small>
            </div>
        </div>

        <div class="panel-body" v-if="tab == 'accounts'">

            <div class="input-group  input-group-sm">
                <input type="text" class="form-control" v-model="account_query" placeholder="Search accounts...">
                <span class="input-group-btn">
                <button class="btn btn-default" type="button">Go!</button>
              </span>
            </div>
            <!-- /input-group -->
            <br />
            <div class="scroll" style="height: 225px; ">

                <a href="#" v-for="(social_account, index) in social_accounts" @click.prevent="toggleSocialAccount(index)" class="platform-btn" :class="{ active: social_account.active, hidden: (account_query && !social_account.found) }" style="">
                    <span><i class="mdi" :class="['mdi-' + social_account.platform]"></i> <span class="truncate">{{social_account.label}}</span></span>
                    <i class="selection mdi " :class="[social_account.active ? 'mdi-check' : 'mdi-close']"></i>
                </a>

            </div>

            <br />
            <div class="text-center">
              <small class="text-center">Select the social media accounts to post to.</small>
            </div>

        </div>

    </div>
</div>

</template>

<script>

export default {
    props: ['myGroups', 'myAccounts', 'defaultGroups', 'defaultAccounts'],
    data: () => ({
        default_groups: [],
        groups: [],
        groups_set: false,
        accounts_set: false,
        default_social_accounts: [],
        social_accounts: [],
        group_query: '',
        account_query: '',
        tab: 'groups'
    }),
    watch: {
        myAccounts: function(val, oldVal) {
            console.log('---myAccounts', val, oldVal);
            this.social_accounts = this.myAccounts;
        },
        group_query: function(val, oldVal) {
            this.searchGroups();
        },
        account_query: function(val, oldVal) {
            this.searchSocialAccounts();
        },
        social_accounts: function(val, oldVal) {
            console.log('---social_accounts', val, oldVal);
            let group_ids = this.groups.filter(value => value.active).map(value => value.id);
            this.$emit('update', [this.social_accounts, group_ids]);
        }
    },
    mounted() {
        console.log('mounted ACCOUNTSELECTOR', this.myGroups, this.myAccounts, this.defaultGroups, this.defaultAccounts);
        this.getSocialAccounts();
        this.getGroups();
    },
    methods: {
        setTab(tab) {
            this.tab = tab;
        },
        toggleGroup(index) {
          this.groups[index].active = !this.groups[index].active;
          this.$set(this.groups, index, this.groups[index]);

          let selection = this.groups.reduce((data, group)=> {
            if(!group.active || !group.selection)
              return data;

            for (let [key, value] of Object.entries(group.selection)) {
               if(value && !data.includes(key))
                  data.push(parseInt(key))
            }

            return data;
          }, []);

          this.social_accounts.map((social_account, index) => {
            this.social_accounts[index].active = selection.includes(social_account.id);
            this.$set(this.social_accounts, index, this.social_accounts[index])
          });

        },
        toggleSocialAccount(index) {
          this.social_accounts[index].active = !this.social_accounts[index].active;
          this.$set(this.social_accounts, index, this.social_accounts[index])
        },
        togglePlatform(platform) {
          this.checked[parseInt(platform.id)] = !this.checked[platform.id];
          this.checked = Object.assign({}, this.checked);
        },
            searchGroups() {
                if (this.group_query != "") {
                    this.groups = this.default_groups.filter((row) => {
                        let fields = (row.name).toLowerCase();
                        return fields.includes(this.group_query.toLowerCase());
                    })
                } else {
                    this.groups = this.default_groups;
                }


                if (this.myGroups !== undefined && !this.groups_set) {
                    this.groups.map((group) => {
                      group.active = this.myGroups.includes(group.id);
                    });
                    this.groups_set = true;
                    let group_ids = this.groups.filter(value => value.active).map(value => value.id)
                    this.$emit('update', [this.social_accounts, group_ids])
                }

            },
            searchSocialAccounts() {
                if (this.account_query != "") {
                    this.social_accounts.map((row) => {
                        let fields = (row.label + row.platform + row.username).toLowerCase();
                        row.found = fields.includes(this.account_query.toLowerCase());
                    });
                } else {
                    this.social_accounts = this.default_social_accounts;
                }
                
                if (this.myAccounts !== undefined && !this.accounts_set) {

                  let accounts = this.myAccounts.filter((account) => {
                    return account.active;
                  }).map(value => value.id);

                  console.log('myAccounts', accounts);

                    this.social_accounts.map((social_account) => {
                      social_account.active = accounts.includes(social_account.id);
                    });
                    this.accounts_set = true;
                    let group_ids = this.groups.filter(value => value.active).map(value => value.id)
                    this.$emit('update', [this.social_accounts, group_ids])

                }

            },
            getSocialAccounts() {
                this.default_social_accounts = this.defaultAccounts;
                this.searchSocialAccounts();
                return;
                axios.get(site_url('/social-accounts-list'))
                    .then((response) => {
                        this.default_social_accounts = response.data.social_accounts;
                        this.searchSocialAccounts();
                    })
                    .catch(function(error) {
                        console.log(error);
                    });

            },
            getGroups() {
              this.default_groups = this.defaultGroups;
              this.searchGroups();
              if(this.default_groups.length == 0) {
                this.setTab('accounts');
              }
              return;
                axios.get('/groups-list')
                    .then((response) => {
                        this.default_groups = response.data.groups;
                        this.searchGroups();
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
    }
}

</script>
