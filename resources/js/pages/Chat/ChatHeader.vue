<template>
    <div class="chat-header border-bottom py-4 py-lg-7">
        <div class="row align-items-center">
            <!-- Mobile: close -->
            <div class="col-2 d-xl-none">
                <a class="icon icon-lg text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
            </div>
            <!-- Mobile: close -->

            <!-- Content -->
            <div class="col-8 col-xl-12">
                <div class="row align-items-center text-center text-xl-start">
                    <!-- Title -->
                    <div class="col-12 col-xl-6">
                        <div class="row align-items-center gx-3">
                            <div class="col-auto">
                                <div class="avatar avatar-online d-none d-xl-inline-block">
                                    <img class="avatar-img" src="../../images/user-img2.jpg" alt="">
                                </div>
                            </div>

                            <div class="col overflow-hidden">
                                <h5 class="text-truncate">Ollie Chandler</h5>
                                <p class="text-truncate line-clamp">is typing<span class="typing-dots"><span>.</span><span>.</span><span>.</span></span></p>
                            </div>
                        </div>
                    </div>
                    <!-- Title -->
                </div>
            </div>
            <!-- Content -->

            <!-- Mobile: more -->
            <div class="col-2 d-xl-none text-end">
                <a href="#" class="icon icon-lg text-muted" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-more" aria-controls="offcanvas-more">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                </a>
            </div>
            <!-- Mobile: more -->

        </div>
    </div>
</template>
<script>
export default {
    
}
</script>
<style scoped>
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
    background:#4dd965;
}
.avatar-offline::before {
    background: #ffba00;
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
</style>