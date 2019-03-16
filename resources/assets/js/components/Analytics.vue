<template>


<div>

    <div class="panel panel-default ">
        <div class="panel-body">

            <div class="row">
              <div class="col-sm-4">
                <select v-model="selectedGroup" class="form-control form-control-sm" @change.prevent="filterGroupsAccounts">
                  <option value="0">All groups/clients</option>
                  <option v-for="group in groups" v-bind:value="group.id">
                    {{ group.name }}
                  </option>
                </select>
              </div>

              <div class="col-sm-8">
                  <select v-model="selectedAccount" class="form-control" @change.prevent="fetchPlatformAnalytics">
                    <option v-for="account in groupAccounts" v-bind:value="account.id">
                      {{ account.name }}
                    </option>
                  </select>
              </div>
            </div>
            <hr />


            <table class="table table-condensed table-striped table-bordered">
                <caption>Displaying all recent posts with realtime stats</caption>
                <thead>
                  <tr>
                    <th class="col-sm-ss">Posted&nbsp;at</th>
                    <th v-if="selectedPlatform == 'instagram'"></th>
                    <th class="col-sm-5">Message</th>
                    <th v-for="header in headers" style="text-transform: capitalize">{{header}}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items">
                    <th><small>{{item.created_date}}</small><br /><small style="color: #BDBDBD">{{item.created_time}}</small></th>
                    <td v-if="selectedPlatform == 'instagram'"><a :href="item.link" target="_blank"><img :src="item.image" style="width: 60px;"/></a></td>
                    <td>{{item.text}} <a :href="item.link" target="_blank"><i style="font-size: xx-small" class="fa fa-external-link" aria-hidden="true"></i></a></td>
                    <td class="text-center" v-for="header in headers"><strong>{{item[header]}}</strong></td>
                  </tr>
                </tbody>
              </table>




        </div>
    </div>

</div>

</template>

<script>
import VueLocalStorage from 'vue-localstorage'
import Datepicker from 'vuejs-datepicker';
var moment = require('moment');

Vue.use(VueLocalStorage)

export default {
    props: ['next', 'groups', 'socialaccounts', 'defaulttab'],
    data: () => ({
        pickerOptions: {
          shortcuts: [{
            text: 'This month',
            onClick(picker) {
                const start = moment().startOf('month').toDate();
                const end   = moment().endOf('month').toDate();
                picker.$emit('pick', [start, end]);
            }
          },
          {
            text: 'Last month',
            onClick(picker) {
                const start = moment().subtract(1,'months').startOf('month').toDate();
                const end   = moment().subtract(1,'months').endOf('month').toDate();
                picker.$emit('pick', [start, end]);
            }
          },
          {
            text: 'This week',
            onClick(picker) {
                const start = moment().startOf('week').toDate();
                const end   = moment().endOf('week').toDate();
                picker.$emit('pick', [start, end]);
            }
          },
          {
            text: 'Last week',
            onClick(picker) {
                const start = moment().subtract(1,'week').startOf('week').toDate();
                const end   = moment().subtract(1,'week').endOf('week').toDate();
                picker.$emit('pick', [start, end]);
            }
          }]
        },
        selectedDateRange: [moment().startOf('month').toDate(), moment().endOf('month').toDate()],
        loading: true,
        selectedGroup: 0,
        selectedAccount: 0,
        selectedPlatform: null,
        groupAccounts: [],
        headers: [],
        items: []
    }),
    components: {
        Datepicker
    },
    beforeMount() {
      this.selectedGroup = this.$localStorage.get('group', 0);
      this.selectedAccount = parseInt(this.$localStorage.get('social_account_id', 0));

      if(!this.selectedAccount && this.socialaccounts.find(() => true)) {
          this.selectedAccount = this.socialaccounts.find(() => true).id;
          this.selectedPlatform = this.socialaccounts.find(() => true).platform_type;
      }

    },
    mounted() {
        console.log('Component mounted.');
        this.fetchPlatformAnalytics();
        this.groupAccounts = this.socialaccounts;
    },
    watch: {
     // whenever question changes, this function will run
     selectedDateRange: function (selectedDateRange) {
       this.fetchPlatformAnalytics();
     }
   },
   methods: {
            selectPlatform(platform) {
                this.selectedPlatform = platform;
                this.fetchPlatformAnalytics();
            },
            filterGroupsAccounts() {

                this.selectedGroup = Number(this.selectedGroup)

                console.log(parseInt(this.selectedGroup));

                if(parseInt(this.selectedGroup) == 0) {
                    this.groupAccounts = this.socialaccounts;
                    if(this.socialaccounts.find(() => true)) {
                        this.selectedAccount = this.socialaccounts.find(() => true).id;
                    }
                    this.fetchPlatformAnalytics();
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

                    if(this.groupAccounts.find(() => true)) {
                        this.selectedAccount = this.groupAccounts.find(() => true).id;
                    }


                }

                this.fetchPlatformAnalytics();

            },
            fetchPlatformAnalytics() {
              console.log(this.groups);

              this.$localStorage.set('group', this.selectedGroup);

              $('#busy').show()

                axios.post('/analytics', {
                        social_account_id: this.selectedAccount
                    })
                    .then((response) => {
                        this.loading = false;
                        this.headers = response.data.headers;
                        this.items = response.data.items;
                        if(this.selectedAccount && this.socialaccounts.find((item) => this.selectedAccount == item.id)) {
                            this.selectedPlatform = this.socialaccounts.find((item) => this.selectedAccount == item.id).platform_type;
                        }
                        $('#busy').hide()
                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                        //alertify.alert(error);
                        $('#busy').hide()
                    });
            }
    }
}

</script>
