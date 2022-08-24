<template>
    <!-- Card -->
    <a href="javascript:;" class="card border-0 text-reset" @click="selectUserChannel" :class="{'active': active }">
        <div class="card-body">
            <div class="row gx-3">
                <div class="col-auto">
                    <div class="avatar" :class="{ 'avatar-online': online, 'avatar-offline' : !online }">
                        <img :src="imgUrl" alt="" class="avatar-img">
                    </div>
                </div>

                <div class="col">
                    <div class="d-flex align-items-center mb-2">
                        <h5 class="me-auto mb-0 fw-bold">{{ name }}</h5>
                        <span class="text-muted extra-small ms-2">08:45 PM</span>
                    </div>

                    <div class="d-flex align-items-center" v-if="isTyping == false">
                        <div class="line-clamp me-auto">
                            {{ lastMessage }}
                        </div>
                        <div class="badge rounded-circle bg-primary ms-5" v-show="unReadCnt">
                            <span>3</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" v-else>
                        <div class="line-clamp me-auto">
                            is typing<span class="typing-dots"><span>.</span><span>.</span><span>.</span></span>
                        </div>
                    </div>                    
                </div>
            </div>
        </div><!-- .card-body -->
    </a>
</template>
<script>
import { ref } from 'vue';
export default {
    props:{
        id: {
            type: Number,
            required: true,
        },
        name: {
            type: String,
            default: '',
        },
        isTyping: {
            type: Boolean,
            default: false,
        },
        imgUrl: {
            type: String,
            default: '../../images/user-img.jpg',
        },
        lastMessage: {
            type: String,
            default: 'Hello! Yeah, I\'m going to meet friend of mine at the departments stores now.'
        },
        unReadCnt: {
            type: Number,
            default: 1,
        },
        online: {
            type: Boolean,
            default: false,
        },
    },
    setup(props){
        const active = ref(false);
        const selectUserChannel = ()=>{
            active.value = !active.value;
        }
        return{
            active,
            selectUserChannel
        }
    }
}
</script>
<style scoped>
a {
    -webkit-transition: color .1s,opacity .1s ease-in-out;
    transition: color .1s,opacity .1s ease-in-out;
    text-decoration: none;
}
.avatar {
    position: relative;
    display: flex;    
    display: -webkit-box;
    display: -ms-flexbox;
    height: 44px;
    width: 44px;
}
.avatar, .avatar .avatar-img, .avatar .avatar-text {
    border-radius: 50%;
}
.avatar .avatar-img {
    max-width: 100%;
    height: auto;
}
.avatar-offline .avatar-img, .avatar-offline .avatar-text, .avatar-online .avatar-img, .avatar-online .avatar-text {
    -webkit-mask-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0i0KHQu9C+0LlfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA0NiA0NiIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNDYgNDYiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPHBhdGggZmlsbD0iI0ZGMDAwMCIgZD0iTTM5LjUsMTNjLTMuNTg5ODQzOCwwLTYuNS0yLjkxMDE1NjMtNi41LTYuNVMzNS45MTAxNTYzLDAsMzkuNSwwSDB2NDZoNDZWNi41DQoJCUM0NiwxMC4wODk4NDM4LDQzLjA4OTg0MzgsMTMsMzkuNSwxM3oiLz4NCgk8cGF0aCBmaWxsPSIjRkYwMDAwIiBkPSJNMzkuNSwwQzQzLjA4OTg0MzgsMCw0NiwyLjkxMDE1NjMsNDYsNi41VjBIMzkuNXoiLz4NCjwvZz4NCjwvc3ZnPg0K);
    mask-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0i0KHQu9C+0LlfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA0NiA0NiIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNDYgNDYiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPHBhdGggZmlsbD0iI0ZGMDAwMCIgZD0iTTM5LjUsMTNjLTMuNTg5ODQzOCwwLTYuNS0yLjkxMDE1NjMtNi41LTYuNVMzNS45MTAxNTYzLDAsMzkuNSwwSDB2NDZoNDZWNi41DQoJCUM0NiwxMC4wODk4NDM4LDQzLjA4OTg0MzgsMTMsMzkuNSwxM3oiLz4NCgk8cGF0aCBmaWxsPSIjRkYwMDAwIiBkPSJNMzkuNSwwQzQzLjA4OTg0MzgsMCw0NiwyLjkxMDE1NjMsNDYsNi41VjBIMzkuNXoiLz4NCjwvZz4NCjwvc3ZnPg0K);
    -webkit-mask-size: 100% 100%;
    mask-size: 100% 100%;
}
.avatar-offline::before, .avatar-online::before {
    position: absolute;
    border-radius: 50%;
    display: block;
    content: "";
    height: 18%;
    width: 18%;
    top: 5%;
    right: 5%;
}
.avatar-online::before {
    background: #ffc107;
}
.avatar-offline::before {
    background: #adb5bd;
}
.h5, h5 {
    font-size: .9375rem;
}
.extra-small {
    font-size: .665em;
}
.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: #a0b3ce;
    font-size: 15px;

}
.typing-dots span {
    opacity: 0;
}
.typing-dots span:nth-child(1) {
    -webkit-animation: 1s type-animation infinite .33333s;
    animation: 1s type-animation infinite .33333s;
}
.typing-dots span:nth-child(2) {
    -webkit-animation: 1s type-animation infinite .66666s;
    animation: 1s type-animation infinite .66666s;
}
.typing-dots span:nth-child(3) {
    -webkit-animation: 1s type-animation infinite .99999s;
    animation: 1s type-animation infinite .99999s;
}
@keyframes type-animation {
    50% {
        opacity: 1;
    }   
}
.card.active{
    background-color: rgb(219, 241, 255);
}
</style>