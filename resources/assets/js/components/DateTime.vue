<template>
  <div>
      <a href="" v-on:click.prevent="openDatePicker()" ref="datepicker" class="text-muted pull-right ml-xxs"><i class="fa fa-calendar-o"></i></a>
      <input ref="datepicker_input" class="xxx text-primary pull-right text-right" style="border: none" v-model="myDate" />
  </div>
</template>

<script>
    export default {
        props: ['date'],
        data: () => ({
          myDate: null,
        }),
        watch: {
         myDate: function (newDate) {
           this.$emit('update-date', newDate);
         }
       },
        mounted() {
          console.log(this.date);
          this.myDate = this.date;
            var self = this;
            setTimeout(() => {
              $(this.$refs.datepicker_input).datetimepicker({
                    format: 'Y-m-d H:m',
                    onChangeDateTime: (dp, $input) => {
                        self.$emit('update-date', $input.val());
                    }
              });
            }, 500);
        },
        beforeDestroy: function() {
         //$(this.$refs.datepicker_input).datetimepicker('hide').datetimepicker('destroy');
       },
       methods: {
           openDatePicker() {
             $(this.$refs.datepicker_input).datetimepicker('show');
          },
      }
    }
</script>
