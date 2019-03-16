<template>
      <form v-on:submit.prevent="onSubmit" >

                  <div class="row">

                    <div class="col-md-4">
              <p class="text-muted mb-xxs">Days:</p>

              <div v-for="day in days">
              <div class="pretty">
                <input type="checkbox" v-model="day.active"/>
                <label style="text-transform: capitalize"><i class="mdi mdi-check"></i> {{day.name}}</label>
              </div>
              </div>
              </div>
  <div class="col-md-7">
    <p class="text-muted mb-xxs">Times:</p>

              <table class="table">
  <tbody>
      <tr  v-for="(time, index) in times">
          <th scope="row"><p class=" form-control-static  input-sm"><i style="font-size: 18px" class="mdi mdi-timer light"></i></p></th>
          <td>
            <select class="form-control input-sm" v-model="time.hour">
              <option :value="n" v-for="n in 24">{{("00" + n).slice(-2)}}</option>
            </select>
          </td>
          <td class="text-center"><p class=" form-control-static  input-sm">:</p></td>
          <td>
            <select class="form-control input-sm"  v-model="time.minute">
              <option :value="n-1" v-for="n in 60">{{("00" + (n-1)).slice(-2)}}</option>
            </select>
          </td>
          <td><p class=" form-control-static  input-sm"><a href="" v-on:click.prevent="removeTime(index)"><i style="font-size: 18px" class="mdi mdi-close light"></i></a></p></td>
      </tr>
      <tr>
          <th scope="row"></th>
          <td></td>
          <td></td>
          <td></td>
          <td><button type="button" v-on:click.prevent="addTime()" class="btn btn-link btn-xs pull-right"><i class="mdi mdi-plus"></i> Add new time</button></td>
      </tr>
  </tbody>
</table>
</div>

</div>
</div>
<div class="row ">



<div class="col-md-12">


<button type="submit" class="btn btn-primary">Save posting times</button>

</div>
</div>


          </div>

        </form>
</template>

<script>

    export default {
        props: ['myDays', 'myTimes', 'id'],
        data: () => ({
          saving: false,
          days: [
            {name: 'sunday', active: true},
            {name: 'monday', active: true},
            {name: 'tuesday', active: true},
            {name: 'wednesday', active: true},
            {name: 'thursday', active: true},
            {name: 'friday', active: true},
            {name: 'saturday', active: true},
          ],
          times: [
            {hour: 8, minute: 5},
          ]
        }),
        mounted() {
            console.log('Component mounted.');
            this.days = this.myDays;
            this.times = this.myTimes;
        },
        methods: {
          removeTime(index) {
            this.times.splice(index, 1);
          },
          addTime() {
            var date = new Date;
            this.times.push({hour: date.getHours(), minute: date.getMinutes()});
          },
          onSubmit() {
            this.saving = true;
            axios.put(site_url('/schedule/' + this.id), {
                days: this.days,
                times: this.times
              })
              .then( (response) => {
                console.log(response);
                this.saving = false;
                this.days = response.data.days;
                this.times = response.data.times;
                alertify.log("Successfully saved!");
                //window.location.href='/social-accounts';
              })
              .catch( (error)=>{
                this.saving = false;
                alertify.alert(error);
              });

          }
        }
    }
</script>
