<template>
  <div>
  <div style="position: relative">
    <textarea class="form-control" ref="text_widget" v-model="message" rows="6" placeholder="The message you want to share..." @focus="focusTextArea" v-on-clickaway="unfocusTextArea"></textarea>
    <div style="position: absolute; right: 20px; bottom: 5px; display: block; width: 20px; height: 20px; text-align: right">
      {{message && message.length}}
    </div>
  </div>
  <div id="preview_pane" v-if="showPreview" :style="{ top: previewTop }">
  <div class="arrow_box">

                          <div class="ui card">
                            <div class="content">
                              <div class="right floated meta"><a href="#" id="preview_pane_close" @click.prevent="closePreview"><i class="mdi mdi-close"></i></a></div>
                              <div class="meta">Quick Preview</div>

                            <div class="description" v-html="previewText" style="max-height: 200px; overflow-y: auto; display: block;"></div>
      </div>

      <div class="content text-center" v-if="previewLoading">
        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>

        </div>

        <div v-if="files && files.constructor === Array && files.length > 0 && !preview.cover && !previewLoading">
              <img v-if="files[0].response.media_type == 'image'" style="width: 100%;height: 100%;" :src="'/uploads/' + files[0].response.path | site_url">

              <video v-if="files[0].response.media_type == 'video'" width="100%" video="100%" style="width:100%, height:100%" autoplay muted>
                <source :src="'/uploads/' + files[0].response.path | site_url" type="video/mp4">
              Your browser does not support the video tag.
              </video>

        </div>

                            <div class="content" v-if="preview.cover" style="border-top: none">

                              <div class="ui card">
                                <div class="image">
                                  <img :src="preview.cover">
                                </div>
                                <div class="content">
                                  <div class="header" v-html="preview.title"></div>
                                  <div class="description" v-html="preview.description"></div>
                                  <div class="meta">
                                    <span class="date" v-html="preview.url"></span>
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

import { mixin as clickaway } from 'vue-clickaway';

function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}

function findHashtags(searchText) {
    var regexp = /(\s|^)\#\w\w+\b/gm
    let result = searchText.match(regexp);
    if (result) {
        result = result.map(function(s){ return s.trim();});
        return result;
    } else {
        return [];
    }
}

function findMentions(searchText) {
    var regexp = /(\s|^)\@\w\w+\b/gm
    let result = searchText.match(regexp);
    if (result) {
        result = result.map(function(s){ return s.trim();});
        return result;
    } else {
        return [];
    }
}

function getLinkFromString(message) {
  var getUrls = require('get-urls');
  let urls = getUrls(message);
  if(urls.length > 0) {
    return urls[0];
  }
  return false;
}

export default {
    mixins: [ clickaway ],
    props: ['myMessage', 'files'],
    data: () => ({
        message: "",
        showPreview: false,
        previewLoading: false,
        previewText: "",
        previewTop: 0,
        twitterLimitShown: false,
        preview: {}
    }),
    watch: {
      myMessage: function (newMessage, oldMessage) {
        this.message = this.myMessage;
      },
      message: function (newMessage, oldMessage) {
        if(newMessage == null) {
          return;
        }
        let replacedText = new String(newMessage);
        replacedText = replacedText.replace(/(#\S+)/g, '<span class="text-primary">$1</span>');
        replacedText = replacedText.replace(/(@\S+)/g, '<span class="text-primary">$1</span>');
        replacedText = linkify(replacedText);
        replacedText = replacedText.replace(/(?:\r\n|\r|\n)/g, '<br />');
        this.previewText = replacedText;

        let newUrl = getLinkFromString(newMessage);
        let oldUrl = getLinkFromString(oldMessage);
        if(newUrl != oldUrl) {
          this.getLinkPreview(newUrl);
        }
        this.focusTextArea();

        if(this.message.length > 280 && !this.twitterLimitShown) {
          this.twitterLimitShown = true;
          alertify.error("Notice: Twitter's limit is 280 characters.");
        }

        //console.log('update:myMessage', newMessage);
        this.$emit('update:myMessage', newMessage);
      }
    },
    mounted() {
      this.message = this.myMessage;
    },
    methods: {

        getHashtagsMentionsAndUrls: function(message) {
          let hashtags = findHashtags(message);
          let mentions = findMentions(message);

          let url = getLinkFromString(message);
          let size = hashtags.length + mentions.length + ((url)?1:0);
console.log('this.files', this.files, Boolean(this.files));
          if(this.files) {
            size += this.files.length;
          }

          return size;
        },

        focusTextArea: function() {

          if(!this.message) {
            return;
          }
          //if hashtag, mention or url then show preview
          let count = this.getHashtagsMentionsAndUrls(this.message);
          console.log(count);
          if(count > 0) {
            if($(this.$refs.text_widget).is(':focus')) {
              this.showPreview = true;
            }
            console.log('text_widget', this.$refs.text_widget);

            var rect = this.$refs.text_widget.getBoundingClientRect();
            console.log(rect.top, rect.right, rect.bottom, rect.left);
            this.previewTop = -16 + 'px';
          } else {
            this.showPreview = false;
          }

        },

        closePreview: function(element) {
          this.showPreview = false;
        },

        unfocusTextArea: function(element) {
          if(document.getElementById('preview_pane') == null) {
            return false;
          }

          var originalElement = element.srcElement || element.originalTarget;
          if (document.getElementById('preview_pane_close').contains(originalElement)) {
            console.log('element', element);
            return false;
          }

          if (document.getElementById('preview_pane').contains(originalElement)) {
              this.showPreview = true;
          } else {
            this.showPreview = false;
          }
        },

        getLinkPreview: function(url) {

          if(url) {
            this.previewLoading = true;
            axios.post(site_url('/link-preview'), {
                    url: url
                })
                .then((response) => {
                    this.preview = response.data.preview;
                    this.previewLoading = false;
                })
                .catch((error) => {
                    this.previewLoading = false;
                });
          } else {
            this.preview = {};
          }

        }

    }
}

</script>
