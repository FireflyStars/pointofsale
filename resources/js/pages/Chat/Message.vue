<template>
    <div class="message" :class="{ 'message-out': direction == 'Out'}">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-user-profile" class="avatar avatar-responsive">
            <img class="avatar-img" :src="userImage" alt="">
        </a>
        <div class="message-inner">
            <div class="message-body">
                <div class="message-content">
                    <div class="message-text">
                        <p>{{ message }}</p>
                    </div>

                    <!-- Dropdown -->
                    <div class="message-action">
                        <div class="dropdown">
                            <a class="icon text-muted" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <span class="me-auto">Edit</span>
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <span class="me-auto">Reply</span>
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-danger" href="#">
                                        <span class="me-auto">Delete</span>
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="message-footer">
                <span class="extra-small text-muted">{{ time }}</span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:{
        direction: {
            type: String,
            default: 'In'
        }, 
        message: {
            type: String,
            required: true
        },
        userImage: {
            type: String,
            required: true
        }, 
        time: {
            type: String,
            required: true
        }, 
        messageType: {
            type: String,
        }
    },
    setup(){

    }
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
.avatar-sm {
    height: 34px;
    width: 34px;
}
.avatar, .avatar .avatar-img, .avatar .avatar-text {
    border-radius: 50%;
}
.avatar .avatar-img {
    max-width: 100%;
    height: auto;
}
.message {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: end;
    -ms-flex-align: end;
    align-items: flex-end;
}
.message+.message {
    margin-top: 1rem;
}
@media (min-width: 992px){
    .message+.message {
        margin-top: 1.5rem;
    }
}
.message.message-out {
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    -ms-flex-direction: row-reverse;
    flex-direction: row-reverse;
}
.message-inner {
    max-width: 100%;
    min-width: 0;
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;    
    margin-left: 0.5rem;
}
@media (min-width: 1440px){
    .message-inner {
        max-width: 45%;
    }
}

@media (min-width: 1200px){
    .message-inner {
        max-width: 50%;
    }
}
@media (min-width: 992px){
    .message-inner {
        max-width: 70%;
    }
}
@media (min-width: 768px){
    .message-inner {
        margin-left: 1rem;
    }
}
.message-out .message-inner {
    margin-left: 0;
    margin-right: 0.5rem;
}
@media (min-width: 768px){
    .message-out .message-inner {
        margin-right: 1rem;
    }
}
.message-content {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
}
.message-content+.message-content {
    margin-top: 0.75rem;
}
.message-out .message-content {
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    -ms-flex-direction: row-reverse;
    flex-direction: row-reverse;
}
.message-content:not(:last-child) .message-text {
    border-bottom-left-radius: 0.25rem;
}
.message-text {
    background: #f6f9fb;
    border-radius: 0.6rem;
    padding: 1rem 1.25rem;
    color: #95aac9;
    overflow: hidden;
    margin-right: 0.25rem;
    font-size: 15px;
}
.message-out .message-gallery, .message-out .message-text {
    margin-left: 0.25rem;
    margin-right: 0;
}
.message-out .message-text {
    background: #2787f5;
    border-radius: 0.6rem;
    color: #fff;
}
.message-out .message-content:not(:last-child) .message-text {
    border-bottom-left-radius: 0.6rem;
    border-bottom-right-radius: 0.25rem;
}
.message-footer {
    line-height: 1.3;
    margin-top: 0.25rem;
}
.message-out .message-footer {
    text-align: right;
}
.extra-small {
    font-size: .665em;
}
.text-muted {
    --bs-text-opacity: 1;
    color: #bfccdf!important;
}
</style>