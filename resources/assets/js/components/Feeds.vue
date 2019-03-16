<template>

      <form v-on:submit.prevent="onSubmit" >

      <div class="row" v-for="(feed, index) in feeds">

        <div class="col-md-9">

        <div class="form-group" :class="{ 'has-error': !feed.is_valid && (feed.url != '' && feed.url != null )}">
          <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">{{ index+1 }}.</span>
            <input type="text" class="form-control" placeholder="http://"  v-on:keydown="validateUrl(feed)" v-model="feed.url" aria-describedby="basic-addon1">
            <span v-if="!feed.is_valid && (feed.url != '' && feed.url != null )" class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>
          </div>
        </div>
        </div>
        <div class="col-md-3">


        </div>

        </div>

        <div class="row">

        <div class="col-md-12">


                    <button type="submit" v-if="!saving" class="btn btn-primary">Save feeds</button>
                    <button type="button" v-if="saving" class="btn btn-primary"><i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i> Saving...</button>

    </div>
    </div>

        </form>
</template>

<script>
    export default {
        props: ['items'],
        data: () => ({
          feeds: this.items,
          saving: false
        }),
        mounted() {
          if(this.items.length < 5) {
            this.feeds = [
              {url: '', twitter: true, facebook: true, instagram: true},
              {url: '', twitter: true, facebook: true, instagram: true},
              {url: '', twitter: true, facebook: true, instagram: true},
              {url: '', twitter: true, facebook: true, instagram: true},
              {url: '', twitter: true, facebook: true, instagram: true},
            ];
          } else {
            this.feeds = this.items;
          }
            console.log('Component mounted.', this.feeds)
        },
        methods: {
          validateUrl(feed) {
            feed.is_valid = true;
          },
          onSubmit() {
            this.saving = true;
            console.log(this.feeds);
            axios.post('/feeds', {
                feeds: this.feeds
              })
              .then( (response) => {
                console.log(response);
                this.feeds = response.data.feeds;
                this.saving = false;
                alertify.log("Successfully saved!");
              })
              .catch( (error) => {
                this.saving = false;
                alertify.alert(error);
              });

          }
        }
    }
</script>
