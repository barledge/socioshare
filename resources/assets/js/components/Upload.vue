<template>

  <div>
  <div class="row">
    <div :class="wide">
                <div class="row">
  <div class="col-sm-6">
    <file-upload :size="file_max_size" extensions="jpg,jpeg,gif,png,webp,avi,flv,wmv,mov,mp4" :multiple="true" ref="upload"  @input-file="inputFile" v-model="files" :post-action="'/api/upload' | site_url"   class="text-muted"><i class="mdi mdi-camera"></i> Add photo or video</file-upload>
  </div>
  <div class="col-sm-6 scheduled_at">
    <a href="" ref="datetimepicker" v-on:click.prevent="openDatePicker()" class="text-muted pull-right ml-xxs"><i class="fa fa-calendar-o"></i></a>
    <input class="text-primary pull-right text-right " style="border: none" v-model="scheduled_at"/>
  </div>
  </div>
  </div>
  </div>

      <div class="row mb-xxs">
        <div class="col-sm-6  mb-xs" v-for="(file, index) in files">


          <div style="background: #f4f4f4; height: 50px;">
            <div style="display: flex;">
            <div class="" style="float: left; width: 50px; height: 50px; text-align: center; ">
              <i v-if="!file.success" style="line-height: 50px;" class="fa fa-2x fa-circle-o-notch fa-spin" aria-hidden="true"></i>
              <img v-if="file.success" :src="'/uploads/' + file.response.thumb | site_url" style="width: 50px;" />
            </div>
            <div class="" style="float: left;    width: calc(100% - 50px);">
              <div style="padding: 5px; position: relative">
                <a href="#" v-on:click.prevent="removeFile(index)" style="position: absolute; top: 3px; right: 5px; color: #666"><i class="mdi mdi-close"></i></a>
                <p class="mb-0 pl-xxs" style="display: flex;    width: 90%;"><small class="trim-text">{{file.name}}</small></p>
                <p class="text-muted mb-0 pl-xxs"><small>{{formatBytes(file.size)}}</small></p>
              </div>
            </div>

          </div>

            <div v-if="!file.success" style="background: #f4f4f4; height: 4px;">
              <div style="background: #2780E3; height: 4px; width: 0%;" :style="{ width: file.progress + '%' }">

              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
      </div>
</template>

<script>
var FileUpload = require('vue-upload-component');

    export default {
        props: ['myFiles', 'scheduled', 'wide'],
        data: () => ({
          files: [],
          scheduled_at: null,
          file_max_size: parseInt(document.head.querySelector("[name=file-max-size]").content)
        }),
        beforeMount() {
          console.log('upload beforeMount');
          console.log('beforeMount', this.myFiles.length);
          //this.files = (this.myFiles)?this.myFiles:[];
        },
        watch: {
          files: function (val, oldVal) {
            console.log('emit', this.files, val, oldVal, (typeof oldVal ));
            if(oldVal !== undefined) {
              this.$emit('update:myFiles', this.files)
            }
          },
          scheduled_at: function (val, oldVal) {
            console.log('update:scheduled', val, oldVal, this.scheduled_at);

            this.$emit('update:scheduled', val)
          },
          scheduled: function (val, oldVal) {
            this.scheduled_at= this.scheduled;
            console.log('scheduled***', val, oldVal, this.scheduled_at);

          },
          myFiles: function (val) {
            console.log('myFiles', val, this.myFiles);
            console.log('myFiles', this.myFiles.length);

            this.files= (this.myFiles)?this.myFiles:[];
          }
       },
       components: {
          FileUpload
        },
        mounted() {
          console.log('upload mounted');
          this.scheduled_at = this.scheduled;
          this.files = (this.myFiles)?this.myFiles:[];
          this.launchPicker();
          //this.$refs.upload.active = true
        },

       methods: {
         launchPicker() {
           console.log('launchPicker', this.$refs.datetimepicker, $(this.$refs.datetimepicker));
           setTimeout(() => {
             $(this.$refs.datetimepicker).datetimepicker({
               format:'Y-m-d H:i',
               minDate: 0,
               onChangeDateTime: (dp,$input) => {
                   this.scheduled_at = $input.val();
                 }
             });
           }, 500);
         },
         inputFile(newFile, oldFile) {
               if (newFile && oldFile && newFile.success && !oldFile.success) {

                   let myFile = this.files.find((row) => {
                     return row.id == newFile.id
                   });

               }

               if (newFile && oldFile && newFile.error && !oldFile.error) {
                   if(newFile.response.error) {
                     alertify.alert("Oops! There was an error uploading your file.<br /><br />\n\n" + newFile.response.error);
                   } else if(newFile.error && newFile.error == 'size') {
                     alertify.alert("Your file must be a maximum of " + ( (this.file_max_size/(1024*1024)).toFixed(2) ) + 'MB');
                   } else {
                     alertify.alert("Oops! There was an error uploading your file.");
                   }


                   let index = this.files.findIndex((value) => {
                     return value.id == newFile.id;
                   });

                   setTimeout(()=>{
                     this.files.splice(index, 1);
                   }, 1000);

               }

               if (!newFile && oldFile) {
                   console.log('delete')
               }

               if (newFile && !oldFile) {
                   console.log('Add to')
               }
               this.$refs.upload.active = true
         },
         openDatePicker() {
             $(this.$refs.datetimepicker).datetimepicker('show');
         },
          removeFile(index) {
            this.files.splice(index, 1);
          },
          formatBytes(bytes, decimals) {
             if(bytes == 0) return '0 Bytes';
             var k = 1000,
                 dm = decimals || 2,
                 sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                 i = Math.floor(Math.log(bytes) / Math.log(k));
             return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
          },
      }
    }
</script>
