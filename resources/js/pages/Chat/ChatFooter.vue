<template>
    <div class="chat-footer pb-3 pb-lg-7 position-absolute bottom-0 start-0">
        <!-- Chat: Files -->
        <div class="dz-preview bg-custom-gray" id="dz-preview-row">
        </div>
        <!-- Chat: Files -->

        <!-- Chat: Form -->
        <form class="chat-form rounded-pill bg-custom-gray">
            <div class="row align-items-center gx-0">
                <div class="col-auto">
                    <a href="#" class="btn btn-icon btn-link text-body rounded-circle dz-clickable" id="dz-btn">
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> -->
                    </a>
                </div>

                <div class="col">
                    <div class="input-group">
                        <textarea class="form-control px-0" placeholder="Type your message..." rows="1" data-emoji-input="" data-autosize="true" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 47px;"></textarea>

                        <a href="#" class="input-group-text text-body pe-0">
                            <span class="icon icon-lg">
                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smile"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg> -->
                            </span>
                        </a>
                    </div>
                </div>

                <div class="col-auto">
                    <button class="btn btn-icon btn-primary rounded-circle ms-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </div>
        </form>
        <!-- Chat: Form -->
    </div>    
</template>
<style scoped>
.chat-footer {
    width: calc(100% + (2rem));
    margin-left: -1rem;
    z-index: 1;
}
.chat-form {
    position: relative;
    padding: 0.5rem;
}
.bg-custom-gray{
    background-color: #ebf1f7 !important;
}
.input-group>.form-control, .input-group>.form-select {
    position: relative;
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
}
.form-control {
    display: block;
    width: 100%;
    padding: 0.59375rem 1.125rem;
    font-size: .9375rem;
    font-weight: 400;
    line-height: 1.6;
    color: #a0b3ce;
    background-color: #ebf1f7;
    background-clip: padding-box;
    border: 2px solid #ebf1f7;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0.6rem;
}
.btn:active,
.btn:focus,
.form-control:active,
.form-control:focus{
    outline: none;
    box-shadow: none;
}
textarea.form-control {
    min-height: calc(1.6em + 1.1875rem + 4px);
}
.btn-icon {
    line-height: 0;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 0;
    height: calc(1.6em + 1.1875rem + 4px);
    width: calc(1.6em + 1.1875rem + 4px);
}
.btn-link {
    font-weight: 400;
    color: #2787f5;
    text-decoration: underline;
}
.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    white-space: nowrap;
    background-color: #ebf1f7;
    border: 2px solid #ebf1f7;
    border-radius: 0.6rem;
}
.text-body {
    --bs-text-opacity: 1;
    color:  rgb(33, 37, 41)!important;
}
</style>